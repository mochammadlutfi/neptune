<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\Production\GasSalesRequest;
use App\Http\Resources\Production\GasSalesResource;
use App\Models\Production\GasSales;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class GasSalesController extends Controller
{
    use ActivityLogger;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = GasSales::with(['vessel', 'gasBuyer', 'recordedBy']);

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('gas_buyer_id')) {
            $query->where('gas_buyer_id', $request->gas_buyer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sales_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sales_date', '<=', $request->date_to);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('vessel', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('gasBuyer', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('code', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('recordedBy', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['sales_date', 'vessel_id', 'gas_buyer_id', 'actual_delivery_mmscf', 'nomination_mmscf'];
        $sort = $request->get('sort', 'sales_date');
        $sortDir = $request->get('sortDir', 'desc');
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('sales_date', 'desc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);
        
        if ($request->filled('page')) {
            $readings = $query->paginate($limit);
            return response()->json([
                'data' => GasSalesResource::collection($readings->items()),
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
                'data' => GasSalesResource::collection($readings)
            ]);
        }
    }

    public function store(GasSalesRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $validatedData = $request->validated();
            
            $data = new GasSales();
            $data->vessel_id = $user->vessel_id;
            $data->gas_buyer_id = $validatedData['gas_buyer_id'];
            $data->sales_date = Carbon::parse($validatedData['sales_date']);
            $data->export_pressure_psi = $validatedData['export_pressure_psi'] ?? null;
            $data->export_temp_f = $validatedData['export_temp_f'] ?? null;
            $data->actual_delivery_mmscf = $validatedData['actual_delivery_mmscf'] ?? null;
            $data->nomination_mmscf = $validatedData['nomination_mmscf'] ?? null;
            $data->heating_value_btu = $validatedData['heating_value_btu'] ?? null;
            $data->specific_gravity = $validatedData['specific_gravity'] ?? null;
            $data->h2s_content_ppm = $validatedData['h2s_content_ppm'] ?? null;
            $data->co2_content_pct = $validatedData['co2_content_pct'] ?? null;
            $data->moisture_content_ppm = $validatedData['moisture_content_ppm'] ?? null;
            $data->recorded_by = $user->id;
            $data->save();

            // Log aktivitas create
            $this->logCreate($data, $request, __('activity.gas_sales.created', ['buyer_name' => $data->gasBuyer?->name]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Gas sales reading berhasil dibuat.',
                'data' => new GasSalesResource($data->load(['vessel', 'gasBuyer', 'recordedBy']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat gas sales reading: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $reading = GasSales::with(['vessel', 'gasBuyer', 'recordedBy'])->where('id', $id)->first();
        
        if (!$reading) {
            return response()->json([
                'success' => false,
                'message' => 'Gas sales reading tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new GasSalesResource($reading)
        ]);
    }

    public function update(GasSalesRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = GasSales::where('id', $id)->first();
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gas sales reading tidak ditemukan.',
                ], 404);
            }
            
            $validatedData = $request->validated();
            $data->gas_buyer_id = $validatedData['gas_buyer_id'];
            $data->sales_date = Carbon::parse($validatedData['sales_date']);
            $data->export_pressure_psi = $validatedData['export_pressure_psi'] ?? null;
            $data->export_temp_f = $validatedData['export_temp_f'] ?? null;
            $data->actual_delivery_mmscf = $validatedData['actual_delivery_mmscf'] ?? null;
            $data->nomination_mmscf = $validatedData['nomination_mmscf'] ?? null;
            $data->heating_value_btu = $validatedData['heating_value_btu'] ?? null;
            $data->specific_gravity = $validatedData['specific_gravity'] ?? null;
            $data->h2s_content_ppm = $validatedData['h2s_content_ppm'] ?? null;
            $data->co2_content_pct = $validatedData['co2_content_pct'] ?? null;
            $data->moisture_content_ppm = $validatedData['moisture_content_ppm'] ?? null;
            
            // Simpan data asli untuk logging
            $originalAttributes = $data->getOriginal();
            $data->save();

            // Log aktivitas update
            $this->logUpdate($data, $request, $originalAttributes, __('activity.gas_sales.updated', ['buyer_name' => $data->gasBuyer?->name]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Gas sales reading berhasil diupdate.',
                'data' => new GasSalesResource($data->fresh()->load(['vessel', 'gasBuyer', 'recordedBy']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate gas sales reading: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $reading = GasSales::where('id', $id)->first();
            
            if (!$reading) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gas sales reading tidak ditemukan.',
                ], 404);
            }
            
            $readingInfo = "Reading for {$reading->gasBuyer?->name} on {$reading->sales_date}";
            
            // Log aktivitas delete sebelum menghapus
            $this->logDelete($reading, $request, __('activity.gas_sales.deleted', ['buyer_name' => $reading->gasBuyer?->name]));
            
            $reading->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Gas sales reading '{$readingInfo}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus gas sales reading: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function byVessel(String $vesselId, Request $request): JsonResponse
    {
        $query = GasSales::with(['vessel', 'gasBuyer', 'recordedBy'])
            ->where('vessel_id', $vesselId);

        if ($request->filled('date_from')) {
            $query->whereDate('sales_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sales_date', '<=', $request->date_to);
        }

        $readings = $query->orderBy('sales_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => GasSalesResource::collection($readings)
        ]);
    }

    public function byBuyer(String $buyerId, Request $request): JsonResponse
    {
        $query = GasSales::with(['vessel', 'gasBuyer', 'recordedBy'])
            ->where('gas_buyer_id', $buyerId);

        if ($request->filled('date_from')) {
            $query->whereDate('sales_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sales_date', '<=', $request->date_to);
        }

        $readings = $query->orderBy('sales_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => GasSalesResource::collection($readings)
        ]);
    }

    public function latestByVessel(String $vesselId): JsonResponse
    {
        $reading = GasSales::with(['vessel', 'gasBuyer', 'recordedBy'])
            ->where('vessel_id', $vesselId)
            ->orderBy('sales_date', 'desc')
            ->first();

        if (!$reading) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada gas sales reading untuk vessel ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new GasSalesResource($reading)
        ]);
    }

    public function stats(Request $request): JsonResponse
    {
        $query = GasSales::query();

        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('gas_buyer_id')) {
            $query->where('gas_buyer_id', $request->gas_buyer_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('sales_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('sales_date', '<=', $request->date_to);
        }

        $stats = [
            'total_readings' => $query->count(),
            'total_delivery' => $query->sum('actual_delivery_mmscf'),
            'total_nomination' => $query->sum('nomination_mmscf'),
            'avg_delivery' => $query->avg('actual_delivery_mmscf'),
            'avg_nomination' => $query->avg('nomination_mmscf'),
            'avg_heating_value' => $query->avg('heating_value_btu'),
            'avg_export_pressure' => $query->avg('export_pressure_psi'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }
}