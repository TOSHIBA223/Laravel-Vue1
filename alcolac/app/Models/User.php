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

class User extends Authenticatable implements CanResetPassword
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use AuthReset;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'dob',
        'phone',
        'groups',
        'location',
        'user_role_id',
        'simple_form',
        'employee_code',
        'is_qr_employee'
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
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
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
     * Scope this function get user sent declaration.
     *
    */

    public function sentDeclarations()
    {
        return $this->hasMany('App\Models\DeclarationSent', 'user_id', 'id');
    }

    /**
     * Scope this function get user poll sent.
     *
    */
    public function sentPolls()
    {
        return $this->hasMany('App\Models\PollSent', 'user_id', 'id');
    }

    /**
     * Scope this function get user address.
     *
    */
    public function address()
    {
        return $this->hasMany('App\Models\UserAddress', 'user_id', 'id');
    }
    /**
     * Scope this function get user location.
     *
    */

    public function location()
    {
        return $this->hasOne('App\Models\Location', 'id', 'location');
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
            ->orWhere('name', 'LIKE', "%$searchString%")
            ->orWhere('employee_code', 'LIKE', "%$searchString%")
            ->orWhere('email', 'LIKE', "%$searchString%")
            ->paginate(15);
    }

    public function search($field, $string)
    {
        return \DB::table($this->table)
            ->where("$field", 'LIKE', "%$string%")
            ->paginate(15);
    }

    public function searchExact($field, $string)
    {
        return \DB::table($this->table)
            ->where("$field", "$string")
            ->paginate(15);
    }
}
