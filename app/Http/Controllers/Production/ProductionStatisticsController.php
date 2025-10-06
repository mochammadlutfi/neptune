<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Production\WellProductionReading;
use App\Models\Production\GasSalesMetering;
use App\Models\Production\GasCompressionData;
use App\Models\Master\Vessel;
use App\Models\Master\Well;
use App\Models\Master\Equipment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProductionStatisticsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Get production dashboard statistics
     */
    public function dashboard(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $vessel_id = $request->get('vessel_id');
        
        $query = WellProductionReading::whereDate('reading_timestamp', $date);
        if ($vessel_id) {
            $query->where('vessel_id', $vessel_id);
        }

        // Well Production Statistics
        $wellProduction = $query->selectRaw('
            COUNT(*) as total_readings,
            COUNT(DISTINCT well_id) as active_wells,
            SUM(oil_rate_bph) as total_oil_bph,
            SUM(gas_rate_mscfh) as total_gas_mscfh,
            SUM(water_rate_bph) as total_water_bph,
            AVG(wellhead_pressure_psi) as avg_wellhead_pressure,
            SUM(flow_hours) as total_flow_hours,
            SUM(downtime_hours) as total_downtime_hours
        ')->first();

        // Gas Sales Statistics
        $gasSalesQuery = GasSalesMetering::whereDate('reading_timestamp', $date);
        if ($vessel_id) {
            $gasSalesQuery->where('vessel_id', $vessel_id);
        }
        
        $gasSales = $gasSalesQuery->selectRaw('
            COUNT(*) as total_sales_records,
            SUM(actual_delivery_mmscf) as total_delivery_mmscf,
            SUM(nomination_mmscf) as total_nomination_mmscf,
            AVG(heating_value_btu_scf) as avg_heating_value,
            COUNT(CASE WHEN buyer_confirmed = 1 THEN 1 END) as confirmed_sales
        ')->first();

        // Compression Statistics
        $compressionQuery = GasCompressionData::whereDate('reading_timestamp', $date);
        if ($vessel_id) {
            $compressionQuery->where('vessel_id', $vessel_id);
        }
        
        $compression = $compressionQuery->selectRaw('
            COUNT(*) as total_readings,
            COUNT(DISTINCT equipment_id) as active_compressors,
            SUM(throughput_mmscfd) as total_throughput_mmscfd,
            AVG(efficiency_percent) as avg_efficiency,
            SUM(power_consumption_kw) as total_power_kw,
            COUNT(CASE WHEN operating_status = "Running" THEN 1 END) as running_compressors
        ')->first();

        // Calculate derived metrics
        $totalLiquid = ($wellProduction->total_oil_bph ?? 0) + ($wellProduction->total_water_bph ?? 0);
        $waterCut = $totalLiquid > 0 ? (($wellProduction->total_water_bph ?? 0) / $totalLiquid) * 100 : 0;
        $uptimePercent = ($wellProduction->total_flow_hours + $wellProduction->total_downtime_hours) > 0 
            ? (($wellProduction->total_flow_hours ?? 0) / (($wellProduction->total_flow_hours ?? 0) + ($wellProduction->total_downtime_hours ?? 0))) * 100 
            : 0;

        $salesVariance = ($gasSales->total_nomination_mmscf ?? 0) > 0 
            ? ((($gasSales->total_delivery_mmscf ?? 0) - ($gasSales->total_nomination_mmscf ?? 0)) / ($gasSales->total_nomination_mmscf ?? 0)) * 100 
            : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'vessel_id' => $vessel_id,
                'well_production' => [
                    'total_readings' => (int) $wellProduction->total_readings,
                    'active_wells' => (int) $wellProduction->active_wells,
                    'total_oil_bph' => number_format($wellProduction->total_oil_bph ?? 0, 2),
                    'total_gas_mscfh' => number_format($wellProduction->total_gas_mscfh ?? 0, 2),
                    'total_water_bph' => number_format($wellProduction->total_water_bph ?? 0, 2),
                    'total_liquid_bph' => number_format($totalLiquid, 2),
                    'water_cut_percent' => number_format($waterCut, 1),
                    'avg_wellhead_pressure_psi' => number_format($wellProduction->avg_wellhead_pressure ?? 0, 1),
                    'uptime_percent' => number_format($uptimePercent, 1),
                ],
                'gas_sales' => [
                    'total_sales_records' => (int) $gasSales->total_sales_records,
                    'total_delivery_mmscf' => number_format($gasSales->total_delivery_mmscf ?? 0, 3),
                    'total_nomination_mmscf' => number_format($gasSales->total_nomination_mmscf ?? 0, 3),
                    'variance_percent' => number_format($salesVariance, 2),
                    'avg_heating_value_btu_scf' => number_format($gasSales->avg_heating_value ?? 0, 2),
                    'confirmed_sales_percent' => $gasSales->total_sales_records > 0 
                        ? number_format((($gasSales->confirmed_sales ?? 0) / $gasSales->total_sales_records) * 100, 1) 
                        : '0.0',
                ],
                'compression' => [
                    'total_readings' => (int) $compression->total_readings,
                    'active_compressors' => (int) $compression->active_compressors,
                    'running_compressors' => (int) $compression->running_compressors,
                    'total_throughput_mmscfd' => number_format($compression->total_throughput_mmscfd ?? 0, 2),
                    'avg_efficiency_percent' => number_format($compression->avg_efficiency ?? 0, 1),
                    'total_power_kw' => number_format($compression->total_power_kw ?? 0, 1),
                    'availability_percent' => $compression->active_compressors > 0 
                        ? number_format((($compression->running_compressors ?? 0) / $compression->active_compressors) * 100, 1) 
                        : '0.0',
                ]
            ]
        ]);
    }

    /**
     * Get wells production summary
     */
    public function wellsSummary(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $vessel_id = $request->get('vessel_id');

        $query = WellProductionReading::with(['well', 'vessel'])
            ->whereDate('reading_timestamp', $date);
        
        if ($vessel_id) {
            $query->where('vessel_id', $vessel_id);
        }

        $wellsSummary = $query->selectRaw('
            well_id,
            vessel_id,
            COUNT(*) as readings_count,
            SUM(oil_rate_bph) as total_oil_bph,
            SUM(gas_rate_mscfh) as total_gas_mscfh,
            SUM(water_rate_bph) as total_water_bph,
            AVG(wellhead_pressure_psi) as avg_wellhead_pressure,
            SUM(flow_hours) as total_flow_hours,
            SUM(downtime_hours) as total_downtime_hours,
            MAX(reading_timestamp) as latest_reading
        ')
        ->groupBy('well_id', 'vessel_id')
        ->get()
        ->map(function ($item) {
            $totalLiquid = $item->total_oil_bph + $item->total_water_bph;
            $waterCut = $totalLiquid > 0 ? ($item->total_water_bph / $totalLiquid) * 100 : 0;
            $uptime = ($item->total_flow_hours + $item->total_downtime_hours) > 0 
                ? ($item->total_flow_hours / ($item->total_flow_hours + $item->total_downtime_hours)) * 100 
                : 0;

            return [
                'well_id' => $item->well_id,
                'well_name' => $item->well?->name,
                'well_code' => $item->well?->code,
                'vessel_name' => $item->vessel?->name,
                'readings_count' => $item->readings_count,
                'total_oil_bph' => number_format($item->total_oil_bph, 2),
                'total_gas_mscfh' => number_format($item->total_gas_mscfh, 2),
                'total_water_bph' => number_format($item->total_water_bph, 2),
                'water_cut_percent' => number_format($waterCut, 1),
                'avg_wellhead_pressure_psi' => number_format($item->avg_wellhead_pressure, 1),
                'uptime_percent' => number_format($uptime, 1),
                'latest_reading' => Carbon::parse($item->latest_reading)->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $wellsSummary
        ]);
    }

    /**
     * Get gas sales summary
     */
    public function gasSalesSummary(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $vessel_id = $request->get('vessel_id');

        $query = GasSalesMetering::with(['vessel'])
            ->whereDate('reading_timestamp', $date);
        
        if ($vessel_id) {
            $query->where('vessel_id', $vessel_id);
        }

        $salesSummary = $query->selectRaw('
            vessel_id,
            buyer_name,
            COUNT(*) as records_count,
            SUM(nomination_mmscf) as total_nomination_mmscf,
            SUM(actual_delivery_mmscf) as total_delivery_mmscf,
            SUM(variance_mmscf) as total_variance_mmscf,
            AVG(heating_value_btu_scf) as avg_heating_value,
            COUNT(CASE WHEN buyer_confirmed = 1 THEN 1 END) as confirmed_count
        ')
        ->groupBy('vessel_id', 'buyer_name')
        ->get()
        ->map(function ($item) {
            $variancePercent = $item->total_nomination_mmscf > 0 
                ? ($item->total_variance_mmscf / $item->total_nomination_mmscf) * 100 
                : 0;
            $confirmationRate = $item->records_count > 0 
                ? ($item->confirmed_count / $item->records_count) * 100 
                : 0;

            return [
                'vessel_name' => $item->vessel?->name,
                'buyer_name' => $item->buyer_name,
                'records_count' => $item->records_count,
                'total_nomination_mmscf' => number_format($item->total_nomination_mmscf, 3),
                'total_delivery_mmscf' => number_format($item->total_delivery_mmscf, 3),
                'total_variance_mmscf' => number_format($item->total_variance_mmscf, 3),
                'variance_percent' => number_format($variancePercent, 2),
                'avg_heating_value_btu_scf' => number_format($item->avg_heating_value, 2),
                'confirmation_rate_percent' => number_format($confirmationRate, 1),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $salesSummary
        ]);
    }

    /**
     * Get compression summary
     */
    public function compressionSummary(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $vessel_id = $request->get('vessel_id');

        $query = GasCompressionData::with(['vessel', 'equipment'])
            ->whereDate('reading_timestamp', $date);
        
        if ($vessel_id) {
            $query->where('vessel_id', $vessel_id);
        }

        $compressionSummary = $query->selectRaw('
            equipment_id,
            vessel_id,
            COUNT(*) as readings_count,
            SUM(throughput_mmscfd) as total_throughput_mmscfd,
            AVG(efficiency_percent) as avg_efficiency_percent,
            SUM(power_consumption_kw) as total_power_kw,
            AVG(compression_ratio) as avg_compression_ratio,
            COUNT(CASE WHEN operating_status = "Running" THEN 1 END) as running_count,
            MAX(reading_timestamp) as latest_reading
        ')
        ->groupBy('equipment_id', 'vessel_id')
        ->get()
        ->map(function ($item) {
            $availability = $item->readings_count > 0 
                ? ($item->running_count / $item->readings_count) * 100 
                : 0;

            return [
                'equipment_id' => $item->equipment_id,
                'equipment_tag' => $item->equipment?->tag,
                'equipment_name' => $item->equipment?->name,
                'vessel_name' => $item->vessel?->name,
                'readings_count' => $item->readings_count,
                'total_throughput_mmscfd' => number_format($item->total_throughput_mmscfd, 2),
                'avg_efficiency_percent' => number_format($item->avg_efficiency_percent, 1),
                'total_power_kw' => number_format($item->total_power_kw, 1),
                'avg_compression_ratio' => number_format($item->avg_compression_ratio, 2),
                'availability_percent' => number_format($availability, 1),
                'latest_reading' => Carbon::parse($item->latest_reading)->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $compressionSummary
        ]);
    }

    /**
     * Get vessel summary
     */
    public function vesselSummary(Request $request, string $vesselId): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        
        $vessel = Vessel::find($vesselId);
        if (!$vessel) {
            return response()->json([
                'success' => false,
                'message' => 'Vessel tidak ditemukan.',
            ], 404);
        }

        // Get all statistics for this vessel
        $dashboardStats = $this->dashboard(new Request(['date' => $date, 'vessel_id' => $vesselId]));
        $wellsStats = $this->wellsSummary(new Request(['date' => $date, 'vessel_id' => $vesselId]));
        $salesStats = $this->gasSalesSummary(new Request(['date' => $date, 'vessel_id' => $vesselId]));
        $compressionStats = $this->compressionSummary(new Request(['date' => $date, 'vessel_id' => $vesselId]));

        return response()->json([
            'success' => true,
            'data' => [
                'vessel' => [
                    'id' => $vessel->id,
                    'name' => $vessel->name,
                    'code' => $vessel->code,
                    'type' => $vessel->type,
                ],
                'date' => $date,
                'dashboard' => $dashboardStats->getData()->data,
                'wells' => $wellsStats->getData()->data,
                'gas_sales' => $salesStats->getData()->data,
                'compression' => $compressionStats->getData()->data,
            ]
        ]);
    }

    /**
     * Get daily summary
     */
    public function dailySummary(Request $request, string $date): JsonResponse
    {
        // Validate date format
        try {
            Carbon::createFromFormat('Y-m-d', $date);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Format tanggal tidak valid. Gunakan format: Y-m-d',
            ], 422);
        }

        $vessel_id = $request->get('vessel_id');
        
        $dashboardStats = $this->dashboard(new Request(['date' => $date, 'vessel_id' => $vessel_id]));

        return $dashboardStats;
    }

    /**
     * Get monthly summary
     */
    public function monthlySummary(Request $request, string $year, string $month): JsonResponse
    {
        // Validate year and month
        if (!is_numeric($year) || !is_numeric($month) || $month < 1 || $month > 12) {
            return response()->json([
                'success' => false,
                'message' => 'Format tahun atau bulan tidak valid.',
            ], 422);
        }

        $vessel_id = $request->get('vessel_id');
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        // Well Production Monthly Statistics
        $wellQuery = WellProductionReading::whereBetween('reading_timestamp', [$startDate, $endDate]);
        if ($vessel_id) {
            $wellQuery->where('vessel_id', $vessel_id);
        }

        $monthlyWellProduction = $wellQuery->selectRaw('
            DATE(reading_timestamp) as date,
            COUNT(*) as total_readings,
            COUNT(DISTINCT well_id) as active_wells,
            SUM(oil_rate_bph) as total_oil_bph,
            SUM(gas_rate_mscfh) as total_gas_mscfh,
            SUM(water_rate_bph) as total_water_bph,
            AVG(wellhead_pressure_psi) as avg_wellhead_pressure
        ')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        // Gas Sales Monthly Statistics
        $salesQuery = GasSalesMetering::whereBetween('reading_timestamp', [$startDate, $endDate]);
        if ($vessel_id) {
            $salesQuery->where('vessel_id', $vessel_id);
        }

        $monthlyGasSales = $salesQuery->selectRaw('
            DATE(reading_timestamp) as date,
            SUM(actual_delivery_mmscf) as total_delivery_mmscf,
            SUM(nomination_mmscf) as total_nomination_mmscf,
            AVG(heating_value_btu_scf) as avg_heating_value
        ')
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'period' => [
                    'year' => (int) $year,
                    'month' => (int) $month,
                    'month_name' => $startDate->format('F'),
                    'start_date' => $startDate->format('Y-m-d'),
                    'end_date' => $endDate->format('Y-m-d'),
                ],
                'vessel_id' => $vessel_id,
                'daily_well_production' => $monthlyWellProduction,
                'daily_gas_sales' => $monthlyGasSales,
            ]
        ]);
    }
}