<?php

namespace App\Http\Controllers\Settings;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Nwidart\Modules\Facades\Module;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class ModuleController extends Controller
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
        $search = strtolower($request->get('q', ''));
    
        $modules = collect(Module::all());
    
        $filtered = $modules->filter(function ($module) use ($search) {
            $name = strtolower($module->getName());
            $description = strtolower($module->get('description'));
    
            return str_contains($name, $search) ||
                   str_contains($description, $search);
        });
    
        return response()->json($filtered->map(function ($module) {
                return [
                    'name' => $module->getName(),
                    'enabled' => $module->isEnabled(),
                    'path' => $module->getPath(),
                    'version' => $module->get('version'),
                    'description' => $module->get('description'),
                ];
            })->values(),
        );
    }


    public function toggle($name)
    {
        $module = Module::find($name);

        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        if ($module->isEnabled()) {
            $module->disable();
        } else {
            $module->enable();
        }

        return response()->json(['message' => 'Module status updated']);
    }

    public function enable($name)
    {
        $module = Module::find($name);

        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        $module->enable();

        return response()->json(['message' => 'Module enabled']);
    }

    public function disable($name)
    {
        $module = Module::find($name);

        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }

        $module->disable();

        return response()->json(['message' => 'Module disabled']);
    }
    
    public function install(Request $request)
    {
        $request->validate([
            'module' => 'required|file|mimes:zip',
        ]);
    
        $zip = new ZipArchive;
        $file = $request->file('module');
        $modulePath = base_path('Modules');
    
        if ($zip->open($file->getRealPath()) === TRUE) {
            $zip->extractTo($modulePath);
            $zip->close();
    
            return response()->json(['message' => 'Module installed successfully']);
        }
    
        return response()->json(['message' => 'Failed to install module'], 500);
    }
    
    public function delete($name)
    {
        $module = Module::find($name);
    
        if (!$module) {
            return response()->json(['message' => 'Module not found'], 404);
        }
    
        $path = $module->getPath();
    
        if (File::exists($path)) {
            File::deleteDirectory($path);
            return response()->json(['message' => 'Module deleted successfully']);
        }
    
        return response()->json(['message' => 'Module path not found'], 404);
    }
    
}
