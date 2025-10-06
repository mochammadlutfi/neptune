<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use Illuminate\Support\Facades\Auth;

class MenuController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Dapatkan semua permissions user
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        // Ambil semua menu dan relasi nested-nya
        Menu::fixTree();

        $menus = Menu::with(['children' => function ($query) {
                $query->orderBy('order', 'ASC');
            }])
            ->whereIsRoot()
            ->orderBy('order', 'ASC')
            ->get();
            // ->toTree();
        
        $sortedTree = $this->sortTreeByOrder($menus);

        // Filter menu berdasarkan permission
        // $filteredMenus = $this->filterMenuByPermissions($menus, $permissions);

        return response()->json($sortedTree);
    }

    private function filterMenuByPermissions($menus, $permissions)
    {
        return $menus->map(function ($menu) use ($permissions) {
            // Cek permission, jika kosong (null), berarti menu ini terbuka untuk semua
            if (!$menu->permission || in_array($menu->permission, $permissions)) {
                // Cek dan filter child-nya
                $children = $menu->children ? $this->filterMenuByPermissions($menu->children, $permissions) : [];

                return [
                    'id' => $menu->id,
                    'name' => $menu->name,
                    'icon' => $menu->icon,
                    'to' => $menu->to,
                    'module' => $menu->module,
                    'permission' => $menu->permission,
                    'children' => $children,
                ];
            }

            return null;
        })->filter()->values();
    }

    private function sortTreeByOrder($nodes)
    {
        return $nodes->sortBy('order')->values()->map(function ($node) {
            if ($node->children) {
                $node->children = $this->sortTreeByOrder($node->children);
            }
            return $node;
        });
    }
}
