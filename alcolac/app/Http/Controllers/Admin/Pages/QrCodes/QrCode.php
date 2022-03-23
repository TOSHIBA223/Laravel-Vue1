<?php

namespace App\Http\Controllers\Admin\Pages\QrCodes;

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
use App\Models\AdminUsers;
use Illuminate\Support\Facades\Hash;
use App\Models\QrCodes;
use Illuminate\Support\Facades\Storage;
use App\Models\QrChecks;


class QrCode extends AdminController
{



    public function index()
    {


        if (session('data'))
            $this->data = session('data');

        $this->data['users'] = QrCodes::withTrashed()->orderBy('id')->paginate(15);

        $this->data['locations'] =   DB::table('users')->select('location')->distinct()->get();

        $this->data['roles'] = UserRole::all();
        $this->data['declarations'] = Declaration::all();
        $roleId = \Auth::user()->user_role_id;

        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 11])->first();


        $this->data['permission'] = $permission;


        if ($permission->perm_view == 1) {

            return Inertia::render('Admin/QrCodes/Index', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
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
            $this->data['users'] = (new QrChecks)->searchParams($r->search);

            foreach ($this->data['users'] as $user) {
                // $user->locationName = Location::find($user->location)->name;
                $user->roleName = '';
                $user->address = '';
            }

            $roleId = \Auth::user()->user_role_id;
            $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 11])->first();
            $this->data['permission'] = $permission;
            if ($permission->perm_view == 1) {

                return Inertia::render('Admin/QrCodes/showqr', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
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


            $url = url('/') . '/qr-code/' . $r->area_name;

            $img = \QrCode::size(500)
                ->format('png')
                ->generate($url, public_path('images/' . $r->area_name . '.png'));
            //Storage::disk('local')->put('images/1/smalls'.'/'.$output_file, $img, 'public');



            $userData = [
                'area_name' => $r->area_name,
                'qr_image' => $r->area_name,
                'qr_url' => $url,
                'status' => 1
            ];
            $new_user = QrCodes::create($userData);




            $this->data['users'] = QrCodes::withTrashed()->orderBy('id')->paginate(15);


            return back()->with(['systemSuccess' => 'QR Code Generated successfully', 'data' => $this->data]);
        } catch (\Exception $e) {

            return back()->withErrors(['systemFail' => 'Something went wrong on the server. Please try again.' . $e->getMessage()]);
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

        $this->data['users'] = AdminUsers::withTrashed()->orderBy('id')->with('role')->paginate(15);



        foreach ($this->data['users'] as $user) {

            $user['locationName'] = Location::find($user->location)->name ?? 'india';
            //$user['locationName'] = UserModel::find($user->location)->name ?? 'india';
            $user['roleName'] = UserRole::find($user->user_role_id)->name ?? 'admin';
            $user['address'] = '';
        }



        $this->data['locations'] =   DB::table('users')->select('location')->distinct()->get();

        $this->data['roles'] = UserRole::all();
        $this->data['declarations'] = Declaration::all();
        $roleId = \Auth::user()->user_role_id;

        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 11])->first();


        $this->data['permission'] = $permission;
        return Inertia::render('Admin/AdminUsers/updateprofile', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
    }

    public function update(Request $r)
    {

        if ($r->enable) {
            try {
                QrCodes::onlyTrashed()->where('id', $r->id)->restore();

                $this->data['users'] = QrCodes::withTrashed()->orderBy('id')->paginate(15);

                DB::table('qr_codes')->where('id', $r->id)->update(array(
                    'status' => 1
                ));

                return (['systemSuccess' => 'QR Codes enabled successfully', 'data' => $this->data]);
            } catch (\Exception $e) {

                return (['systemFail' => 'We couldn\'t enable this menu. Please try again.']);
            }
        }

        try {


            $img = \QrCode::size(500)
                ->format('png')
                ->generate($r->area_name, public_path('images/' . $r->area_name . '.png'));
            //Storage::disk('local')->put('images/1/smalls'.'/'.$output_file, $img, 'public');

            $url = url('/') . '/qr-code/' . $r->area_name;

            DB::table('qr_codes')->where('id', $r->id)->update([
                'area_name' => $r->area_name,
                'qr_image' => $r->area_name,
                'qr_url' => $url,
                'status' => 1

            ]);

            $this->data['users'] = QrCodes::withTrashed()->orderBy('id')->paginate(15);

            return back()->with(['systemSuccess' => 'QR Code updated successfully', 'data' => $this->data]);
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
            QrCodes::withTrashed()->find($id)->delete();

            $this->data['users'] = QrCodes::withTrashed()->orderBy('id')->paginate(15);


            DB::table('qr_codes')->where('id', $id)->update(array(
                'status' => 0,
            ));

            return (['systemSuccess' => 'QR Code deleted successfully', 'data' => $this->data]);
        } catch (\Exception $e) {
            return (['systemFail' => 'Something went wrong on the server. Please try again.']);
        }
    }


    public function permandelete($id)
    {
        try {
            QrCodes::where('id', $id)->forceDelete();

            $this->data['users'] = QrCodes::withTrashed()->orderBy('id')->paginate(15);
            foreach ($this->data['users'] as $user) {
                //$user->locationName = Location::find($user->location)->name;
                $user->roleName = '';
                $user->address = [];
            }

            return (['systemSuccess' => 'QR Code deleted successfully', 'data' => $this->data]);
        } catch (\Exception $e) {

            return (['systemFail' => 'Something went wrong on the server. Please try again.']);
        }
    }

    public function showRATResult()
    {
        $token = 'RATCovidtesting';
        if (session('data'))
            $this->data = session('data');

        $this->data['users'] = QrChecks::where('location', $token)->paginate(15);
        $this->data['locations'] = DB::table('users')->select('location')->distinct()->get();
        $this->data['roles'] = UserRole::all();
        $this->data['declarations'] = Declaration::all();
        $roleId = \Auth::user()->user_role_id;

        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 12])->first();

        $this->data['token'] = $token;
        $this->data['permission'] = $permission;


        if ($permission->perm_view == 1) {
            return Inertia::render('Admin/QrCodes/showqr', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
        } else {
            return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
        }
    }

    public function showqr($token)
    {
        if (session('data'))
            $this->data = session('data');

        $this->data['users'] = QrChecks::where('location', $token)->paginate(15);

        $this->data['locations'] =   DB::table('users')->select('location')->distinct()->get();

        $this->data['roles'] = UserRole::all();
        $this->data['declarations'] = Declaration::all();
        $roleId = \Auth::user()->user_role_id;

        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 12])->first();

        $this->data['token'] = $token;
        $this->data['permission'] = $permission;


        if ($permission->perm_view == 1) {

            return Inertia::render('Admin/QrCodes/showqr', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
        } else {
            return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
        }
    }


    public function contractor(Request $r)
    {

        if (session('data'))
            $this->data = session('data');

        $this->data['users'] = UserModel::where('is_qr_employee', 1)->paginate(15);

        $this->data['locations'] =   DB::table('users')->select('location')->distinct()->get();

        $this->data['roles'] = UserRole::all();
        $this->data['declarations'] = Declaration::all();
        $roleId = \Auth::user()->user_role_id;

        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 12])->first();

        $this->data['token'] = '';
        $this->data['permission'] = $permission;


        if ($permission->perm_view == 1) {

            return Inertia::render('Admin/QrCodes/contractor', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);
        } else {
            return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
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
