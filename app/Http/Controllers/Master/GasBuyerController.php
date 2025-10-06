<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\GasBuyerRequest;
use App\Http\Resources\Master\GasBuyerResource;
use App\Models\Master\GasBuyer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class GasBuyerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Menampilkan daftar data dengan filter dan pencarian.
     */
    public function index(Request $request): JsonResponse
    {
        $query = GasBuyer::query();

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Search functionality
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('code', 'LIKE', '%' . $search . '%')
                  ->orWhere('type', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['code', 'name', 'type', 'grade', 'is_active', 'created_at'];
        $sort = $request->get('sort', 'created_at');
        $sortDir = $request->get('sortDir', 'desc');
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);
        
        if ($request->filled('page')) {
            $data = $query->paginate($limit);
            return response()->json([
                'data' => GasBuyerResource::collection($data->items()),
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
                'data' => GasBuyerResource::collection($data)
            ]);
        }
    }

    /**
     * Menyimpan data baru ke database.
     */
    public function store(GasBuyerRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            
            $gasBuyer = GasBuyer::create($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Gas buyer berhasil dibuat.',
                'data' => new GasBuyerResource($gasBuyer)
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat fuel type: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Menampilkan detail data berdasarkan ID.
     */
    public function show(String $id): JsonResponse
    {
        $data = GasBuyer::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => new GasBuyerResource($data)
        ]);
    }

    /**
     * Mengupdate data.
     */
    public function update(GasBuyerRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $gasBuyer = GasBuyer::findOrFail($id);
            $validatedData = $request->validated();
            
            $gasBuyer->update($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Gas buyer berhasil diupdate.',
                'data' => new GasBuyerResource($gasBuyer->fresh())
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate fuel type: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Menghapus data dari database.
     */
    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $gasBuyer = GasBuyer::findOrFail($id);
            
            $gasBuyerName = $gasBuyer->name;
            $gasBuyer->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Gas buyer '{$gasBuyerName}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus fuel type: ' . $e->getMessage(),
            ], 422);
        }
    }
}
