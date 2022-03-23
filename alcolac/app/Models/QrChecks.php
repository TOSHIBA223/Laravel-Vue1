<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Qrchecks extends Model
{
    protected $table = 'qr_checks';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'employee_code',
        'first_name',
        'last_name',
        'location',
        'ip_address',
        'phone',
        'result'
 ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [

    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [

    ];

    /**
     * Scope this function get user role.
     *
    */



    /**
     * Scope this function search data from table with like qquery.
     *
    */
    public function searchParams($searchString)
    {
       return \DB::table($this->table)
            ->where('first_name', 'LIKE', "%$searchString%")
            ->orWhere('last_name', 'LIKE', "%$searchString%")
            ->orWhere('employee_code', 'LIKE', "%$searchString%")
            ->orWhere('phone', 'LIKE', "%$searchString%")
            ->paginate(15);
    }
}
