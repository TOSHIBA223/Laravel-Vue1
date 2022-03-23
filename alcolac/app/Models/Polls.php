<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Request;
use App\Models\PollSent;

class Polls extends Model
{
    use Notifiable;

    protected $primaryKey = 'id';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'poll_id',
        'user_id',
        'key',
        'answers',
        'complete'
    ];

    protected $table = 'polls';
    protected $sentTable = 'polls_sent';

     /**
     * Scope this function get data from poll sent table with token.
     *
    */

    public function completed($token)
    {
        /*$poll = \DB::table($this->sentTable)
            ->where('key', $token)
            ->value('complete');*/

         $poll = PollSent::where('key', $token)->value('complete');

        return $poll === 1;
    }

    /**
     * Scope this function get data from poll with poll sent table.
     *
    */

    public function getPollData($id)
    {
        return \DB::table($this->table)
            ->select('polls_sent.*')
            ->join('polls_sent', 'polls_sent.poll_id', '=', 'polls.id')
            ->get();
    }

     /**
     * Scope this function update poll data.
     *
    */

    public function savePoll(\Symfony\Component\HttpFoundation\Request $r)
    {
       /* return \DB::table($this->sentTable)
            ->where('key', '=', $r->token)
            ->update([
                'complete' => true,
                'answers' => $r->answers,
                'updated_at' => Carbon::now('Australia/Melbourne')
            ]);*/


            return   PollSent::where('key', '=', $r->token)->update([
                'complete' => true,
                'answers' => $r->answers,
                'updated_at' => Carbon::now('Australia/Melbourne')
            ]);
    }

     /**
     * Scope this function generate ranodm key.
     *
    */

    public function generateKey()
    {
        $key = $this->keyStringGen();
        $does_string_exist = $this->exists($key);

        if($does_string_exist === true)
        {
            $this->generateKey();
        } else {
            return $key;
        }
    }

    /**
     * Scope this function generate ranodm key.
     *
    */
    protected function keyStringGen()
    {
        $characters = config('constant.CharaterString');
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 8; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Scope this function check exist data.
     *
    */

    public function exists($token)
    {
        /*$poll = \DB::table($this->sentTable)
            ->where('key', $token)
            ->get();*/

           $poll =  PollSent::where('key', $token)->get();

        if( $poll->count() )
            return true;
        else
            return false;
    }

    /**
     * Scope this function insert data in sent poll tables.
     *
    */

    public function createPoll($id)
    {
        try{
            $now = \Carbon\Carbon::now(config('constant.timeZone'))->toDateTimeString();
           /* \DB::table($this->sentTable)
                ->insert([
                    'user_id' => $id,
                    'poll_id' => 1,
                    'key' => $this->generateKey(),
                    'answers' => null,
                    'complete' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);*/

            $PollSent = new PollSent;
            $PollSent->user_id = $id;
            $PollSent->poll_id = 1;
            $PollSent->key = $this->generateKey();
            $PollSent->answers = null;
            $PollSent->complete = 0;
            $PollSent->created_at = $now;
            $PollSent->updated_at = $now;
            $PollSent->save();

        } catch( \Exception $e) {
            return $e->getMessage();
        }
    }

     /**
     * Scope this function get data with relation.
     *
    */
    public function getForSending($id)
    {
        return \DB::table($this->sentTable . ' as sent')
            ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'sent.key')
            ->join('users', 'users.id', '=', 'sent.user_id')
            ->where('sent.complete', '=', 0)
            ->where('users.is_inactive', '=', 0)
            ->where('sent.poll_id', '=', $id)
            ->get();
    }
}
