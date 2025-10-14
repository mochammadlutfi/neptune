<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\Production\SalesGasMeteringRequest;
use App\Http\Resources\Production\SalesGasMeteringDailyResource;
use App\Http\Resources\Production\SalesGasMeteringHourlyResource;
use App\Models\Production\SalesGasMeteringDaily;
use App\Models\Production\SalesGasMeteringDailyFlowrate;
use App\Models\Production\SalesGasMeteringHourly;
use App\Models\Production\SalesGasMeteringHourlyFlowrate;


use App\Services\SalesGasMeteringService;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Traits\ActivityLogger;
use App\Services\SequenceGenerator;

class SalesGasMeteringController extends Controller
{
    use ActivityLogger;

    public function __construct(SalesGasMeteringService $service)
    {
        $this->middleware('auth');
        $this->service = $service;
    }

    public function index(Request $request): JsonResponse
    {
        if($request->type == 'hourly'){
            $query = SalesGasMeteringHourly::with(['vessel', 'createdBy']);
        }else{
            $query = SalesGasMeteringDaily::with(['vessel']);
        }


        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('date', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['date', 'status', 'total_nomination'];
        $sort = $request->get('sort', 'date');
        $sortDir = $request->get('sortDir', 'asc');
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('date', 'asc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);
        
        if ($request->filled('page')) {
            $data = $query->paginate($limit);
            return response()->json([
                'data' => $request->type == 'hourly' ? SalesGasMeteringHourlyResource::collection($data->items()) : SalesGasMeteringDailyResource::collection($data->items()),
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
                'data' => $request->type == 'hourly' ? SalesGasMeteringHourlyResource::collection($data) : SalesGasMeteringDailyResource::collection($data)
            ]);
        }       
    }

    public function store(SalesGasMeteringRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $user = auth()->user();

            $data = new SalesGasMeteringHourly();
            $data->date = $validatedData['date'];
            $data->time = $validatedData['time'];
            $data->vessel_id = $validatedData['vessel_id'];
            $data->pressure_psig = $validatedData['pressure_psig'];
            $data->temperature_f = $validatedData['temperature_f'];
            $data->h2o_lb_mmscf = $validatedData['h2o_lb_mmscf'];
            $data->co2_mol_pct = $validatedData['co2_mol_pct'];
            $data->ghv = $validatedData['ghv'];
            $data->specific_gravity = $validatedData['specific_gravity'];
            $data->ejgp_pressure_psig = $validatedData['ejgp_pressure_psig'];
            $data->hcdp = json_encode($validatedData['hcdp']);
            $data->total_flow_rate = $validatedData['total_flow_rate'];
            $data->status = 'draft';
            $data->created_uid = $user->id;
            $data->remarks = $validatedData['remarks'];
            $data->save();

            $data->flowrates()->saveMany(
                array_map(function($line) use ($data) {
                    return new SalesGasMeteringHourlyFlowrate([
                        'vessel_id' => $data->vessel_id,
                        'buyer_id' => $line['buyer_id'],
                        'primary_stream' => $line['primary_stream'],
                        'backup_stream' => $line['backup_stream'],
                        'gas_sales_metering_hourly_id' => $data->id,
                    ]);
                }, $validatedData['flowrates'])
            );
            
            
            $this->logCreate($data, $request, __('activity.sales_gas_metering.created', ['id' => $data->id]));

            DB::commit();
            $this->service->updateDailySummary($data->vessel_id, $data->date);
            
            return response()->json([
                'success' => true,
                'message' => 'Sales Gas Metering berhasil dibuat.',
                'data' => new SalesGasMeteringHourlyResource($data)
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat vessel: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $data = SalesGasMeteringHourly::with(['vessel', 'createdBy', 'lines' => function($q){
            return $q->with('buyer');
        }])->where('id', $id)->first();
        
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Sales Gas Metering tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new SalesGasMeteringResource($data)
        ]);
    }

    public function update(SalesGasMeteringRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = SalesGasMetering::where('id', $id)->first();
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales Gas Metering tidak ditemukan.',
                ], 404);
            }
            
            $validatedData = $request->validated();
            $user = auth()->user();

            $data->date = $validatedData['date'];
            $data->vessel_id = $validatedData['vessel_id'];
            $data->total = $validatedData['total'];
            $data->created_uid = $user->id;
            $data->remarks = $validatedData['remarks'];
            $data->save();
            
            // Delete existing lines
            $data->lines()->delete();

            // Create new lines
            $data->lines()->saveMany(
                array_map(function($line) use ($data) {
                    return new SalesGasMeteringLine([
                        'buyer_id' => $line['buyer_id'],
                        'allocation' => $line['allocation'],
                        'allocation_id' => $data->id,
                    ]);
                }, $validatedData['lines'])
            );
            
            
            $originalAttributes = $data->getOriginal();
            $this->logUpdate($data, $request, $originalAttributes, __('activity.sales_gas_allocation.updated', ['id' => $data->id]));   
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sales Gas Metering berhasil diupdate.',
                'data' => new SalesGasMeteringResource($data->fresh())
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate Sales Gas Metering: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(Request $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = SalesGasMetering::where('id', $id)->first();
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales Gas Metering tidak ditemukan.',
                ], 404);
            }
            
            $dataName = $data->name;
            // Log aktivitas delete sebelum menghapus untuk menangkap atribut model dan request
            $this->logDelete($data, $request, __('activity.sales_gas_allocation.deleted', ['id' => $data->id]));
            $data->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Sales Gas Metering '{$dataName}' berhasil dihapus.",    
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Sales Gas Metering: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get hourly detail for specific date with daily summary
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function hourlyDetail(Request $request): JsonResponse
    {
        try {
            $vesselId = $request->input('vessel_id');
            $date = $request->input('date');
            
            if (!$vesselId || !$date) {
                return response()->json([
                    'success' => false,
                    'message' => 'vessel_id and date are required',
                ], 400);
            }

            // Get daily summary
            $dailySummary = SalesGasMeteringDaily::where('vessel_id', $vesselId)
                ->whereDate('date', $date)
                ->with('vessel')
                ->first();

            // Get all hourly readings for this date
            $hourlyReadings = SalesGasMeteringHourly::where('vessel_id', $vesselId)
                ->whereDate('date', $date)
                ->with(['flowrates' => function($query) {
                    $query->with('buyer');
                }, 'createdBy'])
                ->orderBy('time', 'asc')
                ->get();
            // Generate full 24 hours array with placeholders for missing hours
            $fullHourlyData = $this->generateFullHourlyArray($hourlyReadings, $date);

            // Calculate missing hours
            $missingHours = $this->calculateMissingHours($hourlyReadings);

            // Calculate completed hours
            $completedHours = 24 - count($missingHours);

            return response()->json([
                'daily_summary' => $dailySummary ? new SalesGasMeteringDailyResource($dailySummary) : null,
                'hourly_data' => $fullHourlyData,
                'completed_hours' => $completedHours,
                'missing_hours' => $missingHours,
                'data_completeness_pct' => round(($completedHours / 24) * 100, 2),
            ]);
        } catch (\Exception $e) {
            dd($e);
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch hourly detail: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Generate full 24 hours array with placeholders for missing hours
     * 
     * @param Collection $hourlyReadings
     * @param string $date
     * @return array
     */
    private function generateFullHourlyArray($hourlyReadings, $date): array
    {
        $fullHourly = [];
        
        // Create array indexed by time
        $hourlyByTime = $hourlyReadings->keyBy('time');
        // Generate 24 hours
        for ($hour = 1; $hour <= 24; $hour++) {
            $timeStr = sprintf('%02d:00:00', $hour);
            
            if (isset($hourlyByTime[$timeStr])) {
                // Hour exists, use actual data
                $fullHourly[] = new SalesGasMeteringHourlyResource($hourlyByTime[$timeStr]);
            } else {
                // Hour missing, create placeholder
                $fullHourly[] = [
                    'id' => null,
                    'time' => substr($timeStr, 0, 5), // HH:MM format
                    'date' => $date,
                    'pressure_psig' => null,
                    'temperature_f' => null,
                    'h2o_lb_mmscf' => null,
                    'co2_mol_pct' => null,
                    'ghv' => null,
                    'specific_gravity' => null,
                    'ejgp_pressure_psig' => null,
                    'total_flow_rate' => null,
                    'status' => 'missing',
                ];
            }
        }
        
        return $fullHourly;
    }

    /**
     * Calculate missing hours
     * 
     * @param Collection $hourlyReadings
     * @return array
     */
    private function calculateMissingHours($hourlyReadings): array
    {
        $existingHours = $hourlyReadings->pluck('time')->map(function($time) {
            return substr($time, 0, 5); // Convert to HH:MM format
        })->toArray();
        
        $missingHours = [];
        
        for ($hour = 1; $hour <= 24; $hour++) {
            $timeStr = sprintf('%02d:00', $hour);
            if (!in_array($timeStr, $existingHours)) {
                $missingHours[] = $timeStr;
            }
        }
        
        return $missingHours;
    }

    /**
     * Approve daily summary
     * 
     * @param Request $request
     * @param int $dailyId
     * @return JsonResponse
     */
    public function approveDailySummary(Request $request, int $dailyId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = auth()->user();
            
            $daily = $this->service->approveDailySummary($dailyId, $user->id);
            
            $this->logUpdate(
                $daily, 
                $request, 
                $daily->getOriginal(),
                __('activity.sales_gas_metering.approved', ['id' => $daily->id])
            );
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Daily summary approved successfully',
                'data' => new SalesGasMeteringDailyResource($daily)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve daily summary: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Lock daily summary
     * 
     * @param Request $request
     * @param int $dailyId
     * @return JsonResponse
     */
    public function lockDailySummary(Request $request, int $dailyId): JsonResponse
    {
        DB::beginTransaction();
        try {
            $daily = $this->service->lockDailySummary($dailyId);
            
            $this->logUpdate(
                $daily, 
                $request, 
                $daily->getOriginal(),
                __('activity.sales_gas_metering.locked', ['id' => $daily->id])
            );
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Daily summary locked successfully',
                'data' => new SalesGasMeteringDailyResource($daily)
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to lock daily summary: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get daily summary statistics (for dashboard/analytics)
     * 
     * @param Request $request
     * @return JsonResponse
     */
    public function dailyStatistics(Request $request): JsonResponse
    {
        try {
            $vesselId = $request->input('vessel_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date', now()->format('Y-m-d'));
            
            $query = SalesGasMeteringDaily::where('vessel_id', $vesselId);
            
            if ($startDate) {
                $query->whereDate('date', '>=', $startDate);
            }
            
            $query->whereDate('date', '<=', $endDate);
            
            $statistics = $query->select([
                    DB::raw('COUNT(*) as total_days'),
                    DB::raw('SUM(CASE WHEN data_completeness_pct = 100 THEN 1 ELSE 0 END) as complete_days'),
                    DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved_days'),
                    DB::raw('SUM(CASE WHEN status = "locked" THEN 1 ELSE 0 END) as locked_days'),
                    DB::raw('AVG(pressure_psig) avg_pressure'),
                    DB::raw('AVG(temperature_f) as avg_temperature'),
                    DB::raw('AVG(total_flow_rate) as avg_flow_rate'),
                    DB::raw('AVG(data_completeness_pct) as avg_completeness'),
                ])
                ->first();
            
            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics: ' . $e->getMessage(),
            ], 500);
        }
    }
}
