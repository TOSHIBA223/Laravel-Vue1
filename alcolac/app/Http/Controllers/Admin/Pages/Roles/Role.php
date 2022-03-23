<?php

namespace App\Http\Controllers\Admin\Pages\Roles;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Roles;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Menu;
use App\Models\SMSTemplate as SmsTemplateModel;
use App\Builders\ContentTags;
use App\Models\PermissionRoles;
use redirect;

class Role extends AdminController
{
    public function index()
    {
        if(session('data'))
            $this->data = session('data');
        else {
            $this->data['templates'] = Roles::get();
        }

        $roleId = \Auth::user()->user_role_id;

        $permission = PermissionRoles::where(['role_id'=>$roleId,'module_id'=>7])->first();


		$this->data['permission'] = $permission;


		if($permission->perm_view == 1){

            return Inertia::render('Admin/Roles/Index',
            [
                'data' => $this->data,
                'systemSuccess' => session('systemSuccess'),
                'contentTags' => ContentTags::getContentTags()
            ]
        );

		} else {
			 return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
		}


    }

    public function assign($id)
    {
        if(session('data'))
            $this->data = session('data');
        else {
            $this->data['templates'] = PermissionRoles::where('role_id',$id)->get();
        }
        $this->data['role_id'] = $id;

        $roleId = \Auth::user()->user_role_id;

        $permission = PermissionRoles::where(['role_id'=>$roleId,'module_id'=>7])->first();


		$this->data['permission'] = $permission;

        if($permission->perm_view == 1){

            return Inertia::render('Admin/Roles/Assign',
            [
                'data' => $this->data,
                'systemSuccess' => session('systemSuccess'),
                'contentTags' => ContentTags::getContentTags(),

            ]
        );

		} else {
			 return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
		}



    }

    public function savepermisson(Request $request){

		$dataRequest = $request->all();
	    $data =  $dataRequest['module'];
	    foreach($data as $k => $val){
		   PermissionRoles::where('id',$val['id'])->update(['perm_view'=>$val['view'],'perm_create'=>$val['create'],'perm_update'=>$val['update'],'perm_delete'=>$val['delete']]);
		 }
	    return back()->with(['systemSuccess' => 'updated successfully.']);

	 }


}
