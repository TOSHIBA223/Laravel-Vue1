<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserImportFiles extends Model
{

    protected $primaryKey = 'id';

    protected $fillable = [
        'file_name',
        'imported'
    ];

    protected $table = 'user_import_files';
}
