<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model
{
    protected $table = 'user_roles';

    protected $fillable = [
        'name', 'menu_permission_level', 'location_permissions'
    ];

    /**
     * Scope this function get user data.
     *
    */

    public function user()
    {
        return $this->hasMany('App\Models\User');
    }
}
