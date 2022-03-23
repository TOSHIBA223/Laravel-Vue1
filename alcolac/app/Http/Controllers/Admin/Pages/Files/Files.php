<?php

namespace App\Http\Controllers\Admin\Pages\Files;

use App\Models\File;
use App\Models\FileLog;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\URL;
use App\Actions\Uploader;
use App\Models\PermissionRoles;
use App\Builders\Tokens;

class Files extends AdminController
{

    /**
     * Scope this function get file url data.
     *
     */

    public function index()
    {
        if (session('data')) {
            $this->data = session('data');
            $this->data['baseUrl'] = URL::to('/') . '/';
        } else {
            $this->data['files'] = File::all();
            $this->data['baseUrl'] = URL::to('/') . '/';
        }
        $roleId = \Auth::user()->user_role_id;
        $permission = PermissionRoles::where(['role_id' => $roleId, 'module_id' => 3])->first();
        $this->data['permission'] = $permission;
        if ($permission->perm_view == 1) {
            return Inertia::render(
                'Admin/Files/Index',
                [
                    'data' => $this->data,
                    'systemSuccess' => session('systemSuccess'),
                    'systemFail' => session('systemFail'),
                ]
            );
        } else {
            return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
        }
    }

    /**
     * Scope this function get file url data.
     *
     */

    public function get()
    {
        $this->data['files'] = File::all();
        return back()->with(['data' => $this->data]);
    }

    /**
     * Scope this function set file uploads directory.
     *
     */
    public function create(Request $r)
    {
        try {
            Uploader::upload($r->newFile, 'admin_upload', true);

            $this->data['files'] = File::all();

            return ['systemSuccess' => 'New file added successfully.', 'data' => $this->data];
        } catch (\Exception $e) {
            return ['systemFail' => 'Something went wrong on the server. Please try again.'];
        }
    }

    public function delete(Request $r)
    {
        $file = File::where('id', $r->id)->forceDelete();
        $this->data['files'] = File::all();

        return ['systemSuccess' => 'File delete successfully.', 'data' => $this->data];
    }

    /**
     * Scope this function set file enabled.
     *
     */

    public function update(Request $r)
    {
        if ($r->action === 'open') {
            try {
                File::where('id', $r->id)->update(['archived' => 0]);

                $this->data['files'] = File::all();

                return ['systemSuccess' => 'File enabled successfully.'];
            } catch (\Exception $e) {
                return ['systemFail' => 'We couldn\'t enable this template. Please try again.'];
            }
        }

        try {
            File::where('id', $r->id)->update(['archived' => 1]);

            $this->data['files'] = File::all();

            return ['systemSuccess' => 'File archived successfully.'];
        } catch (\Exception $e) {
            return ['systemFail' => 'We couldn\'t update this template. Please try again.'];
        }
    }

    public function filelog($id)
    {
        $this->data['templates'] = FileLog::where(['file_id' => $id])->orderBy('id', 'DESC')->take(30)->get();
        $fileData = File::where('id', $id)->first();
        $this->data['userName'] = $fileData->name;
        return Inertia::render(
            'Admin/Files/filelog',
            [
                'data' => $this->data,
                'systemSuccess' => session('systemSuccess')
            ]
        );
    }
}
