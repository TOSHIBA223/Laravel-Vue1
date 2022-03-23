<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $table = 'user_address';

    protected $fillable = [
        'user_id', 'address', 'suburb', 'state', 'post_code',
        'current','country'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id',
        'current',
        'id'
    ];

     /**
     * Scope this function get user data.
     *
    */
    public function user()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
