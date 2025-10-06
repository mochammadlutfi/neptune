<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
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
        $page = $request->page;
        
        $query = Role::withCount('users')->
        withCount('permissions')->
        when(!empty($search), function($q, $search) {
            $q->where('name', 'like', '%'.$search.'%');
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
     * Show the form for creating a new resource.
     */
    public function list()
    {
        $names = Permission::query()->pluck('name');

        $grouped = [];

        foreach ($names as $full) {
            if (!$full) continue;

            // remove prefix "base." if exists
            if (str_starts_with($full, 'base.')) {
                $full = substr($full, 5);
            }

            $dotPos = strrpos($full, '.');
            if ($dotPos === false) continue;

            $path   = substr($full, 0, $dotPos);  // e.g. equipment-maintenance
            $action = substr($full, $dotPos + 1); // e.g. view

            [$module, $label] = array_pad(explode('-', $path, 2), 2, null);
            if (!$module || !$label) continue;

            $grouped[$module][$label][$action] = $full;
        }

        // fixed order for modules
        $moduleOrder = ['settings', 'master', 'production', 'equipment'];

        // action order
        $actionOrder = ['view','create','edit','update','delete','import','export','print','confirm','cancel'];

        // build output
        $result = [];
        foreach ($moduleOrder as $module) {
            if (!isset($grouped[$module])) continue;

            $menus = [];
            ksort($grouped[$module]);

            foreach ($grouped[$module] as $label => $actionsMap) {
                // sort actions
                uksort($actionsMap, function ($a, $b) use ($actionOrder) {
                    $ia = array_search($a, $actionOrder, true);
                    $ib = array_search($b, $actionOrder, true);
                    $ia = $ia === false ? PHP_INT_MAX : $ia;
                    $ib = $ib === false ? PHP_INT_MAX : $ib;
                    return $ia === $ib ? strcmp($a, $b) : $ia <=> $ib;
                });

                $actions = [];
                foreach ($actionsMap as $name => $key) {
                    $actions[] = [
                        'key'  => $key,
                        'name' => $name,
                    ];
                }

                $menus[] = [
                    'label'   => $label,
                    'actions' => $actions,
                ];
            }

            $result[] = [
                'module' => $module,
                'menu'   => $menus,
            ];
        }

        return response()->json($result);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try{

            $role = Role::create(['name' => $request->name]);
            $role->syncPermissions($request->lines);
            
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
     * Display the specified resource.
     */
    public function show($id)
    {
        
        $data = Role::with('permissions:id,name')->
        where('id', $id)->first();
        return response()->json($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // dd($request->all());
        DB::beginTransaction();
        try{

            $role = Role::where('id', $id)->first();
            $role->name = $request->name;
            $role->save();

            $role->syncPermissions($request->lines);

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
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();
        try{
            $role = Role::where('id', $id)->first();
            $role->delete();
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
