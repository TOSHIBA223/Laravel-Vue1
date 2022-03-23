<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SMSTemplate extends Model
{
    use softDeletes;

    protected $table = 'sms_templates';

    protected $fillable = [
        'slug','name', 'content', 'assignable'
    ];

    protected $attributes = [
        'assignable' => 1
    ];

    protected $casts = [
        'assignable' => 'boolean'
    ];
}
