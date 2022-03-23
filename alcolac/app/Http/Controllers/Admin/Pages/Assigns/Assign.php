<?php

namespace App\Http\Controllers\Admin\Pages\Assigns;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roles;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Menu;
use App\Models\SMSTemplate as SmsTemplateModel;
use App\Builders\ContentTags;
use App\Models\PermissionRoles;

class Assign extends Controller
{
    public function getroledata($id)
    {
        if(session('data'))
            $this->data = session('data');
        else {
            $this->data['templates'] = PermissionRoles::where('role_id',$id)->get();
        }

        return Inertia::render('Admin/Assigns/getroledata',
            [
                'data' => $this->data,
                'systemSuccess' => session('systemSuccess'),
                'contentTags' => ContentTags::getContentTags()
            ]
        );
    }
}
