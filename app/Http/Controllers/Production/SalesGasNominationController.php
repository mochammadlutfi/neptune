<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Http\Requests\Production\SalesGasNominationRequest;
use App\Http\Resources\Production\SalesGasNominationResource;
use App\Models\Production\SalesGasNomination;
use App\Models\Production\SalesGasNominationLine;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use App\Traits\ActivityLogger;

class SalesGasNominationController extends Controller
{
    use ActivityLogger;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = SalesGasNomination::query();


        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('date', 'LIKE', '%' . $search . '%');
            });
        }

        // Sorting dengan validasi kolom yang diizinkan
        $allowedSorts = ['date', 'status', 'total_nomination'];
        $sort = $request->get('sort', 'date');
        $sortDir = $request->get('sortDir', 'asc');
        
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $sortDir === 'desc' ? 'desc' : 'asc');
        } else {
            $query->orderBy('date', 'asc');
        }

        // Pagination
        $limit = min($request->get('limit', 20), 100);
        
        if ($request->filled('page')) {
            $data = $query->paginate($limit);
            return response()->json([
                'data' => SalesGasNominationResource::collection($data->items()),
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
                'data' => SalesGasNominationResource::collection($data)
            ]);
        }       
    }

    public function store(SalesGasNominationRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validatedData = $request->validated();
            $user = auth()->user();

            $data = new SalesGasNomination();
            
            $data->date = $validatedData['date'];
            $data->vessel_id = $validatedData['vessel_id'];
            $data->total_nomination = $validatedData['total_nomination'];
            $data->total_confirmed = $validatedData['total_confirmed'];
            $data->status = 'draft';
            $data->recorded_by = $user->id;
            $data->save();
            
            $this->logCreate($data, $request, __('activity.sales_gas_nomination.created', ['id' => $data->id]));

            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sales Gas Nomination berhasil dibuat.',
                'data' => new SalesGasNominationResource($data)
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
        $data = SalesGasNomination::where('id', $id)->first();
        
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Sales Gas Nomination tidak ditemukan.',
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'data' => new SalesGasNominationResource($data)
        ]);
    }

    public function update(SalesGasNominationRequest $request, String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = SalesGasNomination::where('id', $id)->first();
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales Gas Nomination tidak ditemukan.',
                ], 404);
            }
            
            $validatedData = $request->validated();
            $data->update($validatedData);
            $this->logUpdate($data, $request, __('activity.sales_gas_nomination.updated', ['id' => $data->id]));
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Sales Gas Nomination berhasil diupdate.',
                'data' => new SalesGasNominationResource($data->fresh())
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate Sales Gas Nomination: ' . $e->getMessage(),
            ], 422);
        }
    }

    public function destroy(String $id): JsonResponse
    {
        DB::beginTransaction();
        try {
            $data = SalesGasNomination::where('id', $id)->first();
            
            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sales Gas Nomination tidak ditemukan.',
                ], 404);
            }
            
            // Check dependencies before deletion
            $wellsCount = $data->wells()->count();
            $equipmentCount = $data->equipment()->count();
            
            if ($wellsCount > 0 || $equipmentCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Tidak dapat menghapus Sales Gas Nomination yang memiliki {$wellsCount} wells dan {$equipmentCount} equipment.",
                ], 422);
            }

            $dataName = $data->name;
            $data->delete();
            $this->logDelete($data, __('activity.sales_gas_nomination.deleted', ['id' => $data->id]));
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => "Sales Gas Nomination '{$dataName}' berhasil dihapus.",    
            ]);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus Sales Gas Nomination: ' . $e->getMessage(),
            ], 422);
        }
    }
}
