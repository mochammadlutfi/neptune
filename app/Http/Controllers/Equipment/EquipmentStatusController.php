<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\EquipmentStatusRequest;
use App\Http\Resources\Equipment\EquipmentStatusResource;
use App\Models\Equipment\EquipmentStatus;
use App\Models\Master\Equipment;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class EquipmentStatusController extends Controller
{
    use ActivityLogger;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = EquipmentStatus::with(['vessel', 'equipment', 'recordedBy']);

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('operational_status')) {
            $query->where('operational_status', $request->operational_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('reading_time', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('reading_time', '<=', $request->date_to);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('vessel', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('equipment', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('tag', 'LIKE', '%' . $search . '%')
                        ->orWhere('code', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Sorting
        $allowedSorts = ['reading_time', 'vessel_id', 'equipment_id', 'shift', 'operational_status', 'running_hours'];
        $sort = $request->get('sort', 'reading_time');
        $sortDir = $request->get('sortDir', 'desc');

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('reading_time', 'desc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);

        if ($request->filled('page')) {
            $readings = $query->paginate($limit);
            return response()->json([
                'data' => EquipmentStatusResource::collection($readings->items()),
                'meta' => [
                    'current_page' => $readings->currentPage(),
                    'last_page' => $readings->lastPage(),
                    'per_page' => $readings->perPage(),
                    'total' => $readings->total(),
                    'from' => $readings->firstItem(),
                    'to' => $readings->lastItem(),
                ]
            ]);
        } else {
            $readings = $query->get();
            return response()->json([
                'data' => EquipmentStatusResource::collection($readings)
            ]);
        }
    }

    public function store(EquipmentStatusRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $validatedData = $request->validated();

            $data = new EquipmentStatus();
            $data->vessel_id = $user->vessel_id;
            $data->equipment_id = $validatedData['equipment_id'];
            $data->reading_time = Carbon::parse($validatedData['reading_time']);
            $data->shift = $validatedData['shift'];
            $data->operational_status = $validatedData['operational_status'];
            $data->running_hours = $validatedData['running_hours'] ?? null;
            $data->parameters = $validatedData['parameters'] ?? null;
            $data->recorded_by = $user->id;
            $data->save();

            // Log aktivitas create
            $this->logCreate($data, $request, __('activity.equipment_status.created', ['equipment_name' => $data->equipment?->name]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Equipment status berhasil dibuat.',
                'data' => new EquipmentStatusResource($data->load(['vessel', 'equipment', 'recordedBy']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat equipment status: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $reading = EquipmentStatus::with(['vessel', 'equipment', 'recordedBy'])->where('id', $id)->first();

        if (!$reading) {
            return response()->json([
                'success' => false,
                'message' => 'Equipment status tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new EquipmentStatusResource($reading)
        ]);
    }

    public function update(EquipmentStatusRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = EquipmentStatus::where('id', $id)->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Equipment status tidak ditemukan.',
                ], 404);
            }

            $validatedData = $request->validated();

            // Simpan data asli untuk logging
            $originalAttributes = $data->getOriginal();

            $data->equipment_id = $validatedData['equipment_id'];
            $data->reading_time = Carbon::parse($validatedData['reading_time']);
            $data->shift = $validatedData['shift'];
            $data->operational_status = $validatedData['operational_status'];
            $data->running_hours = $validatedData['running_hours'] ?? null;
            $data->parameters = $validatedData['parameters'] ?? null;
            $data->save();

            // Log aktivitas update
            $this->logUpdate($data, $request, $originalAttributes, __('activity.equipment_status.updated', ['equipment_name' => $data->equipment?->name]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Equipment status berhasil diupdate.',
                'data' => new EquipmentStatusResource($data->fresh()->load(['vessel', 'equipment', 'recordedBy']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate equipment status: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $reading = EquipmentStatus::where('id', $id)->first();

            if (!$reading) {
                return response()->json([
                    'success' => false,
                    'message' => 'Equipment status tidak ditemukan.',
                ], 404);
            }

            $readingInfo = "Status for {$reading->equipment?->name} on {$reading->reading_time?->format('Y-m-d H:i')}";

            // Log aktivitas delete sebelum menghapus
            $this->logDelete($reading, request(), __('activity.equipment_status.deleted', ['equipment_name' => $reading->equipment?->name]));

            $reading->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Equipment status '{$readingInfo}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus equipment status: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get equipment status by vessel
     */
    public function byVessel(Request $request, String $vesselId): JsonResponse
    {
        $query = EquipmentStatus::with(['vessel', 'equipment', 'recordedBy'])
            ->where('vessel_id', $vesselId);

        if ($request->filled('date')) {
            $query->whereDate('reading_time', $request->date);
        }

        $readings = $query->orderBy('reading_time', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => EquipmentStatusResource::collection($readings)
        ]);
    }

    /**
     * Get latest status by equipment
     */
    public function latestByEquipment(String $equipmentId): JsonResponse
    {
        $reading = EquipmentStatus::with(['vessel', 'equipment', 'recordedBy'])
            ->where('equipment_id', $equipmentId)
            ->orderBy('reading_time', 'desc')
            ->first();

        if (!$reading) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada status untuk equipment ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new EquipmentStatusResource($reading)
        ]);
    }

    /**
     * Get equipment status statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $vessel_id = $request->get('vessel_id');

        $query = EquipmentStatus::with(['vessel', 'equipment'])
            ->whereDate('reading_time', $date);

        if ($vessel_id) {
            $query->where('vessel_id', $vessel_id);
        }

        // Overall statistics
        $overallStats = $query->selectRaw('
            COUNT(*) as total_readings,
            COUNT(DISTINCT equipment_id) as active_equipment,
            COUNT(DISTINCT vessel_id) as active_vessels,
            COUNT(CASE WHEN operational_status = "Running" THEN 1 END) as running_count,
            COUNT(CASE WHEN operational_status = "Standby" THEN 1 END) as standby_count,
            COUNT(CASE WHEN operational_status = "Shutdown" THEN 1 END) as shutdown_count,
            AVG(running_hours) as avg_running_hours,
            SUM(running_hours) as total_running_hours
        ')->first();

        // Status breakdown by shift
        $shiftStats = $query->selectRaw('
            shift,
            operational_status,
            COUNT(*) as count
        ')->groupBy('shift', 'operational_status')->get();

        // Equipment with most running hours
        $topEquipment = $query->selectRaw('
            equipment_id,
            SUM(running_hours) as total_hours,
            COUNT(*) as readings_count,
            AVG(running_hours) as avg_hours
        ')
        ->groupBy('equipment_id')
        ->orderBy('total_hours', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($item) {
            $equipment = Equipment::find($item->equipment_id);

            return [
                'equipment_name' => $equipment?->name,
                'equipment_tag' => $equipment?->tag,
                'total_hours' => number_format($item->total_hours, 2),
                'avg_hours' => number_format($item->avg_hours, 2),
                'readings_count' => (int) $item->readings_count,
            ];
        });

        // Calculate availability percentage
        $totalHours = 24; // Total hours in a day
        $equipmentCount = $overallStats->active_equipment ?? 1;
        $maxPossibleHours = $totalHours * $equipmentCount;
        $availabilityPercent = $maxPossibleHours > 0 ? (($overallStats->total_running_hours ?? 0) / $maxPossibleHours) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'overview' => [
                    'total_readings' => (int) ($overallStats->total_readings ?? 0),
                    'active_equipment' => (int) ($overallStats->active_equipment ?? 0),
                    'active_vessels' => (int) ($overallStats->active_vessels ?? 0),
                ],
                'status_summary' => [
                    'running_count' => (int) ($overallStats->running_count ?? 0),
                    'standby_count' => (int) ($overallStats->standby_count ?? 0),
                    'shutdown_count' => (int) ($overallStats->shutdown_count ?? 0),
                ],
                'operations' => [
                    'total_running_hours' => number_format($overallStats->total_running_hours ?? 0, 2),
                    'avg_running_hours' => number_format($overallStats->avg_running_hours ?? 0, 2),
                    'availability_percent' => number_format($availabilityPercent, 1),
                ],
                'shift_breakdown' => $shiftStats->groupBy(['shift', 'operational_status'])->map(function ($shiftGroup, $shift) {
                    return $shiftGroup->mapWithKeys(function ($statusGroup, $status) {
                        return [$status => $statusGroup->sum('count')];
                    });
                }),
                'top_equipment' => $topEquipment,
            ]
        ]);
    }
}