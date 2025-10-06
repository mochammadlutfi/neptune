<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Partner;
use App\Traits\ActivityLogger;

class ContactController extends Controller
{
    use ActivityLogger;
    
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
     * @return Renderable
     */
    public function index(Request $request)
    {
        $keyword = $request->q;
        $sort = !empty($request->sort) ? $request->sort : 'id';
        $sortDir = !empty($request->sortDir) ? $request->sortDir : 'desc';

        $elq = Partner::when($request->q, function($query, $search){
            $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('phone', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');
        })
        ->withMedia()
        ->when($request->type == 'vendor', function($q, $type){
            return $q->where('is_vendor', '>', 0);
        })
        ->whereDoesntHave('user')
        ->orderBy($sort, $sortDir);
        if($request->limit){
            $data = $elq->paginate($request->limit);
        }else{
            $data = $elq->get();
        }
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        // dd($request->all());
        DB::beginTransaction();
        try{
            $data = new Partner();
            $data->type = $request->type;
            $data->name = $request->name;
            $data->company = $request->company;
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->mobile = $request->mobile;
            $data->address = $request->address;
            $data->city = $request->city;
            $data->zip_code = $request->zip_code;
            $data->state = $request->state;
            $data->country = $request->country;
            $data->is_vendor = $request->is_vendor == 'true' ? 1 : 0;
            $data->is_customer = $request->is_customer == 'true' ? 1 : 0;
            $data->is_active = $request->is_active == 'true' ? 1 : 0;
            $data->tax_number = $request->tax_number;
            $data->credit_limit = $request->credit_limit;
            $data->sale_pay_terms = $request->sale_pay_terms;
            $data->purchase_pay_terms = $request->purchase_pay_terms;
            $data->ref = $request->ref;
            $data->save();

            if($request->image){
                $data->attachMedia($request->image['id'], 'thumbnail');
            }
            
            // Log aktivitas create
            $this->logCreate($data, $request, "Membuat kontak baru: {$data->name}");
            
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
            'result' => $data->id
        ], 200);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        
        $data = Partner::withMedia()->where('id', $id)->first();

        if(!$data){
            return response()->json([
                'success' => false,
            ], 404);
        }
        return response()->json([
            'success' => true,
            'result' => $data
        ], 200);

    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $data = Partner::where('id', $id)->first();

        if(!$data){
            return response()->json([
                'success' => false,
            ], 404);
        }
        
        return response()->json([
            'success' => true,
            'result' => $data
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try{

            $data = Partner::where('id', $id)->first();
            $originalAttributes = $data->getAttributes(); // Simpan atribut asli sebelum update
            $data->type = $request->type;
            $data->name = $request->name;
            $data->company = $request->company;
            $data->email = $request->email;
            $data->phone = $request->phone;
            $data->mobile = $request->mobile;
            $data->address = $request->address;
            $data->city = $request->city;
            $data->zip_code = $request->zip_code;
            $data->state = $request->state;
            $data->country = $request->country;
            $data->is_vendor = $request->is_vendor == 'true' ? 1 : 0;
            $data->is_customer = $request->is_customer == 'true' ? 1 : 0;
            $data->is_active      = $request->is_active == 'true' ? 1 : 0;
            $data->tax_number = $request->tax_number;
            $data->credit_limit = $request->credit_limit;
            $data->sale_pay_terms = $request->sale_pay_terms;
            $data->purchase_pay_terms = $request->purchase_pay_terms;
            $data->ref = $request->ref;
            $data->save();

            // Log aktivitas update
            $this->logUpdate($data, $request, $originalAttributes, "Contact Update: {$data->name}");
            
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
            'result' => $data->id
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(Request $request, $id)
    {
        DB::beginTransaction();
        try{
            $data = Partner::where('id', $id)->first();
            
            // Log aktivitas delete sebelum menghapus
            $this->logDelete($data, $request, "Menghapus kontak: {$data->name}");
            
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
            'success' => true
        ], 200);
    }

    public function toggleStatus(Request $request, $id)
    {
        $data = Partner::where('id', $id)->first();
        if (!$data) {
            return response()->json([
                'success' => false,
                'message' => 'Contact not found'
            ], 404);
        }
        $data->is_active = !$data->is_active;
        $data->save();

        return response()->json([
            'success' => true
        ], 200);
    }
}
