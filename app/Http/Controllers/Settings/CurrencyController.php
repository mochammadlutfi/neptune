<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Settings\CurrencyRequest;
use App\Models\Currency;

class CurrencyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->q;
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';
        
        $elq = Currency::when($request->q, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%');
        })
        ->orderBy('created_at', 'DESC');

        if($request->limit){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{
            $data = new Currency;
            $data->name = $request->name;
            $data->code = $request->code;
            $data->symbol = $request->symbol;
            $data->position = $request->position;
            $data->save();

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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        DB::beginTransaction();
        try{
            $data = Currency::where('id', $id)->first();
            // dd($data);
            $data->name = $request->name;
            $data->code = $request->code;
            $data->symbol = $request->symbol;
            $data->position = $request->position;
            $data->save();

        }catch(\QueryException $e){
            DB::rollback();
            return back()->withError('error');
        }
        DB::commit();
        return redirect()->route('app.settings.currency.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try{
            $data = Currency::where('id', $id)->first();
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
