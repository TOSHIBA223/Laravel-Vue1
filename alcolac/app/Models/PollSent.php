<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PollSent extends Model
{
    protected $table = 'polls_sent';

    protected $fillable = [
        'poll_id', 'user_id', 'token', 'answer', 'complete', 'void', 'sent'
    ];

    protected $attributes = [
        'complete' => 0,
        'void' => 0,
        'sent' => 0
    ];

     /**
     * Get the Poll data that owns the poll sent.
     */

    public function template()
    {
        return $this->belongsTo('App\Models\Poll', 'poll_id', 'id');
    }
}
