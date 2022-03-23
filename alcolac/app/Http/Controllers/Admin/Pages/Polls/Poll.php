<?php
namespace App\Http\Controllers\Admin\Pages\Polls;

use App\Builders\Tokens;
use App\Models\Location;
use App\Models\PollSent;
use App\Models\SMSTemplate;
use App\Models\User;
use App\Services\SMS;
use Inertia\Inertia;
use App\Http\Controllers\Admin\AdminController;
use App\Models\Poll as PollModel;
use Illuminate\Http\Request;
use App\Models\PermissionRoles;

class Poll extends AdminController
{

    /**
     * Scope this function get all poll and user  data.
     *
    */

    public function index()
    {
        if(session('data'))
            $this->data = session('data');
        else
            $this->data['templates'] = PollModel::all();

        $this->data['locations'] = Location::select('id', 'name')->orderby('name', 'ASC')->get();
        $this->data['groups'] = User::select('groups')->distinct()->get();
		$roleId = \Auth::user()->user_role_id;
		$permission = PermissionRoles::where(['role_id'=>$roleId,'module_id'=>2])->first();
		$this->data['permission'] = $permission;
		if($permission->perm_view == 1){

        return Inertia::render('Admin/Polls/Index', ['data' => $this->data, 'systemSuccess' => session('systemSuccess')]);

		} else {
			 return back()->withErrors(['systemFail' => 'You are not authorised this access.']);
		}
    }

    /**
     * Scope this function get all poll data.
     *
    */
    public function get($id)
    {
        $this->data['templates'] = PollModel::all();
        if($id)
            $this->data['templates'] = PollModel::find($id);

        return back()->with(['data' => $this->data]);
    }

    /**
     * Scope this function create poll data.
     *
    */

    public function create(Request $r)
    {
        unset($r['id']);

        try {
            $fields = json_encode($r->fields);
            PollModel::create(
                [
                    'name' => $r->name,
                    'fields' => $fields,
                    'valid_to' => $r->valid_to,
                    'description' => $r->description
                ]
            );

            $this->data['templates'] = PollModel::all();

            return back()->with(['systemSuccess' => 'New poll created successfully', 'data' => $this->data]);
        } catch( \Exception $e) {
            return back()->withErrors(['systemFail' => 'We couldn\'t create your new poll.' . $e->getMessage()]);
        }
    }

     /**
     * Scope this function update poll data.
     *
    */

    public function update(Request $r)
    {
        try {
            PollModel::find($r->id)->update($r->all());

            $this->data['templates'] = PollModel::all();

            return back()->with(['systemSuccess' => 'Poll updated successfully', 'data' => $this->data]);

        } catch( \Exception $e) {
            return back()->withErrors(['systemFail' => 'We couldn\'t update this Poll. Please try again.']);
        }
    }

    /**
     * Scope this function get poll and sent poll data.
     *
    */

    public function getData(Request $r)
    {

        $poll = PollModel::find($r->id);

        $sent_polls = $poll->sent->where('void', 0);

        $complete_polls = $sent_polls->where('complete', 1);

        $this->data['complete_count'] = count($complete_polls);
        $this->data['total_count'] = count($sent_polls);
        $this->data['poll_answers'] = json_decode($poll->fields);

        foreach($complete_polls->groupBy('answer') as $key => $answer)
        {
            $this->data['user_answers'][$key] = count($answer);
        }

        return back()->with(['data' => $this->data]);
    }

    /**
     * Scope this function send poll data.
     *
    */

    public function send(Request $r)
    {

        try {
            if ($r->data['location'] === 'all' && $r->data['group'] === 'all') {
                $users = User::select('id', 'location', 'phone')->where('phone', '!=', 'null')->get();
            } elseif ($r->data['location'] !== 'all' && $r->data['group'] === 'all') {
                $users = User::select('id', 'location', 'phone')->where(
                    [
                        ['phone', '!=', 'null'],
                        ['location', $r->data['location']]
                    ]
                )->get();
            } elseif ($r->data['location'] === 'all' && $r->data['group'] !== 'all') {
                $users = User::select('id', 'location', 'phone')->where(
                    [
                        ['phone', '!=', 'null'],
                        ['groups', $r->data['group']]
                    ]
                )->get();
            } else {
                $users = User::select('id', 'location', 'phone')->where(
                    [
                        ['phone', '!=', 'null'],
                        ['groups', $r->data['group']],
                        ['location', $r->data['location']]
                    ]
                )->get();
            }


            $location_groups = $users->groupBy('location');



            foreach ($location_groups as $key => $location) {
                $user_list = [];
                foreach ($location as $user) {
                    $token = Tokens::verification();
                    $this->voidPoll($user->id, $r->id);
                    $new_dec = PollSent::create(
                        [
                            'user_id' => $user->id,
                            'poll_id' => $r->id,
                            'token' => $token,
                            'sent' => 1
                        ]
                    );

                    if ($new_dec)
                        $user_list[] = $user;
                }

                // TODO add poll text message to poll creator page (ID needs to be changed to poll SMS Template id)
                $sms_content = SMSTemplate::find($r->id);
                $sms_content = $sms_content->content;
                $sms_service = new SMS();
                $sms_service->sendMultiple($sms_content, $user_list, $r->id);

            }

            return back()->with(['systemSuccess' => 'Sent poll successfully.']);
        } catch( \Exception $e) {
            return back()->withErrors(['systemFail' => 'We couldn\'t send this poll' . $e->getMessage()]);
        }
    }

    /**
     * Scope this function set void poll data.
     *
    */

    public function voidPoll($userId, $pollId)
    {
        $exists = PollSent::where([
            'user_id' => $userId,
            'poll_id' => $pollId,
            'complete' => 0,
            'void' => 0
        ])->orderBy('id', 'DESC')->first();

        if( $exists )
            $exists->update(['void' => 1]);
    }
}
