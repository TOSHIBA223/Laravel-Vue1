<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class SmbSentFiles extends Model
{
    use SoftDeletes, Notifiable;

    protected $primaryKey = 'id';

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'file_location', 'sent'
    ];

    protected $table = 'smb_sent_files';

      /**
     * Scope this function get unsent files.
     *
    */

    public function getUnsent()
    {
        $questionnaire = \DB::table($this->table)
            ->where('sent', 0)
            ->get();

        return $questionnaire;
    }

      /**
     * Scope this function update sent status.
     *
    */

    public function updateSentStatus($idArray)
    {
        \DB::table($this->table)
            ->whereIn('id', $idArray)
            ->update(['sent' => 1]);
    }

}
