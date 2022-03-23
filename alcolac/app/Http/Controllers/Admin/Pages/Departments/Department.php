<?php

namespace App\Http\Controllers\Admin\Pages\Departments;

use App\Models\Declaration;
use App\Models\DeclarationSent;
use App\Models\Location;
use App\Models\UserAddress;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Menu;
use App\Models\PermissionRoles;
use App\Models\User as UserModel;
use Illuminate\Support\Facades\DB;
use App\Models\Departments;
use Illuminate\Support\Facades\Hash;

class Department extends AdminController
{
    public function index()
    {

        if (session('data'))
            $this->data = session('data');

        $this->data['users'] = Departments::withTrashed()->orderBy('id')->paginate(15);

        $roleId = \Auth::user()->user_role_id;
        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 8])->first();
        $this->data['permission'] = $permission;
        if ($permission->perm_view == 1) {
            return Inertia::render('Admin/Departments/Index', ['data' => $this->data]);
        } else {
            return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
        }
    }

    /**
     * Scope this function search user  data by specific key woard.
     *
     */


    public function search(Request $r)
    {
        try {
            $this->data['users'] = (new AdminUsers)->searchParams($r->search);

            foreach ($this->data['users'] as $user) {
                $user->roleName = UserRole::find($user->user_role_id)->name;
                $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
            }

            $roleId = \Auth::user()->user_role_id;
            $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 1])->first();
            $this->data['permission'] = $permission;
            if ($permission->perm_view == 1) {
                return ['data' => $this->data];
            } else {
                return ['systemFail' => 'You are not authorized this access.'];
            }
        } catch (\Exception $e) {
            return ['systemFail' => 'We couldn\'t find the users.' . $e->getMessage()];
        }
    }

    /**
     * Scope this function create user  data.
     *
     */


    public function create(Request $r)
    {
        // $name = $r->input('employee_code');
        // var_dump($name); die;
        try {
            $userData = [
                'employee_code' => $r->employee_code,
                'department' => $r->department,
                'manager' => $r->manager,
                'location' => $r->location,
                'deleted_at' => null,
            ];
            Departments::create($userData);
            $this->data['users'] = Departments::withTrashed()->orderBy('id')->paginate(15);
            // foreach ($this->data['users'] as $user) {
            //     $user->roleName = UserRole::find($user->user_role_id)->name;
            //     $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
            // }

            return ['systemSuccess' => 'New Departments User added successfully.', 'data' => $this->data];
        } catch (\Exception $e) {
            return ['systemFail' => 'Something went wrong on the server. Please try again.'];
        }
    }

    /**
     * Scope this function update user  data.
     *
     */

    public function profile()
    {
        if (session('data'))
            $this->data = session('data');

        $this->data['users'] = Departments::withTrashed()->orderBy('id')->with('role')->paginate(15);
        foreach ($this->data['users'] as $user) {
            $user['roleName'] = UserRole::find($user->user_role_id)->name ?? 'admin';
            $user['address'] = '';
        }

        $this->data['locations'] =   DB::table('users')->select('location')->distinct()->get();
        $this->data['roles'] = UserRole::all();
        $this->data['declarations'] = Declaration::all();
        $roleId = \Auth::user()->user_role_id;
        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 8])->first();
        $this->data['permission'] = $permission;
        return Inertia::render('Admin/AdminUsers/updateprofile', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
    }

    public function update(Request $r)
    {
        if ($r->enable) {
            try {
                Departments::onlyTrashed()->where('id', $r->id)->restore();
                $this->data['users'] = Departments::withTrashed()->orderBy('id')->paginate(15);
                DB::table('departments')->where('id', $r->id)->update(array(

                ));
                // foreach ($this->data['users'] as $user) {
                //     $user->roleName = UserRole::find($user->user_role_id)->name;
                //     $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
                // }

                return ['systemSuccess' => 'Item enabled successfully.', 'data' => $this->data];
            } catch (\Exception $e) {
                return ['systemFail' => 'We couldn\'t enable this menu. Please try again.'];
            }
        }

        try {
            DB::table('admin_users')->where('id', $r->id)->update([
                'first_name' => $r->first_name,
                'last_name' => $r->last_name,
                'email' => $r->email,
                'phone' => $r->phone,
                'user_role_id' => $r->user_role_id,
                'status' => 1,
                'password' => Hash::make($r->password)
            ]);

            $this->data['users'] = Departments::withTrashed()->orderBy('id')->with('role')->paginate(15);
            foreach ($this->data['users'] as $user) {
                $user->roleName = UserRole::find($user->user_role_id)->name;
                $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
            }

            return ['systemSuccess' => 'Departments User updated successfully.', 'data' => $this->data];
        } catch (\Exception $e) {
            return ['systemFail' => 'We couldn\'t update this menu. Please try again.'];
        }
    }

    /**
     * Scope this function delete user  data.
     *
     */

    public function delete($id)
    {
        try {
            Departments::withTrashed()->find($id)->delete();

            $this->data['users'] = Departments::withTrashed()->orderBy('id')->paginate(15);
            foreach ($this->data['users'] as $user) {
                $user->roleName = UserRole::find($user->user_role_id)->name;
                $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
            }

            DB::table('admin_users')->where('id', $id)->update(array(
                'status' => 0,
            ));

            return ['systemSuccess' => 'Departments User deleted successfully.', 'data' => $this->data];
        } catch (\Exception $e) {
            return ['systemFail' => 'Something went wrong on the server. Please try again.'];
        }
    }


    public function permandelete($id)
    {
        try {
            Departments::where('id', $id)->forceDelete();

            $this->data['users'] = Departments::withTrashed()->orderBy('id')->paginate(15);
            // foreach ($this->data['users'] as $user) {
            //     $user->roleName = UserRole::find($user->user_role_id)->name;
            //     $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
            // }

            return ['systemSuccess' => 'Departments User deleted successfully.', 'data' => $this->data];
        } catch (\Exception $e) {
            return ['systemFail' => 'Something went wrong on the server. Please try again.'];
        }
    }

    private function setStatus($dec)
    {
        if ($dec->complete === 1)
            return config('constant.status.complete');

        if ($dec->void === 1)
            return config('constant.status.void');
    }
}
