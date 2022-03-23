<?php
namespace App\Http\Controllers\Admin\Pages\IPWhitelist;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\IPWhitelist as WhitelistModel;

class IPWhitelist extends AdminController
{

       /**
     * Scope this function get ip data.
     *
    */

    public function index()
    {
        if(session('data'))
            $this->data = session('data');
        else {
            $this->data['whitelist'] = WhitelistModel::withTrashed()->get();
        }

        return Inertia::render('Admin/IPWhitelist/Index',
            [
                'data' => $this->data,
                'systemSuccess' => session('systemSuccess'),
            ]
        );
    }

      /**
     * Scope this function get all and deleted_at ip data.
     *
    */
    public function get()
    {
        $this->data['whitelist'] = WhitelistModel::withTrashed()->get();
        return back()->with(['data' => $this->data]);
    }

      /**
     * Scope this function create ip list data.
     *
    */

    public function create(Request $r)
    {
        unset($r['id']);

        try {
            WhitelistModel::create($r->all());
            $this->data['whitelist'] = WhitelistModel::withTrashed()->get();

            return back()->with(['systemSuccess' => 'New IP added successfully', 'data' => $this->data]);

        } catch( \Exception $e) {
            return back()->withErrors(['systemFail' => 'Something went wrong on the server. Please try again.' . $e->getMessage()]);
        }
    }

     /**
     * Scope this function update whitelist data.
     *
    */

    public function update(Request $r)
    {
        if( $r->enable ) {
            try {
                WhitelistModel::onlyTrashed()->where('id', $r->id)->restore();

                $this->data['whitelist'] = WhitelistModel::withTrashed()->get();

                return back()->with(['systemSuccess' => 'IP enabled successfully', 'data' => $this->data]);
            } catch( \Exception $e) {
                return back()->withErrors(['systemFail' => 'We couldn\'t enable this template. Please try again.']);
            }
        }

        try {
            WhitelistModel::find($r->id)->update($r->all());

            $this->data['whitelist'] = WhitelistModel::withTrashed()->get();

            return back()->with(['systemSuccess' => 'IP updated successfully', 'data' => $this->data]);

        } catch( \Exception $e) {
            return back()->withErrors(['systemFail' => 'We couldn\'t update this template. Please try again.']);
        }
    }

      /**
     * Scope this function delete whitelist data.
     *
    */

    public function delete($id)
    {
        try{
            WhitelistModel::withTrashed()->find($id)->delete();
            $this->data['whitelist'] = WhitelistModel::withTrashed()->get();

            return back()->with(['systemSuccess' => 'IP deleted successfully', 'data' => $this->data]);
        } catch( \Exception $e) {
            return back()->withErrors(['systemFail' => 'Something went wrong on the server. Please try again.']);
        }
    }
}
