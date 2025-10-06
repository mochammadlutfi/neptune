<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\Production\FPUOperationsRequest;
use App\Http\Resources\Production\FPUOperationsResource;
use App\Models\Production\FPUOperations;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class FPUOperationsController extends Controller
{
    use ActivityLogger;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = FPUOperations::with(['vessel', 'recordedBy']);

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('reading_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('reading_date', '<=', $request->date_to);
        }

        if ($request->filled('reading_hour')) {
            $query->where('reading_hour', $request->reading_hour);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('vessel', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('recordedBy', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['reading_date', 'reading_hour', 'vessel_id', 'inlet_pressure_psi', 'total_gas_rate_mmscfd'];
        $sort = $request->get('sort', 'reading_date');
        $sortDir = $request->get('sortDir', 'desc');
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('reading_date', 'desc')->orderBy('reading_hour', 'desc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);
        
        if ($request->filled('page')) {
            $readings = $query->paginate($limit);
            return response()->json([
                'data' => FPUOperationsResource::collection($readings->items()),
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
                'data' => FPUOperationsResource::collection($readings)
            ]);
        }
    }

    public function store(FPUOperationsRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $validatedData = $request->validated();
            
            $data = new FPUOperations();
            $data->vessel_id = $user->vessel_id;
            $data->reading_date = Carbon::parse($validatedData['reading_date']);
            $data->reading_hour = $validatedData['reading_hour'];
            $data->inlet_pressure_psi = $validatedData['inlet_pressure_psi'] ?? null;
            $data->inlet_temp_f = $validatedData['inlet_temp_f'] ?? null;
            $data->outlet_pressure_psi = $validatedData['outlet_pressure_psi'] ?? null;
            $data->outlet_temp_f = $validatedData['outlet_temp_f'] ?? null;
            $data->total_gas_rate_mmscfd = $validatedData['total_gas_rate_mmscfd'] ?? null;
            $data->fuel_gas_rate_mmscfd = $validatedData['fuel_gas_rate_mmscfd'] ?? null;
            $data->flare_hp_rate_mmscfd = $validatedData['flare_hp_rate_mmscfd'] ?? null;
            $data->flare_lp_rate_mmscfd = $validatedData['flare_lp_rate_mmscfd'] ?? null;
            $data->process_data = $validatedData['process_data'] ?? null;
            $data->recorded_by = $user->id;
            $data->save();

            // Log aktivitas create
            $this->logCreate($data, $request, __('activity.fpu_operations.created', ['vessel_name' => $data->vessel?->name]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'FPU operations reading berhasil dibuat.',
                'data' => new FPUOperationsResource($data->load(['vessel', 'recordedBy']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat FPU operations reading: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $reading = FPUOperations::with(['vessel', 'recordedBy'])->where('id', $id)->first();
        
        if (!$reading) {
            return response()->json([
                'success' => false,
                'message' => 'FPU operations reading tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new FPUOperationsResource($reading)
        ]);
    }

    public function update(FPUOperationsRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = FPUOperations::where('id', $id)->first();
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'FPU operations reading tidak ditemukan.',
                ], 404);
            }
            
            $validatedData = $request->validated();
            $data->reading_date = Carbon::parse($validatedData['reading_date']);
            $data->reading_hour = $validatedData['reading_hour'];
            $data->inlet_pressure_psi = $validatedData['inlet_pressure_psi'] ?? null;
            $data->inlet_temp_f = $validatedData['inlet_temp_f'] ?? null;
            $data->outlet_pressure_psi = $validatedData['outlet_pressure_psi'] ?? null;
            $data->outlet_temp_f = $validatedData['outlet_temp_f'] ?? null;
            $data->total_gas_rate_mmscfd = $validatedData['total_gas_rate_mmscfd'] ?? null;
            $data->fuel_gas_rate_mmscfd = $validatedData['fuel_gas_rate_mmscfd'] ?? null;
            $data->flare_hp_rate_mmscfd = $validatedData['flare_hp_rate_mmscfd'] ?? null;
            $data->flare_lp_rate_mmscfd = $validatedData['flare_lp_rate_mmscfd'] ?? null;
            $data->process_data = $validatedData['process_data'] ?? null;
            
            // Simpan data asli untuk logging
            $originalAttributes = $data->getOriginal();
            $data->save();

            // Log aktivitas update
            $this->logUpdate($data, $request, $originalAttributes, __('activity.fpu_operations.updated', ['vessel_name' => $data->vessel?->name]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'FPU operations reading berhasil diupdate.',
                'data' => new FPUOperationsResource($data->fresh()->load(['vessel', 'recordedBy']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate FPU operations reading: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $reading = FPUOperations::where('id', $id)->first();
            
            if (!$reading) {
                return response()->json([
                    'success' => false,
                    'message' => 'FPU operations reading tidak ditemukan.',
                ], 404);
            }
            
            $readingInfo = "Reading for {$reading->vessel?->name} on {$reading->reading_date} at {$reading->reading_hour}";
            
            // Log aktivitas delete sebelum menghapus
            $this->logDelete($reading, $request, __('activity.fpu_operations.deleted', ['vessel_name' => $reading->vessel?->name]));
            
            $reading->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "FPU operations reading '{$readingInfo}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus FPU operations reading: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function byVessel(String $vesselId, Request $request): JsonResponse
    {
        $query = FPUOperations::with(['vessel', 'recordedBy'])
            ->where('vessel_id', $vesselId);

        if ($request->filled('date_from')) {
            $query->whereDate('reading_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('reading_date', '<=', $request->date_to);
        }

        $readings = $query->orderBy('reading_date', 'desc')
            ->orderBy('reading_hour', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => FPUOperationsResource::collection($readings)
        ]);
    }

    public function latestByVessel(String $vesselId): JsonResponse
    {
        $reading = FPUOperations::with(['vessel', 'recordedBy'])
            ->where('vessel_id', $vesselId)
            ->orderBy('reading_date', 'desc')
            ->orderBy('reading_hour', 'desc')
            ->first();

        if (!$reading) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada FPU operations reading untuk vessel ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new FPUOperationsResource($reading)
        ]);
    }

    public function hourlyReadings(String $date, Request $request): JsonResponse
    {
        $query = FPUOperations::with(['vessel', 'recordedBy'])
            ->whereDate('reading_date', $date);

        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        $readings = $query->orderBy('reading_hour', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => FPUOperationsResource::collection($readings)
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $query = FPUOperations::query();

        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('reading_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('reading_date', '<=', $request->date_to);
        }

        $stats = [
            'total_readings' => $query->count(),
            'avg_inlet_pressure' => $query->avg('inlet_pressure_psi'),
            'avg_total_gas_rate' => $query->avg('total_gas_rate_mmscfd'),
            'avg_fuel_gas_rate' => $query->avg('fuel_gas_rate_mmscfd'),
            'max_total_gas_rate' => $query->max('total_gas_rate_mmscfd'),
            'min_total_gas_rate' => $query->min('total_gas_rate_mmscfd'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}