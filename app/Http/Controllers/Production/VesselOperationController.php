<?php
namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\Production\VesselOperationRequest;
use App\Http\Resources\Production\VesselOperationResource;
use App\Models\Production\VesselOperation;
use App\Models\Production\VesselOperationWellFlow;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Traits\ActivityLogger;

class VesselOperationController extends Controller
{
    use ActivityLogger;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = VesselOperation::with(['vessel', 'recordedBy', 'wellFlows.well']);

        // Filter by vessel
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        // Filter by date range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('date', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('vessel', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('code', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Sorting
        $allowedSorts = ['date', 'inlet_gas_mmscfd', 'total_sales_gas_mmscfd'];
        $sort = $request->get('sort', 'date');
        $sortDir = $request->get('sortDir', 'desc');
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('date', 'desc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);
        
        if ($request->filled('page')) {
            $data = $query->paginate($limit);
            return response()->json([
                'data' => VesselOperationResource::collection($data->items()),
                'meta' => [
                    'current_page' => $data->currentPage(),
                    'last_page' => $data->lastPage(),
                    'per_page' => $data->perPage(),
                    'total' => $data->total(),
                    'from' => $data->firstItem(),
                    'to' => $data->lastItem(),
                ]
            ]);
        } else {
            $data = $query->get();
            return response()->json([
                'data' => VesselOperationResource::collection($data)
            ]);
        }
    }

    public function store(VesselOperationRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $user = auth()->user();

            // Create vessel operation
            $data = VesselOperation::create([
                'vessel_id' => $validatedData['vessel_id'],
                'date' => $validatedData['date'],
                
                // Gas Operations
                'inlet_gas_mmscfd' => $validatedData['inlet_gas_mmscfd'] ?? null,
                'total_sales_gas_mmscfd' => $validatedData['total_sales_gas_mmscfd'] ?? null,
                'fuel_gas_mmscfd' => $validatedData['fuel_gas_mmscfd'] ?? null,
                'flare_hp_mmscfd' => $validatedData['flare_hp_mmscfd'] ?? null,
                'flare_lp_mmscfd' => $validatedData['flare_lp_mmscfd'] ?? null,
                'gas_export_uptime' => $validatedData['gas_export_uptime'] ?? null,
                'inlet_pressure_psi' => $validatedData['inlet_pressure_psi'] ?? null,
                'inlet_temp_f' => $validatedData['inlet_temp_f'] ?? null,
                'outlet_pressure_psi' => $validatedData['outlet_pressure_psi'] ?? null,
                'outlet_temp_f' => $validatedData['outlet_temp_f'] ?? null,
                
                // Condensate Operations
                'condensate_produced_lts' => $validatedData['condensate_produced_lts'] ?? null,
                'condensate_produced_bbls' => $validatedData['condensate_produced_bbls'] ?? null,
                'condensate_skimmed_bbls' => $validatedData['condensate_skimmed_bbls'] ?? null,
                'condensate_stock_bbls' => $validatedData['condensate_stock_bbls'] ?? null,
                'condensate_consumed_gtg_bbls' => $validatedData['condensate_consumed_gtg_bbls'] ?? null,
                'condensate_temp_f' => $validatedData['condensate_temp_f'] ?? null,
                'condensate_uptime' => $validatedData['condensate_uptime'] ?? null,
                
                // Diesel Fuel
                'diesel_fuel_mopu_ltr' => $validatedData['diesel_fuel_mopu_ltr'] ?? null,
                'diesel_fuel_hcml_ltr' => $validatedData['diesel_fuel_hcml_ltr'] ?? null,
                
                // Water Operations
                'produced_water_total_bbls' => $validatedData['produced_water_total_bbls'] ?? null,
                'produced_water_offspec_bbls' => $validatedData['produced_water_offspec_bbls'] ?? null,
                'produced_water_overboard_bbls' => $validatedData['produced_water_overboard_bbls'] ?? null,
                'water_oiw_content_ppm' => $validatedData['water_oiw_content_ppm'] ?? null,
                
                // Metadata
                'recorded_by' => $user->id,
            ]);

            // Create well flows if provided
            if (!empty($validatedData['well_flows'])) {
                foreach ($validatedData['well_flows'] as $wellFlow) {
                    VesselOperationWellFlow::create([
                        'vessel_operation_id' => $data->id,
                        'well_id' => $wellFlow['well_id'],
                        'gas_flow_rate_mmscfd' => $wellFlow['gas_flow_rate_mmscfd'],
                    ]);
                }
            }

            $this->logCreate($data, $request, __('activity.vessel_operation.created', [
                'date' => $data->date->format('Y-m-d')
            ]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Vessel Operation berhasil dibuat.',
                'data' => new VesselOperationResource($data->load(['vessel', 'wellFlows.well', 'recordedBy']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat Vessel Operation: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(string $id): JsonResponse
    {
        $data = VesselOperation::with(['vessel', 'recordedBy', 'wellFlows.well'])
            ->where('id', $id)
            ->first();
        
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Vessel Operation tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new VesselOperationResource($data)
        ]);
    }

    public function update(VesselOperationRequest $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = VesselOperation::where('id', $id)->first();
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vessel Operation tidak ditemukan.',
                ], 404);
            }
            
            $validatedData = $request->validated();
            $originalAttributes = $data->getOriginal();

            // Update vessel operation
            $data->update([
                'vessel_id' => $validatedData['vessel_id'],
                'date' => $validatedData['date'],
                
                // Gas Operations
                'inlet_gas_mmscfd' => $validatedData['inlet_gas_mmscfd'] ?? null,
                'total_sales_gas_mmscfd' => $validatedData['total_sales_gas_mmscfd'] ?? null,
                'fuel_gas_mmscfd' => $validatedData['fuel_gas_mmscfd'] ?? null,
                'flare_hp_mmscfd' => $validatedData['flare_hp_mmscfd'] ?? null,
                'flare_lp_mmscfd' => $validatedData['flare_lp_mmscfd'] ?? null,
                'gas_export_uptime' => $validatedData['gas_export_uptime'] ?? null,
                'inlet_pressure_psi' => $validatedData['inlet_pressure_psi'] ?? null,
                'inlet_temp_f' => $validatedData['inlet_temp_f'] ?? null,
                'outlet_pressure_psi' => $validatedData['outlet_pressure_psi'] ?? null,
                'outlet_temp_f' => $validatedData['outlet_temp_f'] ?? null,
                
                // Condensate Operations
                'condensate_produced_lts' => $validatedData['condensate_produced_lts'] ?? null,
                'condensate_produced_bbls' => $validatedData['condensate_produced_bbls'] ?? null,
                'condensate_skimmed_bbls' => $validatedData['condensate_skimmed_bbls'] ?? null,
                'condensate_stock_bbls' => $validatedData['condensate_stock_bbls'] ?? null,
                'condensate_consumed_gtg_bbls' => $validatedData['condensate_consumed_gtg_bbls'] ?? null,
                'condensate_temp_f' => $validatedData['condensate_temp_f'] ?? null,
                'condensate_uptime' => $validatedData['condensate_uptime'] ?? null,
                
                // Diesel Fuel
                'diesel_fuel_mopu_ltr' => $validatedData['diesel_fuel_mopu_ltr'] ?? null,
                'diesel_fuel_hcml_ltr' => $validatedData['diesel_fuel_hcml_ltr'] ?? null,
                
                // Water Operations
                'produced_water_total_bbls' => $validatedData['produced_water_total_bbls'] ?? null,
                'produced_water_offspec_bbls' => $validatedData['produced_water_offspec_bbls'] ?? null,
                'produced_water_overboard_bbls' => $validatedData['produced_water_overboard_bbls'] ?? null,
                'water_oiw_content_ppm' => $validatedData['water_oiw_content_ppm'] ?? null,
            ]);

            // Delete existing well flows and create new ones
            $data->wellFlows()->delete();
            
            if (!empty($validatedData['well_flows'])) {
                foreach ($validatedData['well_flows'] as $wellFlow) {
                    VesselOperationWellFlow::create([
                        'vessel_operation_id' => $data->id,
                        'well_id' => $wellFlow['well_id'],
                        'gas_flow_rate_mmscfd' => $wellFlow['gas_flow_rate_mmscfd'],
                    ]);
                }
            }

            $this->logUpdate($data, $request, $originalAttributes, __('activity.vessel_operation.updated', [
                'date' => $data->date->format('Y-m-d')
            ]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Vessel Operation berhasil diupdate.',
                'data' => new VesselOperationResource($data->fresh()->load(['vessel', 'wellFlows.well', 'recordedBy']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate Vessel Operation: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Request $request, string $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = VesselOperation::where('id', $id)->first();
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vessel Operation tidak ditemukan.',
                ], 404);
            }
            
            $operationDate = $data->date->format('Y-m-d');
            
            // Log aktivitas delete
            $this->logDelete($data, $request, __('activity.vessel_operation.deleted', [
                'date' => $operationDate
            ]));
            
            $data->delete();
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Vessel Operation '{$operationDate}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Vessel Operation: ' . $e->getMessage(),
            ], 422);
        }
    }
}