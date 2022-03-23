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

class AdminUsers extends Authenticatable implements CanResetPassword
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use AuthReset;

    protected $table = 'admin_users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'user_role_id',
        'status',

        'deleted_at'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
        'role'
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
