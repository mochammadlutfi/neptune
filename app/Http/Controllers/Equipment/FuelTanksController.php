<?php

namespace App\Http\Controllers\Equipment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Equipment\FuelTanksRequest;
use App\Http\Resources\Equipment\FuelTanksResource;
use App\Models\Equipment\FuelTanks;
use App\Traits\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class FuelTanksController extends Controller
{
    use ActivityLogger;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = FuelTanks::with(['vessel']);

        // Apply filters
        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        if ($request->filled('tank_location')) {
            $query->where('tank_location', 'LIKE', '%' . $request->tank_location . '%');
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tank_name', 'LIKE', '%' . $search . '%')
                  ->orWhere('tank_number', 'LIKE', '%' . $search . '%')
                  ->orWhere('tank_location', 'LIKE', '%' . $search . '%')
                  ->orWhereHas('vessel', function($q) use ($search) {
                      $q->where('name', 'LIKE', '%' . $search . '%');
                  });
            });
        }

        // Sorting
        $allowedSorts = ['tank_name', 'tank_number', 'capacity_liters', 'fuel_type', 'tank_location', 'created_at'];
        $sort = $request->get('sort', 'tank_number');
        $sortDir = $request->get('sortDir', 'asc');

        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('tank_number', 'asc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);

        if ($request->filled('page')) {
            $fuelTanks = $query->paginate($limit);
            return response()->json([
                'data' => FuelTanksResource::collection($fuelTanks->items()),
                'meta' => [
                    'current_page' => $fuelTanks->currentPage(),
                    'last_page' => $fuelTanks->lastPage(),
                    'per_page' => $fuelTanks->perPage(),
                    'total' => $fuelTanks->total(),
                    'from' => $fuelTanks->firstItem(),
                    'to' => $fuelTanks->lastItem(),
                ]
            ]);
        } else {
            $fuelTanks = $query->get();
            return response()->json([
                'data' => FuelTanksResource::collection($fuelTanks)
            ]);
        }
    }

    public function store(FuelTanksRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = $request->user();
            $validatedData = $request->validated();

            $data = new FuelTanks();
            $data->vessel_id = $user->vessel_id;
            $data->tank_name = $validatedData['tank_name'];
            $data->tank_number = $validatedData['tank_number'];
            $data->capacity_liters = $validatedData['capacity_liters'];
            $data->fuel_type = $validatedData['fuel_type'] ?? 'Diesel';
            $data->tank_location = $validatedData['tank_location'] ?? null;
            $data->save();

            // Log aktivitas create
            $this->logCreate($data, $request, __('activity.fuel_tanks.created', ['tank_name' => $data->tank_name]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fuel tank berhasil dibuat.',
                'data' => new FuelTanksResource($data->load(['vessel']))
            ], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat fuel tank: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function show(String $id): JsonResponse
    {
        $fuelTank = FuelTanks::with(['vessel'])->where('id', $id)->first();

        if (!$fuelTank) {
            return response()->json([
                'success' => false,
                'message' => 'Fuel tank tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new FuelTanksResource($fuelTank)
        ]);
    }

    public function update(FuelTanksRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = FuelTanks::where('id', $id)->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fuel tank tidak ditemukan.',
                ], 404);
            }

            $validatedData = $request->validated();

            // Simpan data asli untuk logging
            $originalAttributes = $data->getOriginal();

            $data->tank_name = $validatedData['tank_name'];
            $data->tank_number = $validatedData['tank_number'];
            $data->capacity_liters = $validatedData['capacity_liters'];
            $data->fuel_type = $validatedData['fuel_type'] ?? 'Diesel';
            $data->tank_location = $validatedData['tank_location'] ?? null;
            $data->save();

            // Log aktivitas update
            $this->logUpdate($data, $request, $originalAttributes, __('activity.fuel_tanks.updated', ['tank_name' => $data->tank_name]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Fuel tank berhasil diupdate.',
                'data' => new FuelTanksResource($data->fresh()->load(['vessel']))
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate fuel tank: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $fuelTank = FuelTanks::where('id', $id)->first();

            if (!$fuelTank) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fuel tank tidak ditemukan.',
                ], 404);
            }

            $tankInfo = "{$fuelTank->tank_name} (Tank #{$fuelTank->tank_number})";

            // Log aktivitas delete sebelum menghapus
            $this->logDelete($fuelTank, request(), __('activity.fuel_tanks.deleted', ['tank_name' => $fuelTank->tank_name]));

            $fuelTank->delete();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Fuel tank '{$tankInfo}' berhasil dihapus.",
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus fuel tank: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Get fuel tanks by vessel
     */
    public function byVessel(Request $request, String $vesselId): JsonResponse
    {
        $query = FuelTanks::with(['vessel'])
            ->where('vessel_id', $vesselId);

        if ($request->filled('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }

        $fuelTanks = $query->orderBy('tank_number', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => FuelTanksResource::collection($fuelTanks)
        ]);
    }
}