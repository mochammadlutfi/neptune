<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\Production\GasSalesMeteringRequest;
use App\Http\Resources\Production\GasSalesMeteringResource;
use App\Models\Production\GasSalesMetering;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class GasSalesMeteringController extends Controller
{
    use ActivityLogger;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = GasSalesMetering::with(['vessel']);

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
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
                  ->orWhere('recorded_by', 'LIKE', '%' . $search . '%')
                  ->orWhere('approved_by', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['reading_time', 'vessel_id', 'export_pressure_psi', 'export_temperature_f', 'actual_delivery_mmscf', 'variance_percent'];
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
            $meteringData = $query->paginate($limit);
            return response()->json([
                'data' => GasSalesMeteringResource::collection($meteringData->items()),
                'meta' => [
                    'current_page' => $meteringData->currentPage(),
                    'last_page' => $meteringData->lastPage(),
                    'per_page' => $meteringData->perPage(),
                    'total' => $meteringData->total(),
                    'from' => $meteringData->firstItem(),
                    'to' => $meteringData->lastItem(),
                ]
            ]);
        } else {
            $meteringData = $query->get();
            return response()->json([
                'data' => GasSalesMeteringResource::collection($meteringData)
            ]);
        }
    }

    public function store(GasSalesMeteringRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $validatedData = $request->validated();
            
            $data = new GasSalesMetering();
            $data->vessel_id = $user->vessel_id;
            $data->reading_time = Carbon::parse($validatedData['reading_time']);
            $data->export_pressure_psi = $validatedData['export_pressure_psi'];
            $data->export_temp_f = $validatedData['export_temp_f'];
            $data->flowrate_mmscfd = $validatedData['flowrate_mmscfd'];
            $data->total_volume_mmscf = $validatedData['total_volume_mmscf'];
            $data->heating_value_btu_scf = $validatedData['heating_value_btu_scf'];
            $data->specific_gravity = $validatedData['specific_gravity'];
            $data->h2s_content_ppm = $validatedData['h2s_content_ppm'];
            $data->co2_content_percent = $validatedData['co2_content_percent'];
            $data->buyer_name = $validatedData['buyer_name'] ?? null;
            $data->nomination_mmscf = $validatedData['nomination_mmscf'];
            $data->actual_delivery_mmscf = $validatedData['actual_delivery_mmscf'];
            $data->variance_percent = $validatedData['variance_percent'];
            $data->recorded_by = $user->id;
            $data->remarks = $validatedData['remarks'] ?? null;
            $data->save();

            // Log aktivitas create
            $this->logCreate($data, $request, __('activity.gas_sales_metering.created', ['buyer_name' => $data->buyer_name]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Gas sales metering berhasil dibuat.',
                'data' => new GasSalesMeteringResource($data->load(['vessel']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat gas sales metering: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $metering = GasSalesMetering::with(['vessel'])->where('id', $id)->first();
        
        if (!$metering) {
            return response()->json([
                'success' => false,
                'message' => 'Gas sales metering tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new GasSalesMeteringResource($metering)
        ]);
    }

    public function update(GasSalesMeteringRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = GasSalesMetering::where('id', $id)->first();
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gas sales metering tidak ditemukan.',
                ], 404);
            }
            
            $validatedData = $request->validated();
            $data->reading_time = Carbon::parse($validatedData['reading_time']);
            $data->export_pressure_psi = $validatedData['export_pressure_psi'];
            $data->export_temp_f = $validatedData['export_temp_f'];
            $data->flowrate_mmscfd = $validatedData['flowrate_mmscfd'];
            $data->total_volume_mmscf = $validatedData['total_volume_mmscf'];
            $data->heating_value_btu_scf = $validatedData['heating_value_btu_scf'];
            $data->specific_gravity = $validatedData['specific_gravity'];
            $data->h2s_content_ppm = $validatedData['h2s_content_ppm'];
            $data->co2_content_percent = $validatedData['co2_content_percent'];
            $data->buyer_name = $validatedData['buyer_name'] ?? null;
            $data->nomination_mmscf = $validatedData['nomination_mmscf'];
            $data->actual_delivery_mmscf = $validatedData['actual_delivery_mmscf'];
            $data->variance_percent = $validatedData['variance_percent'];
            $data->remarks = $validatedData['remarks'] ?? null;
            
            // Simpan data asli untuk logging
            $originalAttributes = $data->getOriginal();
            $data->save();

            // Log aktivitas update
            $this->logUpdate($data, $request, $originalAttributes, __('activity.gas_sales_metering.updated', ['buyer_name' => $data->buyer_name]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Gas sales metering berhasil diupdate.',
                'data' => new GasSalesMeteringResource($data->fresh()->load(['vessel']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate gas sales metering: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $metering = GasSalesMetering::where('id', $id)->first();
            
            if (!$metering) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gas sales metering tidak ditemukan.',
                ], 404);
            }
            
            $meteringInfo = "Reading for {$metering->buyer_name} on {$metering->reading_time?->format('Y-m-d')}";
            
            // Log aktivitas delete sebelum menghapus
            $this->logDelete($metering, request(), __('activity.gas_sales_metering.deleted', ['buyer_name' => $metering->buyer_name]));
            
            $metering->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Gas sales metering '{$meteringInfo}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gas sales metering: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get gas sales metering by vessel
     */
    public function byVessel(Request $request, string $vesselId): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $sortBy = $request->get('sort_by', 'reading_time');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $query = GasSalesMetering::with(['vessel', 'recordedBy', 'approvedBy'])
            ->where('vessel_id', $vesselId);
        
        // Date range filter
        if ($request->has('date_from')) {
            $query->whereDate('reading_time', '>=', $request->date_from);
        }
        
        if ($request->has('date_to')) {
            $query->whereDate('reading_time', '<=', $request->date_to);
        }
        
        $data = $query->orderBy($sortBy, $sortOrder)->paginate($perPage);
        
        return response()->json([
            'success' => true,
            'data' => GasSalesMeteringResource::collection($data),
            'meta' => [
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'per_page' => $data->perPage(),
                'total' => $data->total(),
            ]
        ]);
    }
    
    /**
     * Get latest gas sales metering by vessel
     */
    public function latestByVessel(string $vesselId): JsonResponse
    {
        $data = GasSalesMetering::with(['vessel', 'recordedBy', 'approvedBy'])
            ->where('vessel_id', $vesselId)
            ->orderBy('reading_time', 'desc')
            ->first();
        
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Data gas sales metering tidak ditemukan untuk vessel ini.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new GasSalesMeteringResource($data),
        ]);
    }

}
