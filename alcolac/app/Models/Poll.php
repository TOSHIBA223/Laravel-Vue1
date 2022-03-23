<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{

    protected $fillable = [
        'name', 'fields', 'valid_to', 'description'
    ];

    /**
     * Get the pollsent that owns the poll.
     */

    public function sent()
    {
        return $this->hasMany('App\Models\PollSent');
    }
}
