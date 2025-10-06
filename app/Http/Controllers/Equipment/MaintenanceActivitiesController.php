<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\MaintenanceActivitiesRequest;
use App\Http\Resources\Equipment\MaintenanceActivitiesResource;
use App\Models\Equipment\MaintenanceActivities;
use App\Models\Master\Equipment;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class MaintenanceActivitiesController extends Controller
{
    use ActivityLogger;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = MaintenanceActivities::with(['vessel', 'equipment', 'recordedBy']);

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        if ($request->filled('work_type')) {
            $query->where('work_type', $request->work_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('activity_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('activity_date', '<=', $request->date_to);
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
                  })
                  ->orWhere('work_order_no', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%')
                  ->orWhere('completed_by', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting
        $allowedSorts = ['activity_date', 'vessel_id', 'equipment_id', 'work_type', 'status', 'completion_date'];
        $sort = $request->get('sort', 'activity_date');
        $sortDir = $request->get('sortDir', 'desc');

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('activity_date', 'desc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);

        if ($request->filled('page')) {
            $activities = $query->paginate($limit);
            return response()->json([
                'data' => MaintenanceActivitiesResource::collection($activities->items()),
                'meta' => [
                    'current_page' => $activities->currentPage(),
                    'last_page' => $activities->lastPage(),
                    'per_page' => $activities->perPage(),
                    'total' => $activities->total(),
                    'from' => $activities->firstItem(),
                    'to' => $activities->lastItem(),
                ]
            ]);
        } else {
            $activities = $query->get();
            return response()->json([
                'data' => MaintenanceActivitiesResource::collection($activities)
            ]);
        }
    }

    public function store(MaintenanceActivitiesRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $validatedData = $request->validated();

            $data = new MaintenanceActivities();
            $data->vessel_id = $user->vessel_id;
            $data->equipment_id = $validatedData['equipment_id'];
            $data->activity_date = Carbon::parse($validatedData['activity_date']);
            $data->work_order_no = $validatedData['work_order_no'] ?? null;
            $data->work_type = $validatedData['work_type'];
            $data->description = $validatedData['description'];
            $data->work_hours = $validatedData['work_hours'] ?? null;
            $data->manpower_count = $validatedData['manpower_count'] ?? 1;
            $data->status = $validatedData['status'] ?? 'Planned';
            $data->completed_by = $validatedData['completed_by'] ?? null;
            $data->completion_date = $validatedData['completion_date'] ? Carbon::parse($validatedData['completion_date']) : null;
            $data->recorded_by = $user->id;
            $data->save();

            // Log aktivitas create
            $this->logCreate($data, $request, __('activity.maintenance_activities.created', ['work_order' => $data->work_order_no]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Maintenance activity berhasil dibuat.',
                'data' => new MaintenanceActivitiesResource($data->load(['vessel', 'equipment', 'recordedBy']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat maintenance activity: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $activity = MaintenanceActivities::with(['vessel', 'equipment', 'recordedBy'])->where('id', $id)->first();

        if (!$activity) {
            return response()->json([
                'success' => false,
                'message' => 'Maintenance activity tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new MaintenanceActivitiesResource($activity)
        ]);
    }

    public function update(MaintenanceActivitiesRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = MaintenanceActivities::where('id', $id)->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maintenance activity tidak ditemukan.',
                ], 404);
            }

            $validatedData = $request->validated();

            // Simpan data asli untuk logging
            $originalAttributes = $data->getOriginal();

            $data->equipment_id = $validatedData['equipment_id'];
            $data->activity_date = Carbon::parse($validatedData['activity_date']);
            $data->work_order_no = $validatedData['work_order_no'] ?? null;
            $data->work_type = $validatedData['work_type'];
            $data->description = $validatedData['description'];
            $data->work_hours = $validatedData['work_hours'] ?? null;
            $data->manpower_count = $validatedData['manpower_count'] ?? 1;
            $data->status = $validatedData['status'] ?? 'Planned';
            $data->completed_by = $validatedData['completed_by'] ?? null;
            $data->completion_date = $validatedData['completion_date'] ? Carbon::parse($validatedData['completion_date']) : null;
            $data->save();

            // Log aktivitas update
            $this->logUpdate($data, $request, $originalAttributes, __('activity.maintenance_activities.updated', ['work_order' => $data->work_order_no]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Maintenance activity berhasil diupdate.',
                'data' => new MaintenanceActivitiesResource($data->fresh()->load(['vessel', 'equipment', 'recordedBy']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate maintenance activity: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $activity = MaintenanceActivities::where('id', $id)->first();

            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maintenance activity tidak ditemukan.',
                ], 404);
            }

            $activityInfo = "Work Order {$activity->work_order_no} for {$activity->equipment?->name}";

            // Log aktivitas delete sebelum menghapus
            $this->logDelete($activity, request(), __('activity.maintenance_activities.deleted', ['work_order' => $activity->work_order_no]));

            $activity->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Maintenance activity '{$activityInfo}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus maintenance activity: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get maintenance activities by vessel
     */
    public function byVessel(Request $request, String $vesselId): JsonResponse
    {
        $query = MaintenanceActivities::with(['vessel', 'equipment', 'recordedBy'])
            ->where('vessel_id', $vesselId);

        if ($request->filled('date')) {
            $query->whereDate('activity_date', $request->date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $activities = $query->orderBy('activity_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => MaintenanceActivitiesResource::collection($activities)
        ]);
    }

    /**
     * Get maintenance activities by equipment
     */
    public function byEquipment(String $equipmentId): JsonResponse
    {
        $activities = MaintenanceActivities::with(['vessel', 'equipment', 'recordedBy'])
            ->where('equipment_id', $equipmentId)
            ->orderBy('activity_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => MaintenanceActivitiesResource::collection($activities)
        ]);
    }

    /**
     * Complete maintenance activity
     */
    public function complete(Request $request, String $id): JsonResponse
    {
        $request->validate([
            'completed_by' => 'required|string|max:100',
            'work_hours' => 'nullable|numeric|min:0',
            'completion_notes' => 'nullable|string'
        ]);

        DB::beginTransaction();
        try {
            $activity = MaintenanceActivities::where('id', $id)->first();

            if (!$activity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maintenance activity tidak ditemukan.',
                ], 404);
            }

            if ($activity->status === 'Completed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Maintenance activity sudah completed.',
                ], 400);
            }

            // Simpan data asli untuk logging
            $originalAttributes = $activity->getOriginal();

            $activity->status = 'Completed';
            $activity->completed_by = $request->completed_by;
            $activity->completion_date = Carbon::now();
            $activity->work_hours = $request->work_hours ?? $activity->work_hours;
            $activity->save();

            // Log aktivitas completion
            $this->logUpdate($activity, $request, $originalAttributes, __('activity.maintenance_activities.completed', ['work_order' => $activity->work_order_no]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Maintenance activity berhasil di-complete.',
                'data' => new MaintenanceActivitiesResource($activity->fresh()->load(['vessel', 'equipment', 'recordedBy']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal complete maintenance activity: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get maintenance activities statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $vessel_id = $request->get('vessel_id');

        $query = MaintenanceActivities::with(['vessel', 'equipment'])
            ->whereDate('activity_date', $date);

        if ($vessel_id) {
            $query->where('vessel_id', $vessel_id);
        }

        // Overall statistics
        $overallStats = $query->selectRaw('
            COUNT(*) as total_activities,
            COUNT(DISTINCT equipment_id) as equipment_maintained,
            COUNT(DISTINCT vessel_id) as active_vessels,
            COUNT(CASE WHEN status = "Completed" THEN 1 END) as completed_count,
            COUNT(CASE WHEN status = "In Progress" THEN 1 END) as in_progress_count,
            COUNT(CASE WHEN status = "Planned" THEN 1 END) as planned_count,
            COUNT(CASE WHEN status = "Deferred" THEN 1 END) as deferred_count,
            SUM(work_hours) as total_work_hours,
            AVG(work_hours) as avg_work_hours
        ')->first();

        // Work type breakdown
        $workTypeStats = $query->selectRaw('
            work_type,
            COUNT(*) as count,
            COUNT(CASE WHEN status = "Completed" THEN 1 END) as completed,
            SUM(work_hours) as total_hours
        ')->groupBy('work_type')->get();

        // Equipment with most maintenance
        $topEquipment = $query->selectRaw('
            equipment_id,
            COUNT(*) as maintenance_count,
            SUM(work_hours) as total_hours,
            COUNT(CASE WHEN status = "Completed" THEN 1 END) as completed_count
        ')
        ->groupBy('equipment_id')
        ->orderBy('maintenance_count', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($item) {
            $equipment = Equipment::find($item->equipment_id);

            return [
                'equipment_name' => $equipment?->name,
                'equipment_tag' => $equipment?->tag,
                'maintenance_count' => (int) $item->maintenance_count,
                'total_hours' => number_format($item->total_hours, 1),
                'completed_count' => (int) $item->completed_count,
                'completion_rate' => $item->maintenance_count > 0 ? number_format(($item->completed_count / $item->maintenance_count) * 100, 1) : '0',
            ];
        });

        // Calculate completion rate
        $completionRate = $overallStats->total_activities > 0 ? (($overallStats->completed_count ?? 0) / $overallStats->total_activities) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'overview' => [
                    'total_activities' => (int) ($overallStats->total_activities ?? 0),
                    'equipment_maintained' => (int) ($overallStats->equipment_maintained ?? 0),
                    'active_vessels' => (int) ($overallStats->active_vessels ?? 0),
                    'completion_rate' => number_format($completionRate, 1),
                ],
                'status_summary' => [
                    'completed_count' => (int) ($overallStats->completed_count ?? 0),
                    'in_progress_count' => (int) ($overallStats->in_progress_count ?? 0),
                    'planned_count' => (int) ($overallStats->planned_count ?? 0),
                    'deferred_count' => (int) ($overallStats->deferred_count ?? 0),
                ],
                'work_hours' => [
                    'total_work_hours' => number_format($overallStats->total_work_hours ?? 0, 1),
                    'avg_work_hours' => number_format($overallStats->avg_work_hours ?? 0, 1),
                ],
                'work_type_breakdown' => $workTypeStats->map(function ($item) {
                    return [
                        'work_type' => $item->work_type,
                        'count' => (int) $item->count,
                        'completed' => (int) $item->completed,
                        'total_hours' => number_format($item->total_hours ?? 0, 1),
                    ];
                }),
                'top_equipment' => $topEquipment,
            ]
        ]);
    }
}