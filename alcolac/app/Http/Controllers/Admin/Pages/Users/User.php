<?php

namespace App\Http\Controllers\Admin\Pages\Users;

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

class User extends AdminController
{

    /**
     * Scope this function user model data.
     *
     */

    public function index(Request $r)
    {
        if (session('data')) {
            $this->data = session('data');
        }

        if ($r->field && $r->value) {
            $this->data['users'] = (new UserModel)->search($r->field, $r->value);
        } else {
            $this->data['users'] = UserModel::where('is_qr_employee', 0)->withTrashed()->orderBy('last_name')->paginate(15);
        }
        foreach ($this->data['users'] as $user) {
            if ($r->field && $r->value) {
                $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
            } else {
                $user['address'] = $user->address()->first();
            }
        }

        $this->data['locations'] = DB::table('users')->select('location')->distinct()->get();
        $this->data['employee_code_array'] = DB::table('users')->select('employee_code')->distinct()->get();
        $this->data['first_name_array'] = DB::table('users')->select('first_name')->distinct()->get();
        $this->data['last_name_array'] = DB::table('users')->select('last_name')->distinct()->get();
        $this->data['department_array'] = DB::table('users')->select('groups')->distinct()->get();
        $this->data['roles'] = UserRole::all();
        $this->data['declarations'] = Declaration::all();
        $this->data['field'] = $r->field;
        $this->data['value'] = $r->value;
        $roleId = \Auth::user()->user_role_id;
        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 1])->first();
        $this->data['permission'] = $permission;
        if ($permission->perm_view == 1) {
            return Inertia::render('Admin/Users/Index', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
        } else {
            return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
        }
    }

    public function searchByEmployeeCode(Request $r)
    {
        try {
            $this->data['users'] = (new UserModel)->searchExact('employee_code', $r->search);
            return ['data' => $this->data, 'systemSuccess' => 'Successfully gets the employee data'];
        } catch (\Exception $e) {
            return ['systemFail' => 'We couldn\'t find the users.' . $e->getMessage()];
        }
    }

    /**
     * Scope this function search user  data by specific key woard.
     *
     */
    public function search(Request $r)
    {
        try {
            $this->data['users'] = (new UserModel)->searchParams($r->search);

            foreach ($this->data['users'] as $user) {
                $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
            }

            $roleId = \Auth::user()->user_role_id;
            $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 1])->first();
            $this->data['permission'] = $permission;
            if ($permission->perm_view == 1) {
                return Inertia::render('Admin/Users/Index', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
            } else {
                return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
            }

            //return back()->with(['data' => $this->data]);
            //return Inertia::render('Admin/Users/Index', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
        } catch (\Exception $e) {
            return back()->withErrors(['systemFail' => 'We couldn\'t find the users.' . $e->getMessage()]);
        }
    }

    /**
     * Scope this function create user  data.
     *
     */


    public function create(Request $r)
    {
        try {
            \DB::transaction(function () use ($r) {
                $userData = [
                    'first_name' => $r->first_name,
                    'last_name' => $r->last_name,
                    'employee_code' => $r->employee_code,
                    'name' => $r->last_name,
                    'email' => $r->email,
                    //'password' => '123',
                    'dob' => $r->dob,
                    'phone' => $r->phone,
                    'groups' => $r->groups,
                    'location' => $r->location,
                    //'user_role_id' => $r->roll,
                    'simple_form' => 0
                ];
                $new_user = UserModel::create($userData);

                if ($cur_address = UserAddress::where([['current', 1], ['user_id', $new_user->id]])->first()) {
                    $cur_address->current = 0;
                    $cur_address->save;
                }

                if (!empty($r->address)) {
                    $locationData = [
                        'user_id' => $new_user->id,
                        'address' => $r->address,
                        'suburb' => $r->suburb,
                        'state' => $r->state,
                        'post_code' => $r->post_code,
                        'current' => 1
                    ];

                    UserAddress::create($locationData);
                }
            });

            $this->data['users'] = UserModel::withTrashed()->orderBy('last_name')->paginate(15);
            // foreach($this->data['users'] as $user)
            // {
            // $user->locationName = Location::find($user->location)->name;
            // $user->roleName = UserRole::find($user->user_role_id)->name;
            // $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
            // }

            return back()->with(['systemSuccess' => 'New user added successfully', 'data' => $this->data]);
        } catch (\Exception $e) {
            return back()->withErrors(['systemFail' => 'Something went wrong on the server. Please try again.' . $e->getMessage()]);
        }
    }

    /**
     * Scope this function update user  data.
     *
     */

    public function update(Request $r)
    {
        if ($r->enable) {

            try {
                UserModel::onlyTrashed()->where('id', $r->id)->restore();
                $this->data['users'] = UserModel::withTrashed()->orderBy('last_name')->paginate(15);
                foreach ($this->data['users'] as $user) {
                    // $user->locationName = Location::find($user->location)->name;
                    // $user->roleName = UserRole::find($user->user_role_id)->name;
                    $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
                }

                DB::table('users')->where('id', $r->id)->update(array(
                    'is_inactive' => 0,
                ));

                return (['systemSuccess' => 'Item enabled successfully', 'data' => $this->data]);
            } catch (\Exception $e) {
                return (['systemFail' => 'We couldn\'t enable this menu. Please try again.']);
            }
        }

        try {

            UserModel::find($r->id)->update($r->all());
            \DB::transaction(function () use ($r) {
                $userData = [
                    'first_name' => $r->first_name,
                    'last_name' => $r->last_name,
                    'email' => $r->email,
                    'employee_code' => $r->employee_code,
                    'name' => $r->last_name,
                    //'password' => '123456',
                    'dob' => $r->dob,
                    'phone' => $r->phone,
                    'groups' => $r->groups,
                    'location' => $r->location,
                    //'user_role_id' => $r->user_role_id,
                    'simple_form' => 0
                ];
                UserModel::find($r->id)->update($userData);

                if ($cur_address = UserAddress::where([['current', 1], ['user_id', $r->id]])->first()) {
                    $cur_address->current = 0;
                    $cur_address->save();
                }

                if (!empty($r->address)) {
                    $locationData = [
                        'user_id' => $r->id,
                        'address' => $r->address,
                        'suburb' => $r->suburb,
                        'state' => $r->state,
                        'post_code' => $r->post_code,
                        'current' => 1
                    ];

                    UserAddress::create($locationData);
                }
            });

            $this->data['users'] = UserModel::withTrashed()->orderBy('last_name')->paginate(15);
            foreach ($this->data['users'] as $user) {
                // $user->locationName = Location::find($user->location)->name;
                //$user->roleName = UserRole::find($user->user_role_id)->name;
                $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
            }

            return back()->with(['systemSuccess' => 'User updated successfully', 'data' => $this->data]);
        } catch (\Exception $e) {
            return back()->withErrors(['systemFail' => 'We couldn\'t update this item. Please try again.' . $e->getMessage()]);
        }
    }

    /**
     * Scope this function delete user  data.
     *
     */

    public function delete($id)
    {
        try {
            UserModel::withTrashed()->find($id)->delete();

            $this->data['users'] = UserModel::withTrashed()->orderBy('last_name')->paginate(15);
            foreach ($this->data['users'] as $user) {
                //$user->locationName = Location::find($user->location)->name;
                //$user->roleName = UserRole::find($user->user_role_id)->name;
                $user->address = UserAddress::where([['current', 1], ['user_id', $user->id]])->first();
            }

            DB::table('users')->where('id', $id)->update(array(
                'is_inactive' => 1,
            ));

            return (['systemSuccess' => 'User deleted successfully', 'data' => $this->data]);
        } catch (\Exception $e) {

            return (['systemFail' => 'Something went wrong on the server. Please try again.']);
        }
    }


    /**
     * Scope this function once declarations can be completed.
     *
     */

    public function showDeclarationEntries($userId, $decId)
    {
        $decItem = json_decode(Declaration::find($decId)->fields);
        $this->data['questions'] = [];

        foreach ($decItem as $field) {
            $this->data['questions'][] = $field->name;
        }

        $decEntries = $this->data['decs'] = DeclarationSent::where(['user_id' => $userId, 'declaration_id' => $decId])->orderBy('id', 'DESC')->paginate(20);

        foreach ($decEntries as $dec) {
            $answers = json_decode($dec->answers);

            //            if(!empty($answers)) {
            //                foreach ($answers as $answer) {
            //                    foreach($answer as $item) {
            //                        $answer->list[] = $item;
            //                    }
            //                }
            //            }
            $dec->answerList = $answers;
            $dec->date = Carbon::parse($dec->created_at)->format('d/m/Y H:i');
            $dec->status = $this->setStatus($dec);
        }

        return back()->with(['data' => $this->data]);
    }

    /**
     * Scope this function set status complete or void.
     *
     */

    private function setStatus($dec)
    {
        if ($dec->complete === 1)
            return config('constant.status.complete');

        if ($dec->void === 1)
            return config('constant.status.void');
    }


    /** Get declaration data **/

    public function showdeclaration($id)
    {
        $this->data['templates'] = DeclarationSent::where(['user_id' => $id])->orderBy('created_at', 'DESC')->with('declaration')->paginate(15);
        $userdata = UserModel::where('id', $id)->withTrashed()->first();
        $this->data['userName'] = $userdata->first_name . ' ' . $userdata->last_name;

        return Inertia::render(
            'Admin/Users/showdeclaration',
            [
                'data' => $this->data,
                'user_id' => $id,
                'systemSuccess' => session('systemSuccess')
            ]
        );
    }
}
