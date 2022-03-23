<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginToken extends Model
{

    protected $fillable = [
        'token',
        'created_at'
    ];

    /**
     * Get the users login otp.
     */

    protected $table= 'login_token';

    public $timestamps = false;

}
