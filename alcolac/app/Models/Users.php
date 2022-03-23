<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';

    /**
     * Get the notes for the users.
     */
    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'employee_code',
        'first_name',
        'last_name',
        'dob',
        'email',
        'menuroles',
        'address',
        'groups',
        'phone',
        'password',
        'location',
        'is_inactive',
        'created_at',
        'updated_at'
    ];

    protected $table = 'users';

     /**
     * Create a relation with declaration model to get declaration data.
     *
    */

    /*public function notes()
    {
        return $this->hasMany('App\Models\Notes');
    }*/
    public function userAddress()
    {
        //return $this->belongsTo('App\Models\UserAddress','id', 'address');
        return $this->hasOne('App\Models\UserAddress','address', 'id');
    }

    public function getUserByEmail($email)
    {
        return \DB::table($this->table)->where('email', $email)->first();
    }

    public function getFullNameForQuestionnaire($token)
    {
        $user = \DB::table($this->table)
            ->select('users.first_name', 'users.last_name')
            ->join('declaration_sent as sent', 'users.id', '=', 'sent.user_id')
            ->where('sent.token', '=', $token)
            ->first();

        return $user->first_name . ' ' . $user->last_name;
    }

    public function getFullNameForPoll($token)
    {
        $user = \DB::table($this->table)
            ->select('users.first_name', 'users.last_name')
            ->join('polls_sent as sent', 'users.id', '=', 'sent.user_id')
            ->where('sent.token', '=', $token)
            ->first();

        return $user->first_name . ' ' . $user->last_name;
    }

    public function getFullNameByEmployeeCode($code)
    {
        $user = \DB::table($this->table)
            ->select('first_name', 'last_name')
            ->where('employee_code', '=', $code)
            ->first();

        return $user->first_name . ' ' . $user->last_name;
    }

    public function getAllForUserList()
    {

        return \DB::table($this->table)
            ->select(
                'address.*',
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.phone',
                'users.groups',
                'users.employee_code'
            )
            ->join('user_address as address', 'users.address', '=', 'address.id')
            ->get();
    }

    public function getEmployeeCode($token)
    {
        $user = \DB::table($this->table)
            ->select('users.employee_code')
            ->join('declaration_sent as sent', 'users.id', '=', 'sent.user_id')
            ->where('sent.token', '=', $token)
            ->value('user.employee_code');

        return $user;
    }

    public function getEmployeeLocation($token)
    {
        $user = \DB::table($this->table)
            ->select('users.location')
            ->join('declaration_sent as sent', 'users.id', '=', 'sent.user_id')
            ->where('sent.token', '=', $token)
            ->value('user.location');

        return $user;
    }

    public function getAllForSundayIntegriti()
    {
         return \DB::table($this->table)
            ->select('employee_code')
             ->where('location', '=', 'Colac')
             ->where('is_inactive', '=', 0)
            ->get();
    }

    public function getLocations()
    {
        return \DB::table($this->table)
            ->select('location')
            ->distinct()
            ->get();
    }

    public function getGroups()
    {
        return \DB::table($this->table)
            ->select('groups')
            ->distinct()
            ->get();
    }

    public function getGroupsByLocation($location)
    {
        return \DB::table($this->table)
            ->select('groups')
            ->where('location', '=', $location)
            ->distinct()
            ->get();
    }

    public function getAllIdsAsArray($group = null)
    {
        if($group === null || $group === 'none') {
            return \DB::table($this->table)
                ->select('id')
                ->where('is_inactive', '=', 0)
                ->get()->toArray();
        } else {
            return \DB::table($this->table)
                ->select('id')
                ->where('groups', 'LIKE', "%$group%")
                ->where('is_inactive', '=', 0)
                ->get()->toArray();
        }
    }

    public function getColacIdsAsArray($group = null)
    {
        if($group === null || $group === 'none') {
            return \DB::table($this->table)
                ->select('id')
                ->where('is_inactive', '=', 0)
                ->where('location', '=', 'Colac')
                ->get()->toArray();
        } else {
            return \DB::table($this->table)
                ->select('id')
                ->where('groups', 'LIKE', "%$group%")
                ->where('is_inactive', '=', 0)
                ->where('location', '=', 'Colac')
                ->get()->toArray();
        }
    }

    public function getMultipleForMessaging($group = null)
    {
        if($group === null) {
            return \DB::table($this->table)
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'sent.token', 'users.location')
                ->join('declaration_sent as sent', 'users.id', '=', 'sent.user_id')
                ->leftJoin('declaration_sent as sent2', function($query){
                    $query->on('sent2.user_id', '=', 'sent.user_id')
                          ->on('sent.id', '<', 'sent2.id')
                          ->on('sent.declaration_id', '=', 'sent2.declaration_id');
                })
                ->whereNull('sent2.id')
                // ->where('sent.complete', '=', 0)
                ->where('sent.void', '=', 0)
                ->where('users.is_inactive', '=', 0)
                ->groupBy(['sent.user_id', 'sent.declaration_id'])
                ->get();

        } else {
            return \DB::table($this->table)
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'sent.token', 'users.location')
                ->join('declaration_sent as sent', 'users.id', '=', 'sent.user_id')
                ->leftJoin('declaration_sent as sent2', function($query){
                    $query->on('sent2.user_id', '=', 'sent.user_id')
                          ->on('sent.id', '<', 'sent2.id')
                          ->on('sent.declaration_id', '=', 'sent2.declaration_id');
                })
                ->whereNull('sent2.id')
                ->where('users.groups', 'LIKE', "%$group%")
                // ->where('sent.complete', '=', 0)
                ->where('sent.void', '=', 0)
                ->where('users.is_inactive', '=', 0)
                ->groupBy(['sent.user_id', 'sent.declaration_id'])
                ->get();
        }
    }

    public function getMultipleColacForMessaging($group = null)
    {
        if($group === null) {
            return \DB::table($this->table)
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'sent.token', 'users.location')
                ->join('declaration_sent as sent', 'users.id', '=', 'sent.user_id')
                ->leftJoin('declaration_sent as sent2', function($query){
                    $query->on('sent2.user_id', '=', 'sent.user_id')
                          ->on('sent.id', '<', 'sent2.id')
                          ->on('sent.declaration_id', '=', 'sent2.declaration_id');
                })
                ->whereNull('sent2.id')
                ->where('sent.complete', '=', 0)
                ->where('sent.void', '=', 0)
                ->where('users.is_inactive', '=', 0)
                ->where('users.location', '=', 'Colac')
                ->groupBy(['sent.user_id', 'sent.declaration_id'])
                ->get();

        } else {
            return \DB::table($this->table)
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'sent.token', 'users.location')
                ->join('declaration_sent as sent', 'users.id', '=', 'sent.user_id')
                ->leftJoin('declaration_sent as sent2', function($query){
                    $query->on('sent2.user_id', '=', 'sent.user_id')
                          ->on('sent.id', '<', 'sent2.id')
                          ->on('sent.declaration_id', '=', 'sent2.declaration_id');
                })
                ->whereNull('sent2.id')
                ->where('users.groups', 'LIKE', "%$group%")
                ->where('sent.complete', '=', 0)
                ->where('sent.void', '=', 0)
                ->where('users.is_inactive', '=', 0)
                ->where('users.location', '=', 'Colac')
                ->groupBy(['sent.user_id', 'sent.declaration_id'])
                ->get();
        }
    }

    public function getMultipleForMessagingForLocation($group = null, $location = 'Colac')
    {
        if($group === null) {
            return \DB::table($this->table)
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'users.location')
                // ->join('declaration_sent as sent', 'users.id', '=', 'sent.user_id')
                // ->leftJoin('declaration_sent as sent2', function($query){
                //     $query->on('sent2.user_id', '=', 'sent.user_id')
                //           ->on('sent.id', '<', 'sent2.id')
                //           ->on('sent.declaration_id', '=', 'sent2.declaration_id');
                // })
                // ->whereNull('sent2.id')
                // ->where('sent.complete', '=', 0)
                // ->where('sent.void', '=', 0)
                ->where('users.is_inactive', '=', 0)
                ->where('users.location', '=', $location)
                // ->groupBy(['sent.user_id', 'sent.declaration_id'])
                ->get();

        } else {
            return \DB::table($this->table)
                ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.phone', 'users.location')
                // ->join('declaration_sent as sent', 'users.id', '=', 'sent.user_id')
                // ->leftJoin('declaration_sent as sent2', function($query){
                //     $query->on('sent2.user_id', '=', 'sent.user_id')
                //           ->on('sent.id', '<', 'sent2.id')
                //           ->on('sent.declaration_id', '=', 'sent2.declaration_id');
                // })
                // ->whereNull('sent2.id')
                ->where('users.groups', 'LIKE', "%$group%")
                // ->where('sent.complete', '=', 0)
                // ->where('sent.void', '=', 0)
                ->where('users.is_inactive', '=', 0)
                ->where('users.location', '=', $location)
                // ->groupBy(['sent.user_id', 'sent.declaration_id'])
                ->get();
        }
    }

    public function getIdForQuestionnaire($token)
    {
        $user = \DB::table($this->table)
            ->select('users.id')
            ->join('declaration_sent as sent', 'users.id', '=', 'sent.user_id')
            ->where('sent.token', '=', $token)
            ->value('users.id');

        return $user;
    }

    public function verifyDob($dob, $token)
    {
        $db_dob = $this->getDobForVerification($token);

        return $this->formatInputDate($dob) === $db_dob ? true : false;
    }

    public function verifyDobPoll($dob, $token)
    {
        $db_dob = $this->getDobForVerificationPoll($token);

        return $this->formatInputDate($dob) === $db_dob ? true : false;
    }

    private function getDobForVerification($token)
    {
        $user = \DB::table($this->table)
            ->select('users.dob')
            ->join('declaration_sent as sent', 'users.id', '=', 'sent.user_id')
            ->where('sent.token', '=', $token)
            ->value('users.dob');

        return $user;
    }

    private function getDobForVerificationPoll($token)
    {
        $user = \DB::table($this->table)
            ->select('users.dob')
            ->join('polls_sent as sent', 'users.id', '=', 'sent.user_id')
            ->where('sent.token', '=', $token)
            ->value('users.dob');

        return $user;
    }

    // For self sending
    public function verifyUserWithDobPhone($dob, $phone)
    {
        $user_id = $this->getUserForSelfSend($dob, $phone);

        if($user_id )
            return $user_id;

        return false;
    }

    public function formatPhone($phone)
    {
        if(substr($phone, 0, 1) === '0')
            $phone = substr_replace($phone, '+61', 0, 1);

        return $phone;
    }

    private function getUserForSelfSend($dob, $phone)
    {
        $user = \DB::table($this->table)
            ->select('id', 'first_name', 'last_name')
            ->where(['phone' => $phone, 'dob' => $dob])
            ->get()->toArray();

        return $user;
    }

    public function formatInputDate($date)
    {
        $date_array = explode('/', $date);

        return implode('-', array_reverse($date_array));
    }

    public function getAddress($id)
    {
        $address = \DB::table($this->table)
            ->select('user_address.*')
            ->join('user_address', 'users.address', '=', 'user_address.id')
            ->where('users.id', '=', $id)
            ->first();

        return $address;
    }

    public function getUserIdbyEmail($email)
    {
        $address = \DB::table($this->table)
            ->where('email', $email)
            ->value('id');

        return $address;
    }

    public function getUserById($id)
    {
        $address = \DB::table($this->table)
            ->select('first_name', 'last_name', 'email', 'phone', 'location', 'employee_code')
            ->where('id', $id)
            ->get();

        return $address;
    }
}
