<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class DefaultDeclaration extends Model
{
    protected $table = 'default_declarations';

    protected $fillable = [
        'declaration_id', 'location'
    ];

    protected $attributes = [

    ];

    /**
     * Create a relation with declaration model to get declaration data.
     *
    */

    public function declaration()
    {
        return $this->belongsTo('App\Models\Declaration', 'declaration_id', 'id');
    }

    /**
     * Create a relation with  user model to get user data.
     *
    */

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    /**
     * Scope this function get data from declaration for generate CSV.
     *
    */



}
