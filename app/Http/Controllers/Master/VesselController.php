<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\VesselRequest;
use App\Http\Resources\Master\VesselResource;
use App\Models\Master\Vessel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class VesselController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Vessel::query();

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('client_name')) {
            $query->where('client_name', 'LIKE', '%' . $request->client_name . '%');
        }

        if ($request->filled('client_oim')) {
            $query->where('client_oim', 'LIKE', '%' . $request->client_oim . '%');
        }

        if($request->filled('allowed_ids')){
            $query->whereIn('id', $request->allowed_ids);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', '%' . $search . '%')
                  ->orWhere('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('client_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('client_oim', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['code', 'name', 'type', 'operator', 'field_name', 'status'];
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
            $vessels = $query->with('oim')->paginate($limit);
            return response()->json([
                'data' => VesselResource::collection($vessels->items()),
                'meta' => [
                    'current_page' => $vessels->currentPage(),
                    'last_page' => $vessels->lastPage(),
                    'per_page' => $vessels->perPage(),
                    'total' => $vessels->total(),
                    'from' => $vessels->firstItem(),
                    'to' => $vessels->lastItem(),
                ]
            ]);
        } else {
            $vessels = $query->get();
            return response()->json([
                'data' => VesselResource::collection($vessels)
            ]);
        }
    }

    public function store(VesselRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $vessel = Vessel::create($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Vessel berhasil dibuat.',
                'data' => new VesselResource($vessel)
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
        $vessel = Vessel::where('id', $id)->first();
        
        if (!$vessel) {
            return response()->json([
                'success' => false,
                'message' => 'Vessel tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new VesselResource($vessel)
        ]);
    }

    public function update(VesselRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $vessel = Vessel::where('id', $id)->first();
            
            if (!$vessel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vessel tidak ditemukan.',
                ], 404);
            }
            
            $validatedData = $request->validated();
            $vessel->update($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Vessel berhasil diupdate.',
                'data' => new VesselResource($vessel->fresh())
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate vessel: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $vessel = Vessel::where('id', $id)->first();
            
            if (!$vessel) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vessel tidak ditemukan.',
                ], 404);
            }
            
            // Check dependencies before deletion
            $wellsCount = $vessel->wells()->count();
            $equipmentCount = $vessel->equipment()->count();
            
            if ($wellsCount > 0 || $equipmentCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat menghapus vessel yang memiliki {$wellsCount} wells dan {$equipmentCount} equipment.",
                ], 422);
            }

            $vesselName = $vessel->name;
            $vessel->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Vessel '{$vesselName}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus vessel: ' . $e->getMessage(),
            ], 422);
        }
    }
}
