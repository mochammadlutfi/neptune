<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use App\Models\Outlet;

class OutletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        $limit = ($request->limit) ? $request->limit : 25;
        $search = !empty($request->search) ? $request->search : '';
        $searchKey = !empty($request->searchKey) ? $request->searchKey : '';
        $page = $request->page;

        $name = $request->name;
        $manager_id = $request->manager_id;
        $address = $request->address;
        $user_id = auth()->user()->id;
        
        $query = Outlet::with(['manager'])
        ->when(!empty($name), function($q, $name) {
            $q->where('name', 'like', '%'.$name.'%');
        })
        ->when(!empty($address), function($q, $address) {
            $q->where('address', 'like', '%'.$address.'%');
        })
        ->when(!empty($manager_id), function($q, $manager_id) {
            $q->where('manager_id', '=', $manager_id);
        })
        ->orderBy($sort, $sortDir);

        if($page){
            $data = $query->paginate($limit);
        }else{
            $data = $query->get();
        }

        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:191|unique:outlets,code',
            'phone' => 'required|string|min:8|max:20',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'manager_id' => 'required|exists:users,id'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'result' => $validator->errors(),
            ], 422);
        }else{
            DB::beginTransaction();
            try{
                $data = new Outlet();
                $data->fill($request->only([
                    'name', 'code', 'phone', 'address', 
                    'city', 'state', 'zip_code',
                    'is_active', 'manager_id','status'
                ]));
                $data->save();
            }catch(\QueryException $e){
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'result' => $e->getMessage(),
                ], 422);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'result' => $data,
            ], 200);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Outlet::with(['manager'])
        ->where('id', $id)
        ->first();

        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $rules = [
            'name' => 'required|string|max:191',
            'code' => 'nullable|string|max:191|unique:outlets,code,' . $id,
            'phone' => 'required|string|min:8|max:20',
            'address' => 'required|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'is_active' => 'boolean',
            'manager_id' => 'required|exists:users,id'
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()){
            return response()->json([
                'success' => false,
                'result' => $validator->errors(),
            ], 422);
        }else{
            DB::beginTransaction();
            try{
                $data = Outlet::findOrFail($id);
                $data->fill($request->only([
                    'name', 'code', 'phone', 'address', 
                    'city', 'state', 'zip_code',
                    'is_active','manager_id', 'status'
                ]));
                $data->save();
            }catch(\QueryException $e){
                DB::rollback();
                return response()->json([
                    'success' => false,
                    'result' => $e->getMessage(),
                ], 422);
            }
            DB::commit();
            return response()->json([
                'success' => true,
                'result' => $data,
            ], 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try{
            $data = Outlet::where('id', $id)->first();
            $data->delete();

        }catch(\QueryException $e){
            DB::rollback();
            return response()->json([
                'success' => false,
                'result' => $e,
            ], 422);
        }
        DB::commit();
        return response()->json([
            'success' => true,
        ], 200);
    }
}
