<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FileLog extends Model
{
    protected $table = 'file_log';

    protected $fillable = [
        'file_id','file_name','ip_address', 'country_name', 'country_code', 'rigin_code', 'rigin_name','city_name','zip_code'
    ];

    protected $attributes = [

    ];
}
