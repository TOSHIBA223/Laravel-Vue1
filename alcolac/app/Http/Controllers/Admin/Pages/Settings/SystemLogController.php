<?php

namespace App\Http\Controllers\Admin\Pages\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roles;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\SystemLog;


class SystemLogController extends AdminController
{
    public function index()
    {
        $this->data['templates'] = SystemLog::select('*')->orderBy('created_at', 'DESC')->with('admin')->take(30)->get();
        // echo "<pre>";
        // var_dump($this->data['templates']);
        // echo "</pre>";
        // die;
        return Inertia::render(
            'Admin/Settings/SystemLog',
            [
                'data' => $this->data,
                'appUrl' => env('APP_URL'),
                'systemSuccess' => session('systemSuccess')
            ]
        );
    }
}
