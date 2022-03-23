<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Auth\Passwords\CanResetPassword as AuthReset;
use Illuminate\Database\Eloquent\Model;

class Departments extends Model
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use AuthReset;

    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'employee_code',
        'department',
        'manager',
        'location',
        'created_at',
        'updated_at',
        'deleted_at'
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



    /**
     * Scope this function search data from table with like qquery.
     *
    */
    // public function searchParams($searchString)
    // {
    //     return \DB::table($this->table)
    //         ->where('first_name', 'LIKE', "%$searchString%")
    //         ->orWhere('last_name', 'LIKE', "%$searchString%")
    //         ->orWhere('employee_code', 'LIKE', "%$searchString%")
    //         ->paginate(15);
    // }


    public function employee()
    {
        return $this->belongsTo('App\Models\Users', 'employee_code', 'employee_code');
    }
}
