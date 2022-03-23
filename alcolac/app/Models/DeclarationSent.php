<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Builders\Tokens;
class DeclarationSent extends Model
{
    protected $table = 'declaration_sent';

    protected $fillable = [
        'declaration_id', 'user_id', 'token', 'answers', 'complete', 'void', 'sent', 'short_valid', 'passed', 'created_at', 'updated_at'
    ];

    protected $attributes = [
        'answers' => null,
        'complete' => 0,
        'void' => 0,
        'sent' => 0,
        'short_valid' => 0,
        'passed' => 0,
    ];

    /**
     * Create a relation with declaration model to get declaration data.
     *
    */

    public function declaration()
    {
        return $this->belongsTo('App\Models\Declaration', 'declaration_id', 'id')->withTrashed();
    }

    /**
     * Create a relation with  user model to get user data.
     *
    */

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function userdata()
    {
        return $this->hasMany('App\Models\User', 'id', 'user_id');
    }

    /**
     * Scope this function get data from declaration for generate CSV.
     *
    */

    public function getForCSV($id, $startDate, $endDate)
    {
        if($startDate !== null && $endDate !== null) {
            return \DB::table($this->table . ' as dec')
                ->select(
                    [
                        'dec.created_at',
                        'dec.updated_at',
                        'dec.declaration_id',
                        'users.employee_code',
                        'users.first_name',
                        'users.last_name',
                        'users.phone',
                        'dec.answers',
                        'dec.complete',
                        'dec.void',
                        'users.groups',
                        'users.location',
                        'dec.passed',
                    ]
                )
                ->join('users', 'users.id', '=', 'dec.user_id')
                ->where('dec.created_at', '>=', date('Y-m-d H:i:s', strtotime($startDate . ' 00:00:00')))
                ->where('dec.created_at', '<=', date('Y-m-d H:i:s', strtotime($endDate . ' 23:59:59')))
                ->where('dec.declaration_id', $id)
                ->get();
        } else {
            return \DB::table($this->table . ' as dec')
                ->select(
                    [
                        'dec.created_at',
                        'dec.updated_at',
                        'dec.declaration_id',
                        'users.employee_code',
                        'users.first_name',
                        'users.last_name',
                        'users.phone',
                        'dec.answers',
                        'dec.complete',
                        'dec.void',
                        'users.groups',
                        'users.location',
                        'dec.passed'
                    ]
                )
                ->join('users', 'users.id', '=', 'dec.user_id')
                ->where('dec.declaration_id', $id)
                ->get();
        }
    }

    /**
     * Scope this function get data according to australia timezone.
     *
    */
    public function template()
    {
        return $this->belongsTo('App\Models\Declaration', 'Declaration_id', 'id');
    }
    
    /**
     * Scope this function to update table data with used id.
     *
    */

    public function voidOldTests($userIds)
    {
        /*$update = \DB::table($this->table)
            ->whereIn('user_id', $userIds)
            ->where('complete', '=', 0)
            ->update(['void' => 1]);*/

         $update = DeclarationSent::where('complete', '=', 0)->whereIn('user_id', $userIds)->update(['void' => 1]);

        return $update;
    }

	public function getSingleCompletionStats($token)
    {

        return DeclarationSent::where('token', '=', $token)->get();
    }

    public function getNightlyCompletionStats()
    {
        $today = Carbon::tomorrow(config('constant.timeZone'));
        $yesterday = Carbon::yesterday(config('constant.timeZone'));
        return \DB::table($this->table . ' as sent')
            ->select('users.employee_code', 'sent.answers', 'sent.complete')
            ->where('sent.created_at', '>=', $yesterday)
            ->where('sent.created_at', '<=', $today)
            ->where('sent.void', '=', 0)
            ->where('sent.passed', '=', 0)
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

    public function createBlankBatch(array $userIds, $declaration_id)
    {
        $now = \Carbon\Carbon::now()->toDateTimeString();
        $data = null;
        $ids = [];
        foreach($userIds as $user)
        {
            $token = Tokens::verification();
            $ids[] = $user->id;

            $data[] = array(
                'user_id' => $user->id,
                'declaration_id' => $declaration_id,
                'token' => $token,
                'short_valid' => 0,
                'passed' => 0,
                'complete' => 0,
                'void' => 0,
                'sent' => 0,
                'created_at'=> $now,
                'updated_at'=> $now,
            );
        }

        try{
            $this->voidOldTests($ids);

            \DB::table($this->table)->insert($data);

            return true;
        }
        catch(\Exception $e)
        {
            return $e->getTrace();
        }
    }
}
