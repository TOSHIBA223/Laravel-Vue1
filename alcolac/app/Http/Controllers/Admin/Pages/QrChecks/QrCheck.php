<?php

namespace App\Http\Controllers\Admin\Pages\QrChecks;

use App\Models\Declaration;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\PermissionRoles;
use Illuminate\Support\Facades\DB;
use App\Models\QrCodes;
use App\Models\QrChecks;

class QrCheck extends AdminController
{
    public function index()
    {
        if (session('data'))
            $this->data = session('data');

        $this->data['users'] = QrChecks::orderBy('id')->paginate(15);
        $this->data['locations'] = DB::table('users')->select('location')->distinct()->get();
        $this->data['roles'] = UserRole::all();
        $this->data['declarations'] = Declaration::all();
        $roleId = \Auth::user()->user_role_id;
        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 12])->first();
        $this->data['permission'] = $permission;

        if ($permission->perm_view == 1) {
            return Inertia::render('Admin/QrChecks/Index', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
        } else {
            return back()->withErrors(['systemFail' => 'You are not authorized this access.']);
        }
    }

    /**
     * Scope this function search user  data by specific key word.
     *
     */


    public function search(Request $r)
    {
        try {
            $this->data['users'] = (new QrChecks)->searchParams($r->search);
            $roleId = \Auth::user()->user_role_id;
            $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 12])->first();
            $this->data['permission'] = $permission;
            if ($permission->perm_view == 1) {
                return ['data' => $this->data, 'systemSuccess' => "Successfully searched."];
            } else {
                return ['systemFail' => 'You are not authorized this access.'];
            }
        } catch (\Exception $e) {
            return ['systemFail' => 'We couldn\'t find the users.' . $e->getMessage()];
        }
    }

    public function create(Request $r)
    {
        try {
            $qr_checks = new QrChecks;
            $qr_checks->employee_code = $r->employee_code;
            $qr_checks->first_name = $r->first_name;
            $qr_checks->last_name = $r->last_name;
            $qr_checks->phone = $r->phone;
            $qr_checks->location = "RATCovidtesting";
            $qr_checks->ip_address = $r->ip();
            $qr_checks->result = $r->result;
            $qr_checks->save();

            $token = 'RATCovidtesting';
            $this->data['users'] = QrChecks::where('location', $token)->paginate(15);

            return ['systemSuccess' => 'New Admin User added successfully.', 'data' => $this->data];
        } catch (\Exception $e) {
            return ['systemFail' => 'Something went wrong on the server. Please try again.'];
        }
    }

    public function update(Request $r)
    {
        try {
            DB::table('qr_checks')->where('id', $r->id)->update([
                'result' => $r->result,
            ]);
        
            $token = 'RATCovidtesting';
            $this->data['users'] = QrChecks::where('location', $token)->paginate(15);
            // $this->data['locations'] = DB::table('users')->select('location')->distinct()->get();
            // $this->data['roles'] = UserRole::all();
            // $this->data['declarations'] = Declaration::all();
            $roleId = \Auth::user()->user_role_id;
            $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 12])->first();
            $this->data['permission'] = $permission;

            if ($permission->perm_view == 1) {
                return ['systemSuccess' => 'RAT result updated successfully', 'data' => $this->data];
            } else {
                return ['systemFail' => 'We couldn\'t update this result. Please try again.'];
            }
        } catch (\Exception $e) {
            return ['systemFail' => 'We couldn\'t update this result. Please try again.' . $e->getMessage()];
        }
    }

    /**
     * Scope this function delete user  data.
     *
     */

    public function delete($id)
    {
        try {
            (new QrChecks())->find($id)->delete();
            $token = 'RATCovidtesting';
            $this->data['users'] = QrChecks::where('location', $token)->paginate(15);

            return ['systemSuccess' => 'RAT result deleted successfully', 'data' => $this->data];
        } catch (\Exception $e) {
            return ['systemFail' => 'Something went wrong on the server. Please try again.'];
        }
    }
}
