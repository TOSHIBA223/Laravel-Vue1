<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class QuestionnaireSent extends Model
{
    use SoftDeletes, Notifiable;

    protected $primaryKey = 'id';

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'questionnaire_id',
        'user_id',
        'key',
        'answers',
        'complete'
    ];

    protected $table = 'questionnaire_sent';

    /**
     * Scope this function get questionnaire complete data.
     *
    */

    public function questionnaireComplete($token)
    {
        $questionnaire = \DB::table($this->table)
            ->where('key', $token)
            ->value('complete');

        return $questionnaire === 1;
    }

    /**
     * Scope this function get data for csv with join some tables.
     *
    */

    public function getForCSV($startDate, $endDate)
    {
        if($startDate !== null && $endDate !== null) {
            return \DB::table($this->table . ' as dec')
                ->select(
                    [
                        'dec.created_at',
                        'dec.updated_at',
                        'dec.questionnaire_id',
                        'users.employee_code',
                        'users.first_name',
                        'users.last_name',
                        'users.phone',
                        'dec.answers',
                        'dec.complete',
                        'dec.void',
                        'users.groups',
                        'users.location'
                    ]
                )
                ->join('users', 'users.id', '=', 'dec.user_id')
                ->where('dec.created_at', '>=', date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00')))
                ->where('dec.created_at', '<=', date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59')))
                ->get();
        } else {
            return \DB::table($this->table . ' as dec')
                ->select(
                    [
                        'dec.created_at',
                        'dec.updated_at',
                        'dec.questionnaire_id',
                        'users.employee_code',
                        'users.first_name',
                        'users.last_name',
                        'users.phone',
                        'dec.answers',
                        'dec.complete',
                        'dec.void',
                        'users.groups',
                        'users.location'
                    ]
                )
                ->join('users', 'users.id', '=', 'dec.user_id')
                ->get();
        }
    }

    /**
     * Scope this function token and userid match data.
     *
    */

    public function doesUserMatchToken($token, $userId)
    {
        $user = \DB::table($this->table)
            ->where(['user_id' => $userId, 'key' => $token])
            ->get();

        if( $user->count() )
            return true;

        return false;
    }

    /**
     * Scope this function get the Declaration data.
     *
    */

    public function getDeclarationForUser($userId = false)
    {
        if( !$userId ) return 'User ID can not be blank';

        return \DB::table($this->table)
            ->where('user_id', '=', $userId)
            ->get()->toArray();
    }

      /**
     * Scope this function get active questionnare keys.
     *
    */

    public function getActiveQuestionnaireKeys($userId)
    {
        return \DB::table($this->table)
            ->select('key')
            ->where('user_id', $userId)
            ->where('complete', 0)
            ->get()->toArray();
    }

     /**
     * Scope this function get  questionnare data with token.
     *
    */


    public function getQuestionnaire($token)
    {
        return \DB::table($this->table)
            ->where('key', $token)
            ->get()->toArray();
    }

     /**
     * Scope this function check exits data.
     *
    */

    public function exists($token)
    {
        $questionnaire = \DB::table($this->table)
            ->where('key', $token)
            ->get();

        if( $questionnaire->count() )
            return true;
        else
            return false;
    }

     /**
     * Scope this function update questionnare sent table.
     *
    */

    public function testComplete( $token, $answers)
    {
        return \DB::table($this->table)
            ->where('key', $token)
            ->update(['complete' => true, 'answers' => $answers, 'updated_at' => Carbon::now('Australia/Melbourne')]);
    }

     /**
     * Scope this function get admin verified value.
     *
    */

    public function isAdminVerified($token)
    {
        return \DB::table($this->table)
            ->select('admin_verified')
            ->where('key', $token)
            ->value('admin_verified');


    }

     /**
     * Scope this function create balnk questionnare data.
     *
    */

    public function createBlank($userId, $questionnaireId, $unencryptedKey, $selfGenerated = 0, $adminVerified = 0)
    {
        $now = \Carbon\Carbon::now('Australia/Melbourne')->toDateTimeString();
        try{
            $this->voidOldTests([$userId]);

            \DB::table($this->table)
            ->insert([
                'user_id' => $userId,
                'questionnaire_id' => $questionnaireId,
                'key' => $unencryptedKey,
                'answers' => 0,
                'complete' => 0,
                'void' => 0,
                'sent_to_user' => 0,
                'created_at' => $now,
                'updated_at' => $now,
                'self_generated' => $selfGenerated,
                'admin_verified' => $adminVerified
            ]);


            return true;

        }
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }

    public function isVoid($key)
    {
        return \DB::table($this->table)
            ->select('void')
            ->where('key', '=', $key)
            ->value('void');
    }

     /**
     * Scope this function get questionnare create date.
     *
    */

    public function getQuestionnaireCreationDate($key)
    {
        return \DB::table($this->table)
            ->select('created_at')
            ->where('key', '=', $key)
            ->value('created_at');
    }

    /**
     * Scope this function create questionnare batch.
     *
    */

    public function createBlankBatch(array $userIds, $questionnaireId)
    {
        $now = \Carbon\Carbon::now('Australia/Melbourne')->toDateTimeString();
        $data = null;
        $ids = [];
        foreach($userIds as $user)
        {
            $key = $this->generateKey();
            $ids[] = $user->id;

            $data[] = array(
                'user_id' => $user->id,
                'questionnaire_id' => $questionnaireId,
                'key' => $key,
                'answers' => 0,
                'complete' => 0,
                'void' => 0,
                'sent_to_user' => 1,
                'created_at'=> $now,
                'updated_at'=> $now,
                'self_generated' => 0,
                'admin_verified' => 0
            );
        }

        try{
            $this->voidOldTests($ids);

            \DB::table($this->table)
            ->insert($data);

            return true;

        }
        catch(\Exception $e)
        {
            return $e->getTrace();
        }
    }

    public function voidTestsForVoidEmail($userIds)
    {
        return $this->voidOldTests($userIds);
    }

    public function voidOldTests($userIds)
    {
        $update = \DB::table($this->table)
            ->whereIn('user_id', $userIds)
            ->where('complete', '=', 0)
            ->update(['void' => 1]);

        return $update;
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
     * Scope this function update sent status.
     *
    */
    public function updateSentStatus($token)
    {
        \DB::table($this->table)
            ->where('key', '=', $token)
            ->update(['sent_to_user' => 1]);
    }

     /**
     * Scope this function get complete stats.
     *
    */
    public function getNightlyCompletionStats()
    {
        $today = Carbon::now(config('constant.timeZone'));
        $yesterday = Carbon::yesterday(config('constant.timeZone'));
        return \DB::table($this->table . ' as sent')
            ->select('users.employee_code', 'sent.answers', 'sent.complete')
            ->where('sent.created_at', '>=', $yesterday)
            ->where('sent.created_at', '<=', $today)
            ->where('sent.void', '=', 0)
            ->where('sent.complete', '=', 0)
            ->where('users.location', '=', 'Colac')
            ->join('users', 'users.id', '=', 'sent.user_id')
            ->get();
    }

    /**
     * Scope this function get incomplete sunshine set.
     *
    */

    public function getIncompleteSunshineTests()
    {
        $today = Carbon::now(config('constant.timeZone'));
        $yesterday = Carbon::yesterday(config('constant.timeZone'));
        return \DB::table($this->table . ' as sent')
            ->select('sent.created_at', 'sent.id', 'users.employee_code', 'users.first_name', 'users.last_name', 'users.phone' )
            ->where('sent.created_at', '>=', $yesterday)
            ->where('sent.created_at', '<=', $today)
            ->where('sent.void', '=', 0)
            ->where('sent.complete', '=', 0)
            ->where('users.location', '=', 'Sunshine')
            ->join('users', 'users.id', '=', 'sent.user_id')
            ->get();
    }

    /**
     * Scope this function get all sunshine set.
     *
    */

    public function getAllSunshineTests()
    {
        $today = Carbon::now(config('constant.timeZone'));
        $yesterday = Carbon::yesterday(config('constant.timeZone'));
        return \DB::table($this->table . ' as sent')
            ->select('sent.created_at', 'sent.updated_at', 'sent.id', 'users.id as user_id',
                'users.employee_code', 'users.first_name', 'users.last_name',
                'users.phone', 'sent.answers', 'sent.complete', 'users.groups', 'users.location')
            ->where('sent.created_at', '>=', $yesterday)
            ->where('sent.created_at', '<=', $today)
            ->where('users.location', '=', 'Sunshine')
            ->where('users.is_inactive', '=', 0)
            ->join('users', 'users.id', '=', 'sent.user_id')
            ->get();
    }

    public function getSingleCompletionStats($token)
    {

        return \DB::table($this->table . ' as sent')
            ->select('sent.id', 'users.employee_code', 'sent.complete', 'sent.answers')
            ->where('key', '=', $token)
            ->join('users', 'users.id', '=', 'sent.user_id')
            ->get();
    }

     /**
     * Scope this function get self generated value.
     *
    */

    public function isSelfGenerated($token)
    {
        return \DB::table($this->table)
            ->where('key', '=', $token)
            ->where('self_generated', '=', 1)
            ->value('self_generated');
    }

}
