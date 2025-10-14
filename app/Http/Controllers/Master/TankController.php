<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\TankRequest;
use App\Http\Resources\Master\TankResource;
use App\Models\Master\Tank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class TankController extends Controller
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
        $query = Tank::with(['vessel', 'creator']);

        // Apply filters
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        if ($request->filled('vessel_id')) {
            $query->byVessel($request->vessel_id);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        if ($request->filled('is_multiphase')) {
            $query->multiphase($request->boolean('is_multiphase'));
        }

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        // Search functionality
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('code', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['code', 'name', 'type', 'capacity_bbls', 'is_active', 'created_at'];
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
                'data' => TankResource::collection($data->items()),
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
                'data' => TankResource::collection($data)
            ]);
        }
    }

    /**
     * Menyimpan data baru ke database.
     */
    public function store(TankRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            
            // Set created_uid dari authenticated user
            $validatedData['created_uid'] = auth()->id();
            
            $tank = Tank::create($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Tank berhasil dibuat.',
                'data' => new TankResource($tank->load(['vessel', 'creator']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat tank: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Menampilkan detail data berdasarkan ID.
     */
    public function show(String $id): JsonResponse
    {
        $tank = Tank::with(['vessel', 'creator'])->findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => new TankResource($tank)
        ]);
    }

    /**
     * Mengupdate data.
     */
    public function update(TankRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $tank = Tank::findOrFail($id);
            $validatedData = $request->validated();
            
            $tank->update($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Tank berhasil diupdate.',
                'data' => new TankResource($tank->fresh(['vessel', 'creator']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate tank: ' . $e->getMessage(),
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
            $tank = Tank::findOrFail($id);
            
            $tankName = $tank->name;
            $tank->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Tank '{$tankName}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus tank: ' . $e->getMessage(),
            ], 422);
        }
    }
}