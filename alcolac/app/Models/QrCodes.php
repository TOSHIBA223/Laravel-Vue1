<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Qrcodes extends Model
{

    use SoftDeletes;


    protected $table = 'qr_codes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [

        'area_name',
        'qr_image',
        'qr_url',
        'status'



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
        return \DB::table('qr_checks')
            ->where('location',$searchString->token)
            ->where('first_name', 'LIKE', "%$searchString->search%")
            ->orWhere('last_name', 'LIKE', "%$$searchString->search%")
            ->orWhere('email', 'LIKE', "%$$searchString->search%")
            ->orWhere('location', 'LIKE', "%$$searchString->search%")
            ->paginate(15);
    }
}
