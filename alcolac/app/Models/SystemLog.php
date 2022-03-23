<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    protected $table = 'system_log';

    protected $fillable = [
        'user_id', 'message', 'type'
    ];

    protected $attributes = [

    ];

    public function admin()
    {
        return $this->belongsTo('App\Models\AdminUsers', 'user_id', 'id');
    }
}
