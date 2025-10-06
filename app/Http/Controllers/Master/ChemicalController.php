<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\ChemicalRequest;
use App\Http\Resources\Master\ChemicalResource;
use App\Models\Master\Chemical;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class ChemicalController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request): JsonResponse
    {
        $query = Chemical::query();

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('hazard_class')) {
            $query->where('hazard_class', $request->hazard_class);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        if ($request->filled('primary_supplier')) {
            $query->where('primary_supplier', 'LIKE', '%' . $request->primary_supplier . '%');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', '%' . $search . '%')
                  ->orWhere('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('trade_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('type', 'LIKE', '%' . $search . '%')
                  ->orWhere('primary_supplier', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['code', 'name', 'trade_name', 'type', 'hazard_class', 'primary_supplier', 'is_active'];
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
            $chemicals = $query->paginate($limit);
            return response()->json([
                'data' => ChemicalResource::collection($chemicals->items()),
                'meta' => [
                    'current_page' => $chemicals->currentPage(),
                    'last_page' => $chemicals->lastPage(),
                    'per_page' => $chemicals->perPage(),
                    'total' => $chemicals->total(),
                    'from' => $chemicals->firstItem(),
                    'to' => $chemicals->lastItem(),
                ]
            ]);
        } else {
            $chemicals = $query->get();
            return response()->json([
                'data' => ChemicalResource::collection($chemicals)
            ]);
        }
    }

    public function store(ChemicalRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            
            $chemical = Chemical::create($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Chemical berhasil dibuat.',
                'data' => new ChemicalResource($chemical)
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat chemical: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $chemical = Chemical::where('id', $id)->first();
        
        if (!$chemical) {
            return response()->json([
                'success' => false,
                'message' => 'Chemical tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new ChemicalResource($chemical)
        ]);
    }

    public function update(ChemicalRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $chemical = Chemical::where('id', $id)->first();
            
            if (!$chemical) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chemical tidak ditemukan.',
                ], 404);
            }
            
            $validatedData = $request->validated();
            
            // Filter empty arrays
            if (isset($validatedData['ppe_requirements'])) {
                $validatedData['ppe_requirements'] = array_filter($validatedData['ppe_requirements']);
            }
            if (isset($validatedData['incompatible_chemicals'])) {
                $validatedData['incompatible_chemicals'] = array_filter($validatedData['incompatible_chemicals']);
            }
            
            $chemical->update($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Chemical berhasil diupdate.',
                'data' => new ChemicalResource($chemical->fresh())
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate chemical: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $chemical = Chemical::where('id', $id)->first();
            
            if (!$chemical) {
                return response()->json([
                    'success' => false,
                    'message' => 'Chemical tidak ditemukan.',
                ], 404);
            }
            
            // Check dependencies before deletion
            // Add checks for chemical usage in operations, inventory, etc.

            $chemicalName = $chemical->name;
            $chemical->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Chemical '{$chemicalName}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus chemical: ' . $e->getMessage(),
            ], 422);
        }
    }
}
