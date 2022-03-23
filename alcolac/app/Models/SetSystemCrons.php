<?php
namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class SetSystemCrons extends Model
{
    protected $table = 'set_system_crons';

    protected $fillable = [
        'system_crons_id', 'locations', 'settime', 'status','cronenbale','cronday'
    ];

    protected $attributes = [

    ];

    /**
     * Create a relation with declaration model to get declaration data.
     *
    */

    public function cronTimes()
    {
        return $this->belongsTo('App\Models\SystemCrons', 'system_cron_id', 'id');
    }









}
