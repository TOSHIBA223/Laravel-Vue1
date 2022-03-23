<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class SystemCrons extends Model
{

    use SoftDeletes;


    protected $table = 'system_crons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'name',
        'description',
        'email_to',



    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [

    ];

    /**
     * Scope this function get user role.
     *
    */

    public function role()
    {
        return $this->belongsTo('App\Models\UserRole', 'user_role_id', 'id');
    }

    public function cronTimes()
    {
        return $this->hasMany('App\Models\SetSystemCrons');
    }

    /**
     * Scope this function search data from table with like qquery.
     *
    */
    public function searchParams($searchString)
    {
        return \DB::table($this->table)
            ->where('first_name', 'LIKE', "%$searchString%")
            ->orWhere('last_name', 'LIKE', "%$searchString%")
            ->orWhere('email', 'LIKE', "%$searchString%")
            ->paginate(15);
    }
}
