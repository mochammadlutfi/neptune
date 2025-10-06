<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\FuelInventoryRequest;
use App\Http\Resources\Equipment\FuelInventoryResource;
use App\Models\Equipment\FuelInventory;
use App\Models\Equipment\FuelTanks;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class FuelInventoryController extends Controller
{
    use ActivityLogger;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = FuelInventory::with(['vessel', 'tank', 'recordedBy']);

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('tank_id')) {
            $query->where('tank_id', $request->tank_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('inventory_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('inventory_date', '<=', $request->date_to);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('vessel', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%');
                  })
                  ->orWhereHas('tank', function($q) use ($search) {
                      $q->where('tank_name', 'LIKE', '%' . $search . '%')
                        ->orWhere('tank_number', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Sorting
        $allowedSorts = ['inventory_date', 'vessel_id', 'tank_id', 'rob_volume_liters', 'consumed_volume_liters'];
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
                'data' => FuelInventoryResource::collection($inventories->items()),
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
                'data' => FuelInventoryResource::collection($inventories)
            ]);
        }
    }

    public function store(FuelInventoryRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $validatedData = $request->validated();

            $data = new FuelInventory();
            $data->vessel_id = $user->vessel_id;
            $data->tank_id = $validatedData['tank_id'];
            $data->inventory_date = Carbon::parse($validatedData['inventory_date']);
            $data->opening_volume_liters = $validatedData['opening_volume_liters'] ?? null;
            $data->received_volume_liters = $validatedData['received_volume_liters'] ?? null;
            $data->consumed_volume_liters = $validatedData['consumed_volume_liters'] ?? null;
            $data->rob_volume_liters = $validatedData['rob_volume_liters'];
            $data->ullage_level = $validatedData['ullage_level'] ?? null;
            $data->temperature = $validatedData['temperature'] ?? null;
            $data->density = $validatedData['density'] ?? null;
            $data->water_content = $validatedData['water_content'] ?? null;
            $data->remarks = $validatedData['remarks'] ?? null;
            $data->recorded_by = $user->id;
            $data->save();

            // Log aktivitas create
            $this->logCreate($data, $request, __('activity.fuel_inventory.created', ['tank_name' => $data->tank?->tank_name]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fuel inventory berhasil dibuat.',
                'data' => new FuelInventoryResource($data->load(['vessel', 'tank', 'recordedBy']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat fuel inventory: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $inventory = FuelInventory::with(['vessel', 'tank', 'recordedBy'])->where('id', $id)->first();

        if (!$inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Fuel inventory tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new FuelInventoryResource($inventory)
        ]);
    }

    public function update(FuelInventoryRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = FuelInventory::where('id', $id)->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fuel inventory tidak ditemukan.',
                ], 404);
            }

            $validatedData = $request->validated();

            // Simpan data asli untuk logging
            $originalAttributes = $data->getOriginal();

            $data->tank_id = $validatedData['tank_id'];
            $data->inventory_date = Carbon::parse($validatedData['inventory_date']);
            $data->opening_volume_liters = $validatedData['opening_volume_liters'] ?? null;
            $data->received_volume_liters = $validatedData['received_volume_liters'] ?? null;
            $data->consumed_volume_liters = $validatedData['consumed_volume_liters'] ?? null;
            $data->rob_volume_liters = $validatedData['rob_volume_liters'];
            $data->ullage_level = $validatedData['ullage_level'] ?? null;
            $data->temperature = $validatedData['temperature'] ?? null;
            $data->density = $validatedData['density'] ?? null;
            $data->water_content = $validatedData['water_content'] ?? null;
            $data->remarks = $validatedData['remarks'] ?? null;
            $data->save();

            // Log aktivitas update
            $this->logUpdate($data, $request, $originalAttributes, __('activity.fuel_inventory.updated', ['tank_name' => $data->tank?->tank_name]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fuel inventory berhasil diupdate.',
                'data' => new FuelInventoryResource($data->fresh()->load(['vessel', 'tank', 'recordedBy']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate fuel inventory: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $inventory = FuelInventory::where('id', $id)->first();

            if (!$inventory) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fuel inventory tidak ditemukan.',
                ], 404);
            }

            $inventoryInfo = "Fuel inventory for {$inventory->tank?->tank_name} on {$inventory->inventory_date?->format('Y-m-d')}";

            // Log aktivitas delete sebelum menghapus
            $this->logDelete($inventory, request(), __('activity.fuel_inventory.deleted', ['tank_name' => $inventory->tank?->tank_name]));

            $inventory->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Fuel inventory '{$inventoryInfo}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus fuel inventory: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get fuel inventory by vessel
     */
    public function byVessel(Request $request, String $vesselId): JsonResponse
    {
        $query = FuelInventory::with(['vessel', 'tank', 'recordedBy'])
            ->where('vessel_id', $vesselId);

        if ($request->filled('date')) {
            $query->whereDate('inventory_date', $request->date);
        }

        $inventories = $query->orderBy('inventory_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => FuelInventoryResource::collection($inventories)
        ]);
    }

    /**
     * Get fuel inventory by tank
     */
    public function byTank(String $tankId): JsonResponse
    {
        $inventories = FuelInventory::with(['vessel', 'tank', 'recordedBy'])
            ->where('tank_id', $tankId)
            ->orderBy('inventory_date', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => FuelInventoryResource::collection($inventories)
        ]);
    }

    /**
     * Get latest inventory by tank
     */
    public function latestByTank(String $tankId): JsonResponse
    {
        $inventory = FuelInventory::with(['vessel', 'tank', 'recordedBy'])
            ->where('tank_id', $tankId)
            ->orderBy('inventory_date', 'desc')
            ->first();

        if (!$inventory) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada inventory untuk tank ini.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new FuelInventoryResource($inventory)
        ]);
    }

    /**
     * Get fuel inventory statistics
     */
    public function stats(Request $request): JsonResponse
    {
        $date = $request->get('date', Carbon::today()->format('Y-m-d'));
        $vessel_id = $request->get('vessel_id');

        $query = FuelInventory::with(['vessel', 'tank'])
            ->whereDate('inventory_date', $date);

        if ($vessel_id) {
            $query->where('vessel_id', $vessel_id);
        }

        // Overall statistics
        $overallStats = $query->selectRaw('
            COUNT(*) as total_records,
            COUNT(DISTINCT tank_id) as active_tanks,
            COUNT(DISTINCT vessel_id) as active_vessels,
            SUM(rob_volume_liters) as total_rob_liters,
            SUM(received_volume_liters) as total_received_liters,
            SUM(consumed_volume_liters) as total_consumed_liters,
            AVG(rob_volume_liters) as avg_rob_liters
        ')->first();

        // Tank breakdown
        $tankStats = $query->selectRaw('
            tank_id,
            rob_volume_liters,
            received_volume_liters,
            consumed_volume_liters,
            ullage_level
        ')
        ->get()
        ->map(function ($item) {
            $tank = FuelTanks::find($item->tank_id);
            $fillPercentage = $tank && $tank->capacity_liters > 0 ?
                ($item->rob_volume_liters / $tank->capacity_liters) * 100 : 0;

            return [
                'tank_name' => $tank?->tank_name,
                'tank_number' => $tank?->tank_number,
                'fuel_type' => $tank?->fuel_type,
                'capacity_liters' => $tank?->capacity_liters ?? 0,
                'rob_volume_liters' => number_format($item->rob_volume_liters, 2),
                'fill_percentage' => number_format($fillPercentage, 1),
                'received_liters' => number_format($item->received_volume_liters ?? 0, 2),
                'consumed_liters' => number_format($item->consumed_volume_liters ?? 0, 2),
                'ullage_level' => $item->ullage_level ?? null,
            ];
        });

        // Fuel type summary
        $fuelTypeStats = $query->join('fuel_tanks', 'fuel_inventory.tank_id', '=', 'fuel_tanks.id')
            ->selectRaw('
                fuel_tanks.fuel_type,
                COUNT(*) as tank_count,
                SUM(fuel_inventory.rob_volume_liters) as total_rob,
                SUM(fuel_inventory.consumed_volume_liters) as total_consumed
            ')
            ->groupBy('fuel_tanks.fuel_type')
            ->get();

        // Low fuel alerts (less than 10% capacity)
        $lowFuelAlerts = $query->join('fuel_tanks', 'fuel_inventory.tank_id', '=', 'fuel_tanks.id')
            ->whereRaw('fuel_inventory.rob_volume_liters < (fuel_tanks.capacity_liters * 0.1)')
            ->get()
            ->map(function ($item) {
                $tank = FuelTanks::find($item->tank_id);
                $fillPercentage = $tank && $tank->capacity_liters > 0 ?
                    ($item->rob_volume_liters / $tank->capacity_liters) * 100 : 0;

                return [
                    'tank_name' => $tank?->tank_name,
                    'tank_number' => $tank?->tank_number,
                    'rob_volume_liters' => number_format($item->rob_volume_liters, 2),
                    'capacity_liters' => $tank?->capacity_liters ?? 0,
                    'fill_percentage' => number_format($fillPercentage, 1),
                    'alert_level' => $fillPercentage < 5 ? 'Critical' : 'Warning',
                ];
            });

        // Calculate overall fuel efficiency
        $totalCapacity = FuelTanks::where('vessel_id', $vessel_id)->sum('capacity_liters');
        $overallFillPercentage = $totalCapacity > 0 ?
            (($overallStats->total_rob_liters ?? 0) / $totalCapacity) * 100 : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'date' => $date,
                'overview' => [
                    'total_records' => (int) ($overallStats->total_records ?? 0),
                    'active_tanks' => (int) ($overallStats->active_tanks ?? 0),
                    'active_vessels' => (int) ($overallStats->active_vessels ?? 0),
                    'overall_fill_percentage' => number_format($overallFillPercentage, 1),
                ],
                'fuel_summary' => [
                    'total_rob_liters' => number_format($overallStats->total_rob_liters ?? 0, 2),
                    'total_received_liters' => number_format($overallStats->total_received_liters ?? 0, 2),
                    'total_consumed_liters' => number_format($overallStats->total_consumed_liters ?? 0, 2),
                    'avg_rob_liters' => number_format($overallStats->avg_rob_liters ?? 0, 2),
                ],
                'tank_breakdown' => $tankStats,
                'fuel_type_summary' => $fuelTypeStats->map(function ($item) {
                    return [
                        'fuel_type' => $item->fuel_type,
                        'tank_count' => (int) $item->tank_count,
                        'total_rob' => number_format($item->total_rob, 2),
                        'total_consumed' => number_format($item->total_consumed ?? 0, 2),
                    ];
                }),
                'alerts' => [
                    'low_fuel_count' => $lowFuelAlerts->count(),
                    'low_fuel_tanks' => $lowFuelAlerts,
                ],
            ]
        ]);
    }
}