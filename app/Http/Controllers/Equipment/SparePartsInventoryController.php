<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\SparePartsInventoryRequest;
use App\Http\Resources\Equipment\SparePartsInventoryResource;
use App\Models\Equipment\SparePartsInventory;
use App\Models\Equipment\SpareParts;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class SparePartsInventoryController extends Controller
{
    use ActivityLogger;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = SparePartsInventory::with(['sparePart', 'sparePart.vessel', 'sparePart.equipment']);

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->whereHas('sparePart', function($q) use ($request) {
                $q->where('vessel_id', $request->vessel_id);
            });
        }

        if ($request->filled('spare_part_id')) {
            $query->where('spare_part_id', $request->spare_part_id);
        }

        if ($request->filled('condition_status')) {
            $query->where('condition_status', $request->condition_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('inventory_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('inventory_date', '<=', $request->date_to);
        }

        if ($request->filled('low_stock')) {
            $query->whereRaw('quantity_onboard <= min_stock_level');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('sparePart', function($q) use ($search) {
                      $q->where('part_number', 'LIKE', '%' . $search . '%')
                        ->orWhere('part_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('manufacturer', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhere('storage_location', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting
        $allowedSorts = ['inventory_date', 'spare_part_id', 'quantity_onboard', 'min_stock_level', 'reorder_point'];
        $sort = $request->get('sort', 'inventory_date');
        $sortDir = $request->get('sortDir', 'desc');

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('inventory_date', 'desc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);

        if ($request->filled('page')) {
            $inventories = $query->paginate($limit);
            return response()->json([
                'data' => SparePartsInventoryResource::collection($inventories->items()),
                'meta' => [
                    'current_page' => $inventories->currentPage(),
                    'last_page' => $inventories->lastPage(),
                    'per_page' => $inventories->perPage(),
                    'total' => $inventories->total(),
                    'from' => $inventories->firstItem(),
                    'to' => $inventories->lastItem(),
                ]
            ]);
        } else {
            $inventories = $query->get();
            return response()->json([
                'data' => SparePartsInventoryResource::collection($inventories)
            ]);
        }
    }

    public function store(SparePartsInventoryRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $validatedData = $request->validated();

            $data = new SparePartsInventory();
            $data->spare_part_id = $validatedData['spare_part_id'];
            $data->inventory_date = Carbon::parse($validatedData['inventory_date']);
            $data->quantity_onboard = $validatedData['quantity_onboard'];
            $data->min_stock_level = $validatedData['min_stock_level'] ?? 0;
            $data->reorder_point = $validatedData['reorder_point'] ?? 0;
            $data->reorder_quantity = $validatedData['reorder_quantity'] ?? 0;
            $data->last_reorder_date = $validatedData['last_reorder_date'] ? Carbon::parse($validatedData['last_reorder_date']) : null;
            $data->storage_location = $validatedData['storage_location'] ?? null;
            $data->condition_status = $validatedData['condition_status'] ?? 'New';
            $data->remarks = $validatedData['remarks'] ?? null;
            $data->recorded_by = $user->id;
            $data->save();

            // Log aktivitas create
            $this->logCreate($data, $request, __('activity.spare_parts_inventory.created', ['part_name' => $data->sparePart?->part_name]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Spare parts inventory berhasil dibuat.',
                'data' => new SparePartsInventoryResource($data->load(['sparePart', 'sparePart.vessel', 'sparePart.equipment']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat spare parts inventory: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $inventory = SparePartsInventory::with(['sparePart', 'sparePart.vessel', 'sparePart.equipment'])->where('id', $id)->first();

        if (!$inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Spare parts inventory tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new SparePartsInventoryResource($inventory)
        ]);
    }

    public function update(SparePartsInventoryRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = SparePartsInventory::where('id', $id)->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spare parts inventory tidak ditemukan.',
                ], 404);
            }

            $validatedData = $request->validated();

            // Simpan data asli untuk logging
            $originalAttributes = $data->getOriginal();

            $data->spare_part_id = $validatedData['spare_part_id'];
            $data->inventory_date = Carbon::parse($validatedData['inventory_date']);
            $data->quantity_onboard = $validatedData['quantity_onboard'];
            $data->min_stock_level = $validatedData['min_stock_level'] ?? 0;
            $data->reorder_point = $validatedData['reorder_point'] ?? 0;
            $data->reorder_quantity = $validatedData['reorder_quantity'] ?? 0;
            $data->last_reorder_date = $validatedData['last_reorder_date'] ? Carbon::parse($validatedData['last_reorder_date']) : null;
            $data->storage_location = $validatedData['storage_location'] ?? null;
            $data->condition_status = $validatedData['condition_status'] ?? 'New';
            $data->remarks = $validatedData['remarks'] ?? null;
            $data->save();

            // Log aktivitas update
            $this->logUpdate($data, $request, $originalAttributes, __('activity.spare_parts_inventory.updated', ['part_name' => $data->sparePart?->part_name]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Spare parts inventory berhasil diupdate.',
                'data' => new SparePartsInventoryResource($data->fresh()->load(['sparePart', 'sparePart.vessel', 'sparePart.equipment']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate spare parts inventory: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $inventory = SparePartsInventory::where('id', $id)->first();

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Spare parts inventory tidak ditemukan.',
                ], 404);
            }

            $inventoryInfo = "Inventory for {$inventory->sparePart?->part_name} on {$inventory->inventory_date?->format('Y-m-d')}";

            // Log aktivitas delete sebelum menghapus
            $this->logDelete($inventory, request(), __('activity.spare_parts_inventory.deleted', ['part_name' => $inventory->sparePart?->part_name]));

            $inventory->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Spare parts inventory '{$inventoryInfo}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus spare parts inventory: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get spare parts inventory by vessel
     */
    public function byVessel(Request $request, String $vesselId): JsonResponse
    {
        $query = SparePartsInventory::with(['sparePart', 'sparePart.vessel', 'sparePart.equipment'])
            ->whereHas('sparePart', function($q) use ($vesselId) {
                $q->where('vessel_id', $vesselId);
            });

        if ($request->filled('date')) {
            $query->whereDate('inventory_date', $request->date);
        }

        $inventories = $query->orderBy('inventory_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => SparePartsInventoryResource::collection($inventories)
        ]);
    }

    /**
     * Get latest inventory by spare part
     */
    public function latestBySparePart(String $sparePartId): JsonResponse
    {
        $inventory = SparePartsInventory::with(['sparePart', 'sparePart.vessel', 'sparePart.equipment'])
            ->where('spare_part_id', $sparePartId)
            ->orderBy('inventory_date', 'desc')
            ->first();

        if (!$inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada inventory untuk spare part ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new SparePartsInventoryResource($inventory)
        ]);
    }

    /**
     * Get inventory alerts (low stock, reorder points)
     */
    public function alerts(Request $request): JsonResponse
    {
        $vessel_id = $request->get('vessel_id');

        $query = SparePartsInventory::with(['sparePart', 'sparePart.vessel', 'sparePart.equipment'])
            ->whereRaw('quantity_onboard <= reorder_point OR quantity_onboard <= min_stock_level');

        if ($vessel_id) {
            $query->whereHas('sparePart', function($q) use ($vessel_id) {
                $q->where('vessel_id', $vessel_id);
            });
        }

        // Get latest inventory for each spare part
        $latestInventories = $query->orderBy('inventory_date', 'desc')
            ->get()
            ->groupBy('spare_part_id')
            ->map(function ($inventories) {
                return $inventories->first();
            })
            ->values()
            ->map(function ($inventory) {
                $alertType = [];
                if ($inventory->quantity_onboard <= $inventory->min_stock_level) {
                    $alertType[] = 'Critical Stock Level';
                }
                if ($inventory->quantity_onboard <= $inventory->reorder_point) {
                    $alertType[] = 'Reorder Point Reached';
                }

                return [
                    'spare_part_id' => $inventory->spare_part_id,
                    'part_number' => $inventory->sparePart?->part_number,
                    'part_name' => $inventory->sparePart?->part_name,
                    'current_stock' => $inventory->quantity_onboard,
                    'min_stock_level' => $inventory->min_stock_level,
                    'reorder_point' => $inventory->reorder_point,
                    'reorder_quantity' => $inventory->reorder_quantity,
                    'alert_types' => $alertType,
                    'storage_location' => $inventory->storage_location,
                    'last_updated' => $inventory->inventory_date?->format('Y-m-d'),
                ];
            });

        return response()->json([
            'success' => true,
            'data' => [
                'alerts_count' => $latestInventories->count(),
                'alerts' => $latestInventories
            ]
        ]);
    }

    /**
     * Get spare parts inventory statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $vessel_id = $request->get('vessel_id');

        $query = SparePartsInventory::with(['sparePart', 'sparePart.vessel'])
            ->whereDate('inventory_date', $date);

        if ($vessel_id) {
            $query->whereHas('sparePart', function($q) use ($vessel_id) {
                $q->where('vessel_id', $vessel_id);
            });
        }

        // Overall statistics
        $overallStats = $query->selectRaw('
            COUNT(*) as total_inventory_records,
            COUNT(DISTINCT spare_part_id) as unique_parts,
            SUM(quantity_onboard) as total_quantity,
            COUNT(CASE WHEN quantity_onboard <= min_stock_level THEN 1 END) as low_stock_count,
            COUNT(CASE WHEN quantity_onboard <= reorder_point THEN 1 END) as reorder_needed_count,
            COUNT(CASE WHEN condition_status = "New" THEN 1 END) as new_condition_count,
            COUNT(CASE WHEN condition_status = "Used" THEN 1 END) as used_condition_count
        ')->first();

        // Condition status breakdown
        $conditionStats = $query->selectRaw('
            condition_status,
            COUNT(*) as count,
            SUM(quantity_onboard) as total_quantity
        ')->groupBy('condition_status')->get();

        // Storage location breakdown
        $locationStats = $query->selectRaw('
            storage_location,
            COUNT(*) as count,
            SUM(quantity_onboard) as total_quantity
        ')
        ->whereNotNull('storage_location')
        ->groupBy('storage_location')
        ->orderBy('count', 'desc')
        ->get();

        // Parts with highest quantities
        $highQuantityParts = $query->with('sparePart')
            ->orderBy('quantity_onboard', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($inventory) {
                return [
                    'part_number' => $inventory->sparePart?->part_number,
                    'part_name' => $inventory->sparePart?->part_name,
                    'quantity_onboard' => $inventory->quantity_onboard,
                    'storage_location' => $inventory->storage_location,
                    'condition_status' => $inventory->condition_status,
                ];
            });

        // Calculate stock health percentage
        $totalParts = $overallStats->unique_parts ?? 0;
        $lowStockCount = $overallStats->low_stock_count ?? 0;
        $stockHealthPercent = $totalParts > 0 ? (($totalParts - $lowStockCount) / $totalParts) * 100 : 100;

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'overview' => [
                    'total_inventory_records' => (int) ($overallStats->total_inventory_records ?? 0),
                    'unique_parts' => (int) ($overallStats->unique_parts ?? 0),
                    'total_quantity' => (int) ($overallStats->total_quantity ?? 0),
                    'stock_health_percent' => number_format($stockHealthPercent, 1),
                ],
                'alerts' => [
                    'low_stock_count' => (int) ($overallStats->low_stock_count ?? 0),
                    'reorder_needed_count' => (int) ($overallStats->reorder_needed_count ?? 0),
                ],
                'condition_breakdown' => $conditionStats->map(function ($item) {
                    return [
                        'condition' => $item->condition_status ?? 'Unknown',
                        'count' => (int) $item->count,
                        'total_quantity' => (int) $item->total_quantity,
                    ];
                }),
                'location_breakdown' => $locationStats->map(function ($item) {
                    return [
                        'location' => $item->storage_location,
                        'count' => (int) $item->count,
                        'total_quantity' => (int) $item->total_quantity,
                    ];
                }),
                'high_quantity_parts' => $highQuantityParts,
            ]
        ]);
    }
}