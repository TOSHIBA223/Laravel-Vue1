<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SetCrons extends Model
{
    protected $table = 'set_crons';

    protected $fillable = [
        'declaration_id', 'locations', 'settime', 'status','cronenbale','cronday'
    ];

    protected $attributes = [

    ];

    /**
     * Create a relation with declaration model to get declaration data.
     *
    */

    public function declaration()
    {
        return $this->belongsTo('App\Models\Declaration', 'declaration_id', 'id');
    }









}
