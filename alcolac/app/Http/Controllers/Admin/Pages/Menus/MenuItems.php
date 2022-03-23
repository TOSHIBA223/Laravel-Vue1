<?php
namespace App\Http\Controllers\Admin\Pages\Menus;

use App\Http\Controllers\Admin\AdminController;
use App\Models\Menu;
use Illuminate\Http\Request;
use App\Models\MenuItem;
use Inertia\Inertia;

class MenuItems extends AdminController
{

      /**
     * Scope this function get all menu data.
     *
    */

    public function get($id)
    {
        $selectedMenuItems = MenuItem::withTrashed()->where('menu_id', $id)->orderBy('order', 'asc')->get();
        if($selectedMenuItems->count() === 0)
            $selectedMenuItems = false;

        $this->data['menus'] = Menu::all();
        $this->data['selectedMenuItems'] = $selectedMenuItems;
        $this->data['selectedMenu'] = Menu::find($id);
        return back()->with(['data' => $this->data]);
    }

     /**
     * Scope this function create menu item data.
     *
    */

    public function create(Request $r)
    {
        unset($r['id']);

        $r->link = strtolower($r->link);
        if($r->access_level === 0 || empty($r->access_level) || $r->access_level === false )
            $r->access_level = 10;

        if($r->order === 0 || empty($r->order) || $r->order === false )
            $r->order = 5;

        try {
            MenuItem::create($r->all());

            $selectedMenuItems = MenuItem::withTrashed()->where('menu_id', $r->menu_id)->orderBy('order', 'asc')->get();
            if($selectedMenuItems->count() === 0)
                $selectedMenuItems = false;

            $this->data['selectedMenuItems'] = $selectedMenuItems;

            $this->data['selectedMenuItems'] = $selectedMenuItems;
            $this->data['selectedMenu'] = Menu::find($r->menu_id);
            $this->data['menus'] = Menu::all();

            return back()->with(['systemSuccess' => 'New menu item added successfully', 'data' => $this->data]);

        } catch( \Exception $e) {
            return back()->withErrors(['systemFail' => 'Something went wrong on the server. Please try again.']);
        }
    }

     /**
     * Scope this function update menu item data.
     *
    */

    public function update(Request $r)
    {
        if( $r->enable ) {
            try {
                MenuItem::onlyTrashed()->where('id', $r->id)->restore();

                $selectedMenuItems = MenuItem::withTrashed()->where('menu_id', $r->menu_id)->orderBy('order', 'asc')->get();
                if($selectedMenuItems->count() === 0)
                    $selectedMenuItems = false;

                $this->data['selectedMenuItems'] = $selectedMenuItems;
                $this->data['selectedMenu'] = Menu::find($r->menu_id);
                $this->data['menus'] = Menu::all();

                return back()->with(['systemSuccess' => 'Item enabled successfully', 'data' => $this->data]);
            } catch( \Exception $e) {
                return back()->withErrors(['systemFail' => 'We couldn\'t enable this menu. Please try again.']);
            }
        }

        try {
            MenuItem::find($r->id)->update($r->all());

            $selectedMenuItems = MenuItem::withTrashed()->where('menu_id', $r->menu_id)->orderBy('order', 'asc')->get();
            if($selectedMenuItems->count() === 0)
                $selectedMenuItems = false;

            $this->data['selectedMenuItems'] = $selectedMenuItems;
            $this->data['selectedMenu'] = Menu::find($r->menu_id);
            $this->data['menus'] = Menu::all();

            return back()->with(['systemSuccess' => 'Item updated successfully', 'data' => $this->data]);

        } catch( \Exception $e) {
            return back()->withErrors(['systemFail' => 'We couldn\'t update this item. Please try again.']);
        }
    }

    /**
     * Scope this function delete menu item data.
     *
    */

    public function delete($menuId, $id)
    {
        try{
            MenuItem::withTrashed()->find($id)->delete();
            $selectedMenuItems = MenuItem::withTrashed()->where('menu_id', $menuId)->orderBy('order', 'asc')->get();
            if($selectedMenuItems->count() === 0)
                $selectedMenuItems = false;

            $this->data['selectedMenuItems'] = $selectedMenuItems;
            $this->data['selectedMenu'] = Menu::find($menuId);
            $this->data['menus'] = Menu::all();

            return back()->with(['systemSuccess' => 'Item deleted successfully', 'data' => $this->data]);
        } catch( \Exception $e) {
            return back()->withErrors(['systemFail' => 'Something went wrong on the server. Please try again.']);
        }
    }
}
