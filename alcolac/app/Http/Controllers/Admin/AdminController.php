<?php

namespace App\Http\Controllers\Admin;

use App\Models\MenuItem;
use App\Models\Menus;
use App\Models\Settings;
use App\Models\PermissionRoles;
use Illuminate\Routing\Controller as BaseController;
use DB;

abstract class AdminController extends BaseController
{

    protected $data;

    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->setSidebarItems();

            return $next($request);
        });
    }

    /**
     * Scope this function set sidebar menu.
     *
     */

    public function setSidebarItems()
    {
        if (\Auth::user() !== null) {

            $roleID = \Auth::user()->user_role_id;
            $data =  DB::table('permission_roles')->join('menu_items', 'menu_items.id', '=', 'permission_roles.module_id')->where(['permission_roles.role_id' => $roleID, 'permission_roles.status' => 1])->get();
            //$data = MenuItem::get()->toArray();

            // echo "<prE>"; print_r($data); die ;

            //$user_permission_level = \Auth::user()->role->menu_permission_level;
            $menu_items = MenuItem::orderBy('order', 'ASC')->get(); //MenuItem::where('access_level', '<=', $user_permission_level)->get();
           
            $ordered_menu_items = [];
            $children_menu_items = [];
            foreach ($menu_items as $item) {
                if ($item->parent == 0) {
                    $ordered_menu_items[$item->menu_id] = $item;
                } else {
                    if (!isset($children_menu_items[$item->parent])) {
                        $children_menu_items[$item->parent] = [];
                    }
                    array_push($children_menu_items[$item->parent], $item);
                }
            }
            $this->data['menuItems']['sidebar'] = $ordered_menu_items;
            $this->data['menuItems']['sidebar_children'] = $children_menu_items;
            $this->data['logo'] = Settings::get();
            //$this->data['menuItems']['sidebar'] = $data;

            // echo "<pre>"; print_r($this->data); die;

            //MenuItem::get();
            //dd($this->data['menuItems']['sidebar']);
        }
    }
}
