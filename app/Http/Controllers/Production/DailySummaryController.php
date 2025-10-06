<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Resources\Production\DailySummaryResource;
use App\Models\Production\DailySummary;
use App\Models\Production\WellProduction;
use App\Models\Production\GasSales;
use App\Models\Production\FPUOperations;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DailySummaryController extends Controller
{
    use ActivityLogger;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = DailySummary::with(['vessel']);

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('summary_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('summary_date', '<=', $request->date_to);
        }

        if ($request->filled('calculation_status')) {
            $query->where('calculation_status', $request->calculation_status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('vessel', function($q) use ($search) {
                    $q->where('name', 'LIKE', '%' . $search . '%');
                });
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['summary_date', 'vessel_id', 'total_oil_bbl', 'total_gas_mmscf', 'equipment_availability_pct'];
        $sort = $request->get('sort', 'summary_date');
        $sortDir = $request->get('sortDir', 'desc');
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('summary_date', 'desc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);
        
        if ($request->filled('page')) {
            $summaries = $query->paginate($limit);
            return response()->json([
                'data' => DailySummaryResource::collection($summaries->items()),
                'meta' => [
                    'current_page' => $summaries->currentPage(),
                    'last_page' => $summaries->lastPage(),
                    'per_page' => $summaries->perPage(),
                    'total' => $summaries->total(),
                    'from' => $summaries->firstItem(),
                    'to' => $summaries->lastItem(),
                ]
            ]);
        } else {
            $summaries = $query->get();
            return response()->json([
                'data' => DailySummaryResource::collection($summaries)
            ]);
        }
    }

    public function show(String $id): JsonResponse
    {
        $summary = DailySummary::with(['vessel'])->where('id', $id)->first();
        
        if (!$summary) {
            return response()->json([
                'success' => false,
                'message' => 'Daily summary tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new DailySummaryResource($summary)
        ]);
    }

    public function byVessel(String $vesselId, Request $request): JsonResponse
    {
        $query = DailySummary::with(['vessel'])
            ->where('vessel_id', $vesselId);

        if ($request->filled('date_from')) {
            $query->whereDate('summary_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('summary_date', '<=', $request->date_to);
        }

        $summaries = $query->orderBy('summary_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => DailySummaryResource::collection($summaries)
        ]);
    }

    public function byDate(String $date, Request $request): JsonResponse
    {
        $query = DailySummary::with(['vessel'])
            ->whereDate('summary_date', $date);

        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        $summaries = $query->orderBy('vessel_id')->get();

        return response()->json([
            'success' => true,
            'data' => DailySummaryResource::collection($summaries)
        ]);
    }

    public function calculate(Request $request): JsonResponse
    {
        $request->validate([
            'vessel_id' => 'required|exists:vessels,id',
            'summary_date' => 'required|date',
        ]);

        DB::beginTransaction();
        try {
            $vesselId = $request->vessel_id;
            $summaryDate = Carbon::parse($request->summary_date);
            
            // Check if summary already exists
            $existingSummary = DailySummary::where('vessel_id', $vesselId)
                ->whereDate('summary_date', $summaryDate)
                ->first();

            // Calculate production data from well production
            $wellProductionData = WellProduction::where('vessel_id', $vesselId)
                ->whereDate('reading_datetime', $summaryDate)
                ->selectRaw('
                    SUM(oil_rate_bph * test_duration_hours / 24) as total_oil_bbl,
                    SUM(gas_rate_mmscfd * test_duration_hours / 24) as total_gas_mmscf,
                    SUM(water_rate_bph * test_duration_hours / 24) as total_water_bbl,
                    AVG(water_cut_pct) as avg_water_cut_pct
                ')
                ->first();

            // Calculate gas sales data
            $gasSalesData = GasSales::where('vessel_id', $vesselId)
                ->whereDate('sales_date', $summaryDate)
                ->selectRaw('
                    SUM(actual_delivery_mmscf) as gas_export_mmscf
                ')
                ->first();

            // Calculate FPU operations data
            $fpuData = FPUOperations::where('vessel_id', $vesselId)
                ->whereDate('reading_date', $summaryDate)
                ->selectRaw('
                    AVG(fuel_gas_rate_mmscfd) as gas_fuel_mmscf,
                    AVG(flare_hp_rate_mmscfd + flare_lp_rate_mmscfd) as gas_flare_mmscf
                ')
                ->first();

            // Prepare summary data
            $summaryData = [
                'vessel_id' => $vesselId,
                'summary_date' => $summaryDate,
                'total_oil_bbl' => $wellProductionData->total_oil_bbl ?? 0,
                'total_gas_mmscf' => $wellProductionData->total_gas_mmscf ?? 0,
                'total_water_bbl' => $wellProductionData->total_water_bbl ?? 0,
                'water_cut_pct' => $wellProductionData->avg_water_cut_pct ?? 0,
                'gas_export_mmscf' => $gasSalesData->gas_export_mmscf ?? 0,
                'gas_fuel_mmscf' => $fpuData->gas_fuel_mmscf ?? 0,
                'gas_flare_mmscf' => $fpuData->gas_flare_mmscf ?? 0,
                'total_equipment' => 0, // This would need to be calculated from equipment data
                'equipment_running' => 0, // This would need to be calculated from equipment data
                'equipment_availability_pct' => 0, // This would need to be calculated
                'total_pob' => 0, // This would need to be calculated from personnel data
                'safety_incidents' => 0, // This would need to be calculated from safety data
                'calculation_status' => 'calculated',
                'calculated_at' => now(),
            ];

            if ($existingSummary) {
                $existingSummary->update($summaryData);
                $summary = $existingSummary;
                $message = 'Daily summary berhasil diperbarui.';
            } else {
                $summary = DailySummary::create($summaryData);
                $message = 'Daily summary berhasil dihitung dan dibuat.';
            }

            // Log aktivitas
            $this->logCreate($summary, $request, __('activity.daily_summary.calculated', [
                'vessel_name' => $summary->vessel?->name,
                'date' => $summaryDate->format('Y-m-d')
            ]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => new DailySummaryResource($summary->load(['vessel']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung daily summary: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function recalculate(String $id, Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $summary = DailySummary::where('id', $id)->first();
            
            if (!$summary) {
                return response()->json([
                    'success' => false,
                    'message' => 'Daily summary tidak ditemukan.',
                ], 404);
            }

            $vesselId = $summary->vessel_id;
            $summaryDate = $summary->summary_date;
            
            // Recalculate production data from well production
            $wellProductionData = WellProduction::where('vessel_id', $vesselId)
                ->whereDate('reading_datetime', $summaryDate)
                ->selectRaw('
                    SUM(oil_rate_bph * test_duration_hours / 24) as total_oil_bbl,
                    SUM(gas_rate_mmscfd * test_duration_hours / 24) as total_gas_mmscf,
                    SUM(water_rate_bph * test_duration_hours / 24) as total_water_bbl,
                    AVG(water_cut_pct) as avg_water_cut_pct
                ')
                ->first();

            // Recalculate gas sales data
            $gasSalesData = GasSales::where('vessel_id', $vesselId)
                ->whereDate('sales_date', $summaryDate)
                ->selectRaw('
                    SUM(actual_delivery_mmscf) as gas_export_mmscf
                ')
                ->first();

            // Recalculate FPU operations data
            $fpuData = FPUOperations::where('vessel_id', $vesselId)
                ->whereDate('reading_date', $summaryDate)
                ->selectRaw('
                    AVG(fuel_gas_rate_mmscfd) as gas_fuel_mmscf,
                    AVG(flare_hp_rate_mmscfd + flare_lp_rate_mmscfd) as gas_flare_mmscf
                ')
                ->first();

            // Update summary data
            $summary->update([
                'total_oil_bbl' => $wellProductionData->total_oil_bbl ?? 0,
                'total_gas_mmscf' => $wellProductionData->total_gas_mmscf ?? 0,
                'total_water_bbl' => $wellProductionData->total_water_bbl ?? 0,
                'water_cut_pct' => $wellProductionData->avg_water_cut_pct ?? 0,
                'gas_export_mmscf' => $gasSalesData->gas_export_mmscf ?? 0,
                'gas_fuel_mmscf' => $fpuData->gas_fuel_mmscf ?? 0,
                'gas_flare_mmscf' => $fpuData->gas_flare_mmscf ?? 0,
                'calculation_status' => 'recalculated',
                'calculated_at' => now(),
            ]);

            // Log aktivitas
            $this->logUpdate($summary, $request, $summary->getOriginal(), __('activity.daily_summary.recalculated', [
                'vessel_name' => $summary->vessel?->name,
                'date' => $summaryDate->format('Y-m-d')
            ]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Daily summary berhasil dihitung ulang.',
                'data' => new DailySummaryResource($summary->fresh()->load(['vessel']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghitung ulang daily summary: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function stats(Request $request): JsonResponse
    {
        $query = DailySummary::query();

        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('summary_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('summary_date', '<=', $request->date_to);
        }

        $stats = [
            'total_summaries' => $query->count(),
            'total_oil_production' => $query->sum('total_oil_bbl'),
            'total_gas_production' => $query->sum('total_gas_mmscf'),
            'total_water_production' => $query->sum('total_water_bbl'),
            'avg_oil_production' => $query->avg('total_oil_bbl'),
            'avg_gas_production' => $query->avg('total_gas_mmscf'),
            'avg_water_cut' => $query->avg('water_cut_pct'),
            'avg_equipment_availability' => $query->avg('equipment_availability_pct'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}