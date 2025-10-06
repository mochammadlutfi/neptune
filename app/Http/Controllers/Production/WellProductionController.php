<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\Production\WellProductionRequest;
use App\Http\Resources\Production\WellProductionResource;
use App\Models\Production\WellProduction;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;
class WellProductionController extends Controller
{
    use ActivityLogger;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = WellProduction::with(['vessel', 'well']);

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('well_id')) {
            $query->where('well_id', $request->well_id);
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->shift);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('reading_datetime', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('reading_datetime', '<=', $request->date_to);
        }

        if ($request->filled('is_well_test')) {
            $query->where('is_well_test', $request->is_well_test == '1');
        }

        if ($request->filled('data_quality')) {
            $query->where('data_quality', $request->data_quality);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('vessel', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('well', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('code', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhere('recorded_by', 'LIKE', '%' . $search . '%')
                  ->orWhere('validated_by', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['reading_datetime', 'vessel_id', 'well_id', 'shift', 'oil_rate_bph', 'gas_rate_mscfh', 'water_rate_bph'];
        $sort = $request->get('sort', 'reading_datetime');
        $sortDir = $request->get('sortDir', 'desc');
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('reading_datetime', 'desc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);
        
        if ($request->filled('page')) {
            $readings = $query->paginate($limit);
            return response()->json([
                'data' => WellProductionResource::collection($readings->items()),
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
                'data' => WellProductionResource::collection($readings)
            ]);
        }
    }

    public function store(WellProductionRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            dd($request->all());
            $validatedData = $request->validated();
            // dd($validatedData['reading_datetime']);
            // $reading = WellProduction::create($validatedData);
            $data = new WellProduction();
            $data->vessel_id = $user->vessel_id;
            $data->reading_datetime = Carbon::now();
            $data->shift = $validatedData['shift'];
            $data->recorded_by = $user->id;
            $data->well_id = $validatedData['well_id'];
            $data->oil_rate_bph = $validatedData['oil_rate_bph'];
            $data->gas_rate_mscfh = $validatedData['gas_rate_mscfh'];
            $data->water_rate_bph = $validatedData['water_rate_bph'];
            $data->wellhead_pressure_psi = $validatedData['wellhead_pressure_psi'];
            $data->wellhead_temperature_f = $validatedData['wellhead_temperature_f'];
            $data->choke_size_64th = $validatedData['choke_size_64th'];
            $data->flow_hours = $validatedData['flow_hours'];
            $data->api_gravity = $validatedData['api_gravity'];
            $data->bs_w_percent = $validatedData['bs_w_percent'];
            $data->remarks = $validatedData['remarks'];
            $data->save();

            // Log aktivitas create
            $this->logCreate($data, $request, __('activity.well_production.created', ['well_name' => $data->well?->name]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Well production reading berhasil dibuat.',
                'data' => new WellProductionResource($data->load(['vessel', 'well']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat well production reading: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $reading = WellProduction::with(['vessel', 'well'])->where('id', $id)->first();
        
        if (!$reading) {
            return response()->json([
                'success' => false,
                'message' => 'Well production reading tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new WellProductionResource($reading)
        ]);
    }

    public function update(WellProductionRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = WellProduction::where('id', $id)->first();
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Well production reading tidak ditemukan.',
                ], 404);
            }
            $validatedData = $request->validated();
            $data->reading_datetime = Carbon::parse($validatedData['reading_datetime']);
            $data->well_id = $validatedData['well_id'];
            $data->oil_rate_bph = $validatedData['oil_rate_bph'];
            $data->gas_rate_mscfh = $validatedData['gas_rate_mscfh'];
            $data->water_rate_bph = $validatedData['water_rate_bph'];
            $data->wellhead_pressure_psi = $validatedData['wellhead_pressure_psi'];
            $data->wellhead_temperature_f = $validatedData['wellhead_temperature_f'];
            $data->choke_size_64th = $validatedData['choke_size_64th'];
            $data->flow_hours = $validatedData['flow_hours'];
            $data->api_gravity = $validatedData['api_gravity'];
            $data->bs_w_percent = $validatedData['bs_w_percent'];
            $data->remarks = $validatedData['remarks'];
            // Simpan data asli untuk logging
            $originalAttributes = $data->getOriginal();
            $data->save();

            // Log aktivitas update
            $this->logUpdate($data, $request, $originalAttributes, __('activity.well_production.updated', ['well_name' => $data->well?->name]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Well production reading berhasil diupdate.',
                'data' => new WellProductionResource($data->fresh()->load(['vessel', 'well']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate well production reading: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $reading = WellProduction::where('id', $id)->first();
            
            if (!$reading) {
                return response()->json([
                    'success' => false,
                    'message' => 'Well production reading tidak ditemukan.',
                ], 404);
            }
            
            $readingInfo = "Reading for {$reading->well?->name} on {$reading->reading_datetime?->format('Y-m-d')}";
            
            // Log aktivitas delete sebelum menghapus
            $this->logDelete($reading, $request, __('activity.well_production.deleted', ['well_name' => $reading->well?->name]));
            
            $reading->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Well production reading '{$readingInfo}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus well production reading: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get production readings by vessel
     */
    public function byVessel(Request $request, String $vesselId): JsonResponse
    {
        $query = WellProduction::with(['vessel', 'well'])
            ->where('vessel_id', $vesselId);

        if ($request->filled('date')) {
            $query->whereDate('reading_datetime', $request->date);
        }

        $readings = $query->orderBy('reading_datetime', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => WellProductionResource::collection($readings)
        ]);
    }

    /**
     * Get latest reading by well
     */
    public function latestByWell(String $wellId): JsonResponse
    {
        $reading = WellProduction::with(['vessel', 'well'])
            ->where('well_id', $wellId)
            ->orderBy('reading_datetime', 'desc')
            ->first();
        
        if (!$reading) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada production reading untuk well ini.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new WellProductionResource($reading)
        ]);
    }

    /**
     * Get daily statistics overview for wells production
     */
    public function stats(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $vessel_id = $request->get('vessel_id');
        
        $query = WellProduction::with(['vessel', 'well'])
            ->whereDate('reading_datetime', $date);
        
        if ($vessel_id) {
            $query->where('vessel_id', $vessel_id);
        }

        // Overall statistics
        $overallStats = $query->selectRaw('
            COUNT(*) as total_readings,
            COUNT(DISTINCT well_id) as active_wells,
            COUNT(DISTINCT vessel_id) as active_vessels,
            SUM(oil_rate_bph) as total_oil_bph,
            SUM(gas_rate_mscfh) as total_gas_mscfh,
            SUM(water_rate_bph) as total_water_bph,
            AVG(wellhead_pressure_psi) as avg_wellhead_pressure,
            AVG(wellhead_temperature_f) as avg_wellhead_temperature,
            SUM(flow_hours) as total_flow_hours,
            SUM(downtime_hours) as total_downtime_hours,
            COUNT(CASE WHEN is_well_test = 1 THEN 1 END) as well_tests_count,
            COUNT(CASE WHEN data_quality = "Good" THEN 1 END) as good_quality_count
        ')->first();

        // Well status breakdown
        $wellStatusStats = Well::selectRaw('
            status,
            COUNT(*) as count
        ')->groupBy('status')->get()->pluck('count', 'status')->toArray();

        // Shift breakdown
        $shiftStats = $query->selectRaw('
            shift,
            COUNT(*) as readings_count,
            SUM(oil_rate_bph) as oil_total,
            SUM(gas_rate_mscfh) as gas_total,
            AVG(wellhead_pressure_psi) as avg_pressure
        ')->groupBy('shift')->get();

        // Top performing wells
        $topWells = $query->selectRaw('
            well_id,
            SUM(oil_rate_bph + water_rate_bph) as total_liquid_bph,
            SUM(gas_rate_mscfh) as total_gas_mscfh,
            AVG(wellhead_pressure_psi) as avg_pressure,
            SUM(flow_hours) as flow_hours,
            SUM(downtime_hours) as downtime_hours
        ')
        ->groupBy('well_id')
        ->orderBy('total_liquid_bph', 'desc')
        ->limit(5)
        ->get()
        ->map(function ($item) {
            $well = Well::find($item->well_id);
            $uptime = ($item->flow_hours + $item->downtime_hours) > 0 
                ? ($item->flow_hours / ($item->flow_hours + $item->downtime_hours)) * 100 
                : 0;
            
            return [
                'well_name' => $well?->name,
                'well_code' => $well?->code,
                'total_liquid_bph' => number_format($item->total_liquid_bph, 2),
                'total_gas_mscfh' => number_format($item->total_gas_mscfh, 2),
                'avg_pressure_psi' => number_format($item->avg_pressure, 1),
                'uptime_percent' => number_format($uptime, 1),
            ];
        });

        // Calculate derived metrics
        $totalLiquid = ($overallStats->total_oil_bph ?? 0) + ($overallStats->total_water_bph ?? 0);
        $waterCut = $totalLiquid > 0 ? (($overallStats->total_water_bph ?? 0) / $totalLiquid) * 100 : 0;
        $totalHours = ($overallStats->total_flow_hours ?? 0) + ($overallStats->total_downtime_hours ?? 0);
        $uptimePercent = $totalHours > 0 ? (($overallStats->total_flow_hours ?? 0) / $totalHours) * 100 : 0;
        $dataQualityPercent = $overallStats->total_readings > 0 ? (($overallStats->good_quality_count ?? 0) / $overallStats->total_readings) * 100 : 0;
        $gor = ($overallStats->total_oil_bph ?? 0) > 0 ? (($overallStats->total_gas_mscfh ?? 0) * 1000) / ($overallStats->total_oil_bph ?? 0) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'overview' => [
                    'total_readings' => (int) ($overallStats->total_readings ?? 0),
                    'active_wells' => (int) ($overallStats->active_wells ?? 0),
                    'active_vessels' => (int) ($overallStats->active_vessels ?? 0),
                    'well_tests_count' => (int) ($overallStats->well_tests_count ?? 0),
                ],
                'production' => [
                    'total_oil_bph' => number_format($overallStats->total_oil_bph ?? 0, 2),
                    'total_gas_mscfh' => number_format($overallStats->total_gas_mscfh ?? 0, 2),
                    'total_water_bph' => number_format($overallStats->total_water_bph ?? 0, 2),
                    'total_liquid_bph' => number_format($totalLiquid, 2),
                    'water_cut_percent' => number_format($waterCut, 1),
                    'gor_scf_bbl' => number_format($gor, 0),
                ],
                'operations' => [
                    'avg_wellhead_pressure_psi' => number_format($overallStats->avg_wellhead_pressure ?? 0, 1),
                    'avg_wellhead_temperature_f' => number_format($overallStats->avg_wellhead_temperature ?? 0, 1),
                    'total_flow_hours' => number_format($overallStats->total_flow_hours ?? 0, 1),
                    'total_downtime_hours' => number_format($overallStats->total_downtime_hours ?? 0, 1),
                    'uptime_percent' => number_format($uptimePercent, 1),
                ],
                'quality' => [
                    'data_quality_percent' => number_format($dataQualityPercent, 1),
                    'good_quality_readings' => (int) ($overallStats->good_quality_count ?? 0),
                ],
                'well_status_breakdown' => $wellStatusStats,
                'shift_breakdown' => $shiftStats->map(function ($item) {
                    return [
                        'shift' => $item->shift,
                        'readings_count' => (int) $item->readings_count,
                        'oil_total_bph' => number_format($item->oil_total, 2),
                        'gas_total_mscfh' => number_format($item->gas_total, 2),
                        'avg_pressure_psi' => number_format($item->avg_pressure, 1),
                    ];
                }),
                'top_wells' => $topWells,
            ]
        ]);
    }
}
