<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DecLog extends Model
{
    protected $table = 'dec_log';

    protected $fillable = [
        'dec_id','token','ip_address', 'country_name', 'employee_id','city_name','dec_name', 'browser'
    ];

    protected $attributes = [

    ];
}
