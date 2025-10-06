<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\PersonnelPositionRequest;
use App\Http\Resources\Master\PersonnelPositionResource;
use App\Models\Master\PersonnelPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class PersonnelPositionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(Request $request): JsonResponse
    {
        $query = PersonnelPosition::query();

        // Apply filters
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('is_essential')) {
            $query->where('is_essential', $request->is_essential == '1');
        }

        if ($request->filled('is_minimum_manning')) {
            $query->where('is_minimum_manning', $request->is_minimum_manning == '1');
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active == '1');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', '%' . $search . '%')
                  ->orWhere('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('department', 'LIKE', '%' . $search . '%')
                  ->orWhere('level', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['code', 'name', 'department', 'level', 'min_experience_years', 'is_active'];
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
            $positions = $query->paginate($limit);
            return response()->json([
                'data' => PersonnelPositionResource::collection($positions->items()),
                'meta' => [
                    'current_page' => $positions->currentPage(),
                    'last_page' => $positions->lastPage(),
                    'per_page' => $positions->perPage(),
                    'total' => $positions->total(),
                    'from' => $positions->firstItem(),
                    'to' => $positions->lastItem(),
                ]
            ]);
        } else {
            $positions = $query->get();
            return response()->json([
                'data' => PersonnelPositionResource::collection($positions)
            ]);
        }
    }

    public function store(PersonnelPositionRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            
            // Filter empty certificates
            if (isset($validatedData['required_certificates'])) {
                $validatedData['required_certificates'] = array_filter($validatedData['required_certificates']);
            }
            
            $position = PersonnelPosition::create($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Personnel position berhasil dibuat.',
                'data' => new PersonnelPositionResource($position)
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat personnel position: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $position = PersonnelPosition::where('id', $id)->first();
        
        if (!$position) {
            return response()->json([
                'success' => false,
                'message' => 'Personnel position tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new PersonnelPositionResource($position)
        ]);
    }

    public function update(PersonnelPositionRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $position = PersonnelPosition::where('id', $id)->first();
            
            if (!$position) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personnel position tidak ditemukan.',
                ], 404);
            }
            
            $validatedData = $request->validated();
            
            // Filter empty certificates
            if (isset($validatedData['required_certificates'])) {
                $validatedData['required_certificates'] = array_filter($validatedData['required_certificates']);
            }
            
            $position->update($validatedData);

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Personnel position berhasil diupdate.',
                'data' => new PersonnelPositionResource($position->fresh())
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate personnel position: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $position = PersonnelPosition::where('id', $id)->first();
            
            if (!$position) {
                return response()->json([
                    'success' => false,
                    'message' => 'Personnel position tidak ditemukan.',
                ], 404);
            }
            
            // Check dependencies before deletion
            // Add checks for personnel assignments if needed

            $positionName = $position->name;
            $position->delete();
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Personnel position '{$positionName}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus personnel position: ' . $e->getMessage(),
            ], 422);
        }
    }
}
