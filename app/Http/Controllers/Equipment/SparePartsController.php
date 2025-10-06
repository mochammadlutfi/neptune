<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\SparePartsRequest;
use App\Http\Resources\Equipment\SparePartsResource;
use App\Models\Equipment\SpareParts;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class SparePartsController extends Controller
{
    use ActivityLogger;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = SpareParts::with(['vessel', 'equipment']);

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('equipment_id')) {
            $query->where('equipment_id', $request->equipment_id);
        }

        if ($request->filled('part_category')) {
            $query->where('part_category', $request->part_category);
        }

        if ($request->filled('manufacturer')) {
            $query->where('manufacturer', 'LIKE', '%' . $request->manufacturer . '%');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('part_number', 'LIKE', '%' . $search . '%')
                  ->orWhere('part_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('part_description', 'LIKE', '%' . $search . '%')
                  ->orWhere('manufacturer', 'LIKE', '%' . $search . '%')
                  ->orWhere('supplier', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('vessel', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('equipment', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%')
                        ->orWhere('tag', 'LIKE', '%' . $search . '%')
                        ->orWhere('code', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Sorting
        $allowedSorts = ['part_number', 'part_name', 'part_category', 'manufacturer', 'unit_cost', 'created_at'];
        $sort = $request->get('sort', 'part_number');
        $sortDir = $request->get('sortDir', 'asc');

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('part_number', 'asc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);

        if ($request->filled('page')) {
            $spareParts = $query->paginate($limit);
            return response()->json([
                'data' => SparePartsResource::collection($spareParts->items()),
                'meta' => [
                    'current_page' => $spareParts->currentPage(),
                    'last_page' => $spareParts->lastPage(),
                    'per_page' => $spareParts->perPage(),
                    'total' => $spareParts->total(),
                    'from' => $spareParts->firstItem(),
                    'to' => $spareParts->lastItem(),
                ]
            ]);
        } else {
            $spareParts = $query->get();
            return response()->json([
                'data' => SparePartsResource::collection($spareParts)
            ]);
        }
    }

    public function store(SparePartsRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $validatedData = $request->validated();

            $data = new SpareParts();
            $data->vessel_id = $user->vessel_id;
            $data->part_number = $validatedData['part_number'];
            $data->part_name = $validatedData['part_name'];
            $data->part_description = $validatedData['part_description'] ?? null;
            $data->equipment_id = $validatedData['equipment_id'] ?? null;
            $data->part_category = $validatedData['part_category'] ?? null;
            $data->manufacturer = $validatedData['manufacturer'] ?? null;
            $data->supplier = $validatedData['supplier'] ?? null;
            $data->unit_cost = $validatedData['unit_cost'] ?? null;
            $data->currency = $validatedData['currency'] ?? 'USD';
            $data->unit_measure = $validatedData['unit_measure'] ?? 'Each';
            $data->min_stock_level = $validatedData['min_stock_level'] ?? 0;
            $data->reorder_point = $validatedData['reorder_point'] ?? 0;
            $data->save();

            // Log aktivitas create
            $this->logCreate($data, $request, __('activity.spare_parts.created', ['part_name' => $data->part_name]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Spare part berhasil dibuat.',
                'data' => new SparePartsResource($data->load(['vessel', 'equipment']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat spare part: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $sparePart = SpareParts::with(['vessel', 'equipment'])->where('id', $id)->first();

        if (!$sparePart) {
            return response()->json([
                'success' => false,
                'message' => 'Spare part tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new SparePartsResource($sparePart)
        ]);
    }

    public function update(SparePartsRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = SpareParts::where('id', $id)->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spare part tidak ditemukan.',
                ], 404);
            }

            $validatedData = $request->validated();

            // Simpan data asli untuk logging
            $originalAttributes = $data->getOriginal();

            $data->part_number = $validatedData['part_number'];
            $data->part_name = $validatedData['part_name'];
            $data->part_description = $validatedData['part_description'] ?? null;
            $data->equipment_id = $validatedData['equipment_id'] ?? null;
            $data->part_category = $validatedData['part_category'] ?? null;
            $data->manufacturer = $validatedData['manufacturer'] ?? null;
            $data->supplier = $validatedData['supplier'] ?? null;
            $data->unit_cost = $validatedData['unit_cost'] ?? null;
            $data->currency = $validatedData['currency'] ?? 'USD';
            $data->unit_measure = $validatedData['unit_measure'] ?? 'Each';
            $data->min_stock_level = $validatedData['min_stock_level'] ?? 0;
            $data->reorder_point = $validatedData['reorder_point'] ?? 0;
            $data->save();

            // Log aktivitas update
            $this->logUpdate($data, $request, $originalAttributes, __('activity.spare_parts.updated', ['part_name' => $data->part_name]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Spare part berhasil diupdate.',
                'data' => new SparePartsResource($data->fresh()->load(['vessel', 'equipment']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate spare part: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $sparePart = SpareParts::where('id', $id)->first();

            if (!$sparePart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spare part tidak ditemukan.',
                ], 404);
            }

            $partInfo = "{$sparePart->part_number} - {$sparePart->part_name}";

            // Log aktivitas delete sebelum menghapus
            $this->logDelete($sparePart, request(), __('activity.spare_parts.deleted', ['part_name' => $sparePart->part_name]));

            $sparePart->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Spare part '{$partInfo}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus spare part: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get spare parts by vessel
     */
    public function byVessel(Request $request, String $vesselId): JsonResponse
    {
        $query = SpareParts::with(['vessel', 'equipment'])
            ->where('vessel_id', $vesselId);

        if ($request->filled('part_category')) {
            $query->where('part_category', $request->part_category);
        }

        $spareParts = $query->orderBy('part_number', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => SparePartsResource::collection($spareParts)
        ]);
    }

    /**
     * Get spare parts by equipment
     */
    public function byEquipment(String $equipmentId): JsonResponse
    {
        $spareParts = SpareParts::with(['vessel', 'equipment'])
            ->where('equipment_id', $equipmentId)
            ->orderBy('part_number', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => SparePartsResource::collection($spareParts)
        ]);
    }

    /**
     * Get spare parts statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $vessel_id = $request->get('vessel_id');

        $query = SpareParts::with(['vessel', 'equipment']);

        if ($vessel_id) {
            $query->where('vessel_id', $vessel_id);
        }

        // Overall statistics
        $overallStats = $query->selectRaw('
            COUNT(*) as total_parts,
            COUNT(DISTINCT equipment_id) as equipment_covered,
            COUNT(DISTINCT vessel_id) as vessels,
            COUNT(DISTINCT manufacturer) as manufacturers,
            COUNT(DISTINCT supplier) as suppliers,
            AVG(unit_cost) as avg_unit_cost,
            SUM(unit_cost) as total_value
        ')->first();

        // Category breakdown
        $categoryStats = $query->selectRaw('
            part_category,
            COUNT(*) as count,
            AVG(unit_cost) as avg_cost,
            SUM(unit_cost) as total_value
        ')->groupBy('part_category')->get();

        // Manufacturer breakdown
        $manufacturerStats = $query->selectRaw('
            manufacturer,
            COUNT(*) as count,
            AVG(unit_cost) as avg_cost
        ')
        ->whereNotNull('manufacturer')
        ->groupBy('manufacturer')
        ->orderBy('count', 'desc')
        ->limit(10)
        ->get();

        // Top expensive parts
        $expensiveParts = $query->orderBy('unit_cost', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($item) {
                return [
                    'part_number' => $item->part_number,
                    'part_name' => $item->part_name,
                    'manufacturer' => $item->manufacturer,
                    'unit_cost' => number_format($item->unit_cost ?? 0, 2),
                    'currency' => $item->currency ?? 'USD',
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'overview' => [
                    'total_parts' => (int) ($overallStats->total_parts ?? 0),
                    'equipment_covered' => (int) ($overallStats->equipment_covered ?? 0),
                    'vessels' => (int) ($overallStats->vessels ?? 0),
                    'manufacturers' => (int) ($overallStats->manufacturers ?? 0),
                    'suppliers' => (int) ($overallStats->suppliers ?? 0),
                ],
                'financial' => [
                    'avg_unit_cost' => number_format($overallStats->avg_unit_cost ?? 0, 2),
                    'total_value' => number_format($overallStats->total_value ?? 0, 2),
                ],
                'category_breakdown' => $categoryStats->map(function ($item) {
                    return [
                        'category' => $item->part_category ?? 'Uncategorized',
                        'count' => (int) $item->count,
                        'avg_cost' => number_format($item->avg_cost ?? 0, 2),
                        'total_value' => number_format($item->total_value ?? 0, 2),
                    ];
                }),
                'manufacturer_breakdown' => $manufacturerStats->map(function ($item) {
                    return [
                        'manufacturer' => $item->manufacturer,
                        'count' => (int) $item->count,
                        'avg_cost' => number_format($item->avg_cost ?? 0, 2),
                    ];
                }),
                'expensive_parts' => $expensiveParts,
            ]
        ]);
    }
}