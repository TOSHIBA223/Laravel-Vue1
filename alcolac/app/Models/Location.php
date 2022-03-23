<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

     /**
     * Create a relation with  user model to get user data bind with location table.
     *
    */

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'location', 'id');
    }
}
