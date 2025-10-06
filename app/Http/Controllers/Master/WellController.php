<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\WellRequest;
use App\Http\Resources\Master\WellResource;
use App\Models\Master\Well;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Traits\ActivityLogger;

class WellController extends Controller
{
    use ActivityLogger;
    
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $query = Well::with('vessel');

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->byVessel($request->vessel_id);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('artificial_lift')) {
            $query->where('artificial_lift', $request->artificial_lift);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', '%' . $search . '%')
                  ->orWhere('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('api_number', 'LIKE', '%' . $search . '%')
                  ->orWhere('reservoir_name', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('vessel', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['code', 'name', 'type', 'status'];
        $sort = $request->get('sort', 'code');
        $sortDir = $request->get('sortDir', 'asc');
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('code', 'asc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);
        
        if ($request->filled('page')) {
            $wells = $query->paginate($limit);
            return response()->json([
                'data' => WellResource::collection($wells->items()),
                'meta' => [
                    'current_page' => $wells->currentPage(),
                    'last_page' => $wells->lastPage(),
                    'per_page' => $wells->perPage(),
                    'total' => $wells->total(),
                    'from' => $wells->firstItem(),
                    'to' => $wells->lastItem(),
                ]
            ]);
        } else {
            $wells = $query->get();
            return response()->json([
                'data' => WellResource::collection($wells)
            ]);
        }
    }

    /**
     * Get wells statistics
     */
    public function stats()
    {
        $stats = [
            'total' => Well::count(),
            'active' => Well::where('status', 'Active')->count(),
            'shut_in' => Well::where('status', 'Shut-in')->count(),
            'suspended' => Well::where('status', 'Suspended')->count(),
            'testing' => Well::where('status', 'Testing')->count(),
            'abandoned' => Well::where('status', 'Abandoned')->count()
        ];

        return response()->json($stats);
    }

    /**
     * Export wells data
     */
    public function export(Request $request)
    {
        // Similar filtering logic as index but return CSV/Excel
        // Implementation depends on your export library
    }

    public function store(WellRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            // dd($validatedData);
            $well = Well::create($validatedData);

            $this->logCreate($well, $request, __('activity.well.created', ['well_name' => $well->name]));


            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Well berhasil dibuat.',
                'data' => new WellResource($well->load('vessel'))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat well: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): \Illuminate\Http\JsonResponse
    {
        $well = Well::with('vessel')->where('id', $id)->first();
        
        if (!$well) {
            return response()->json([
                'success' => false,
                'message' => 'Well tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new WellResource($well)
        ]);
    }

    public function update(WellRequest $request, String $id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = Well::where('id', $id)->first();
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Well tidak ditemukan.',
                ], 404);
            }
            
            $validatedData = $request->validated();
            // $data->update($validatedData);
            $data->name = $validatedData['name'];
            $data->type = $validatedData['type'];
            $data->max_oil_rate = $validatedData['max_oil_rate'];
            $data->max_gas_rate = $validatedData['max_gas_rate'];
            $data->status = $validatedData['status'];
            $data->save();
            $originalAttributes = $data->getOriginal();

            $this->logUpdate($data, $request, $originalAttributes, __('activity.well.updated', ['well_name' => $data->name]));
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Well berhasil diupdate.',
                'data' => new WellResource($data->fresh()->load('vessel'))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate well: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Update well status
     */
    public function updateStatus(Request $request, Well $well)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:Active,Shut-in,Suspended,Testing,Abandoned'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();
        try {
            $well->update(['status' => $request->status]);
            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $well
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update well status',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(String $id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();
        try {
            $well = Well::where('id', $id)->first();
            
            if (!$well) {
                return response()->json([
                    'success' => false,
                    'message' => 'Well tidak ditemukan.',
                ], 404);
            }
            
            // Check dependencies before deletion (if any)
            // Add relationship checks here if needed

            $wellName = $well->name;
            $well->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Well '{$wellName}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus well: ' . $e->getMessage(),
            ], 422);
        }
    }
}