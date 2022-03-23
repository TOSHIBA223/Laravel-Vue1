<?php
namespace App\Http\Controllers\Admin\Pages\Menus;

use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Menu;

class Menus extends AdminController
{

      /**
     * Scope this function get all menu data.
     *
    */

    public function index()
    {
        if(session('data'))
            $this->data = session('data');
        else {
            $this->data['menus'] = Menu::all();
            $this->data['selectedMenu'] = false;
        }
        return Inertia::render('Admin/Menus/Index', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
    }
}
