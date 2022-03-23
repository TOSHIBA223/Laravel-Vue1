<?php
namespace App\Http\Controllers\Admin\Pages;

use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Menus;
use App\Models\User;

class Dashboard extends AdminController
{
     /**
     * Scope this function get admin dashboard data.
     *
    */

    public function index()
    {

        return Inertia::render('Admin/Dashboard', ['data' => $this->data]);
    }
}
