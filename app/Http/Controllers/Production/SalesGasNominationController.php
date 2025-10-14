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
use App\Services\SequenceGenerator;

class SalesGasNominationController extends Controller
{
    use ActivityLogger;

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): JsonResponse
    {
        $query = SalesGasNomination::with(['vessel', 'createdBy']);


        if ($request->filled('vessel_id')) {
            $query->where('vessel_id', $request->vessel_id);
        }

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
            
            $data->name = SequenceGenerator::generate('sales_gas_nomination');
            $data->date = $validatedData['date'];
            $data->vessel_id = $validatedData['vessel_id'];
            $data->total_nomination = $validatedData['total_nomination'];
            $data->total_confirmed = $validatedData['total_confirmed'];
            $data->status = 'draft';
            $data->created_uid = $user->id;
            $data->remarks = $validatedData['remarks'];
            $data->save();

            $data->lines()->saveMany(
                array_map(function($line) use ($data) {
                    return new SalesGasNominationLine([
                        'buyer_id' => $line['buyer_id'],
                        'nomination' => $line['nomination'],
                        'confirmed' => $line['confirmed'],
                        'sales_gas_nomination_id' => $data->id,
                    ]);
                }, $validatedData['lines'])
            );
            
            
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
        $data = SalesGasNomination::with(['vessel', 'createdBy', 'lines' => function($q){
            return $q->with('buyer');
        }])->where('id', $id)->first();
        
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
            $user = auth()->user();

            $data->name = SequenceGenerator::generate('sales_gas_nomination');
            $data->date = $validatedData['date'];
            $data->vessel_id = $validatedData['vessel_id'];
            $data->total_nomination = $validatedData['total_nomination'];
            $data->total_confirmed = $validatedData['total_confirmed'];;
            $data->created_uid = $user->id;
            $data->remarks = $validatedData['remarks'];
            $data->save();
            
            // Delete existing lines
            $data->lines()->delete();

            // Create new lines
            $data->lines()->saveMany(
                array_map(function($line) use ($data) {
                    return new SalesGasNominationLine([
                        'buyer_id' => $line['buyer_id'],
                        'nomination' => $line['nomination'],
                        'confirmed' => $line['confirmed'],
                        'sales_gas_nomination_id' => $data->id,
                    ]);
                }, $validatedData['lines'])
            );
            
            
            $originalAttributes = $data->getOriginal();
            $this->logUpdate($data, $request, $originalAttributes, __('activity.sales_gas_nomination.updated', ['id' => $data->id]));
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

    public function destroy(Request $request, String $id): JsonResponse
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
            
            $dataName = $data->name;
            // Log aktivitas delete sebelum menghapus untuk menangkap atribut model dan request
            $this->logDelete($data, $request, __('activity.sales_gas_nomination.deleted', ['id' => $data->id]));
            $data->delete();
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
