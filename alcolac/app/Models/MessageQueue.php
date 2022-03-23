<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageQueue extends Model
{
    protected $table = 'message_queue';

    protected $fillable = [
        'user_id', 'message', 'type', 'sent', 'result', 'schedule', 'to'
    ];

    protected $attributes = [

    ];

    public function admin()
    {
        return $this->belongsTo('App\Models\AdminUsers', 'user_id', 'id');
    }
}
