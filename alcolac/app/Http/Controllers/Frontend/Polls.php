<?php
namespace App\Http\Controllers\Frontend;

use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notifiable;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Polls as PollsModel;

class Polls extends BaseController
{

    public function show($token)
    {
        $polls = new PollsModel();
        $users = new Users();
        $data = [
            'complete' => $polls->completed($token),
            'name'  => $users->getFullNameForPoll($token),
            'token' => $token,
            'invalid' => false

        ];

        if (Carbon::now('Australia/Melbourne')->gt(Carbon::parse('2020-09-25 00:00:00', 'Australia/Melbourne')))
            $data['invalid'] = true;

        return view('polls.main', $data);
    }

    public function save(Request $r)
    {
        $poll = new PollsModel();
        $save = $poll->savePoll($r);

        if( $save )
            return response()->json('success');

        return response()->json(false);
    }

    public function verifyDob(Request $r)
    {
        $poll = new PollsModel();
        $users = new Users();

        if( !$poll->completed($r->token)) {
            $dob = $users->verifyDobPoll($r->dob, $r->token);


            if ($dob) {
                return view('polls.footy-poll', ['token' => $r->token]);
            }

            return response()->json($dob);
        }

        return response()->json('complete');
    }

    public function showData($id)
    {
        $poll_data = (new \App\Models\Polls)->getPollData($id);
        $data = $this->setPollData($poll_data);

        return view ('polls.footy-data', $data);
    }

    //TODO make dynamic
    private function setPollData($pollData)
    {
        $data = [];
        $data['total'] = count($pollData);
        $data['complete'] = 0;
        $data['first'] = 0;
        $data['second'] = 0;
        foreach($pollData as $poll) {
            if( $poll->complete == 1)
            {
                $data['complete'] += 1;

                if($poll->answers === 'first')
                    $data['first'] += 1;

                if($poll->answers === 'second')
                    $data['second'] += 1;
            }
        }

        return $data;
    }
}
