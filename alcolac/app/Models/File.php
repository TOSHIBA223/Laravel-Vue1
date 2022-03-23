<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $table = 'files';

    protected $fillable = [
        'token', 'name', 'path', 'archived', 'deleted_path'
    ];

    protected $attributes = [
        'views' => 0,
        'deleted_path' => ''
    ];
}
