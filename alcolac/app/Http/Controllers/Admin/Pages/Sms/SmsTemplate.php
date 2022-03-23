<?php
namespace App\Http\Controllers\Admin\Pages\Sms;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Menu;
use App\Models\SMSTemplate as SmsTemplateModel;
use App\Builders\ContentTags;
use App\Models\PermissionRoles;

class SmsTemplate extends AdminController
{

    /**
     * Scope this function get sms template and set data.
     *
    */

    public function index()
    {
        if(session('data'))
            $this->data = session('data');
        else {
            $this->data['templates'] = SmsTemplateModel::withTrashed()->get();
        }

		$roleId = \Auth::user()->user_role_id;
		$permission = PermissionRoles::where(['role_id'=>$roleId,'module_id'=>5])->first();
		$this->data['permission'] = $permission;
		if($permission->perm_view == 1){

        return Inertia::render('Admin/Sms/Template/Index',
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

    /**
     * Scope this function get sms template.
     *
    */

    public function get()
    {
        $this->data['templates'] = SmsTemplateModel::withTrashed()->get();
        return back()->with(['data' => $this->data]);
    }

    /**
     * Scope this function create sms template.
     *
    */

    public function create(Request $r)
    {
        unset($r['id']);

        try {
            SmsTemplateModel::create($r->all());
            $this->data['templates'] = SmsTemplateModel::withTrashed()->get();

            return back()->with(['systemSuccess' => 'New template added successfully', 'data' => $this->data]);

        } catch( \Exception $e) {
            return back()->withErrors(['systemFail' => 'Something went wrong on the server. Please try again.' . $e->getMessage()]);
        }
    }

    /**
     * Scope this function update sms template.
     *
    */
    public function update(Request $r)
    {
        if( $r->enable ) {
            try {
                SmsTemplateModel::onlyTrashed()->where('id', $r->id)->restore();

                $this->data['templates'] = SmsTemplateModel::withTrashed()->get();

                return (['systemSuccess' => 'Template enabled successfully', 'data' => $this->data]);
            } catch( \Exception $e) {
                return (['systemFail' => 'We couldn\'t enable this template. Please try again.']);
            }
        }

        try {
            SmsTemplateModel::find($r->id)->update($r->all());

            $this->data['templates'] = SmsTemplateModel::withTrashed()->get();

            return back()->with(['systemSuccess' => 'Template updated successfully', 'data' => $this->data]);

        } catch( \Exception $e) {
            return back()->withErrors(['systemFail' => 'We couldn\'t update this template. Please try again.']);
        }
    }

    /**
     * Scope this function create sms template.
     *
    */
    public function delete($id)
    {
        try{
            SmsTemplateModel::withTrashed()->find($id)->delete();
            $this->data['templates'] = SmsTemplateModel::withTrashed()->get();

            return ['systemSuccess' => 'Template deleted successfully', 'data' => $this->data];
        } catch( \Exception $e) {
            return ['systemFail' => 'Something went wrong on the server. Please try again.'];
        }
    }
}
