<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Questionnaire extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'id';

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'dob',
        'email',
        'menuroles',
        'address'
    ];

    protected $table = 'questionnaire';

}
