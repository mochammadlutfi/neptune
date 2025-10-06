<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\Master\EquipmentRequest;
use App\Http\Resources\Master\EquipmentResource;
use App\Models\Master\Equipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Traits\ActivityLogger;

class EquipmentController extends Controller
{
    use ActivityLogger;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = Equipment::with('vessel');

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_critical')) {
            $query->where('is_critical', $request->is_critical == '1');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'LIKE', '%' . $search . '%')
                  ->orWhere('tag', 'LIKE', '%' . $search . '%')
                  ->orWhere('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('manufacturer', 'LIKE', '%' . $search . '%')
                  ->orWhere('model', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('vessel', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['code', 'tag', 'name', 'type', 'category', 'manufacturer', 'status', 'next_maintenance_date'];
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
            $equipment = $query->paginate($limit);
            return response()->json([
                'data' => EquipmentResource::collection($equipment->items()),
                'meta' => [
                    'current_page' => $equipment->currentPage(),
                    'last_page' => $equipment->lastPage(),
                    'per_page' => $equipment->perPage(),
                    'total' => $equipment->total(),
                    'from' => $equipment->firstItem(),
                    'to' => $equipment->lastItem(),
                ]
            ]);
        } else {
            $equipment = $query->get();
            return response()->json([
                'data' => EquipmentResource::collection($equipment)
            ]);
        }
    }

    public function store(EquipmentRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            // $equipment = Equipment::create($validatedData);
            $equipment = new Equipment();
            $equipment->vessel_id = $validatedData['vessel_id'];
            $equipment->code = $validatedData['code'];
            $equipment->tag = $validatedData['tag'];
            $equipment->name = $validatedData['name'];
            $equipment->type = $validatedData['type'];
            $equipment->category = $validatedData['category'];
            $equipment->manufacturer = $validatedData['manufacturer'];
            $equipment->model = $validatedData['model'];
            $equipment->serial_number = $validatedData['serial_number'];
            $equipment->installation_date = $validatedData['installation_date'];
            $equipment->is_critical = $validatedData['is_critical'];
            $equipment->save();

            DB::commit();
            $this->logCreate($equipment, $request, __('activity.equipment.created', ['equipment_name' => $equipment->name]));
            
            return response()->json([
                'success' => true,
                'message' => 'Equipment berhasil dibuat.',
                'data' => new EquipmentResource($equipment->load('vessel'))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat equipment: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $equipment = Equipment::with('vessel')->where('id', $id)->first();
        
        if (!$equipment) {
            return response()->json([
                'success' => false,
                'message' => 'Equipment tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new EquipmentResource($equipment)
        ]);
    }

    public function update(EquipmentRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $equipment = Equipment::where('id', $id)->first();
            
            if (!$equipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Equipment tidak ditemukan.',
                ], 404);
            }
            
            $validatedData = $request->validated();
            $originalAttributes = $equipment->getOriginal();
            $equipment->update($validatedData);
            
            DB::commit();

            $this->logUpdate($equipment, $request, $originalAttributes, __('activity.equipment.updated', ['equipment_name' => $equipment->name]));

            return response()->json([
                'success' => true,
                'message' => 'Equipment berhasil diupdate.',
                'data' => new EquipmentResource($equipment->fresh()->load('vessel'))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate equipment: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $equipment = Equipment::where('id', $id)->first();
            
            if (!$equipment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Equipment tidak ditemukan.',
                ], 404);
            }
            
            // Check dependencies before deletion
            // Check if this equipment is used as spare for other equipment
            $usedAsSpareCount = Equipment::where('spare_equipment_id', $id)->count();
            
            if ($usedAsSpareCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat menghapus equipment yang digunakan sebagai spare untuk {$usedAsSpareCount} equipment lain.",
                ], 422);
            }

            $equipmentName = $equipment->name;
            $originalAttributes = $equipment->getOriginal();
            $equipment->delete();
            DB::commit();
            
            $this->logDelete($equipment, $request, $originalAttributes, __('activity.equipment.deleted', ['equipment_name' => $equipment->name]));
            
            return response()->json([
                'success' => true,
                'message' => "Equipment '{$equipmentName}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus equipment: ' . $e->getMessage(),
            ], 422);
        }
    }
}
