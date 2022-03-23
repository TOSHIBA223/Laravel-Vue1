<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

class QuestionnaireCompleteSendQueue extends Model
{
    use SoftDeletes, Notifiable;

    protected $primaryKey = 'id';

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'sent_id', 'sent'
    ];

    protected $table = 'questionnaire_complete_send_queue';

     /**
     * Scope this function get unsent data with user.
     *
    */

    public function getUnsent()
    {
        $questionnaire = \DB::table($this->table . ' as queue')
            ->select('queue.id', 'sent.answers', 'users.employee_code')
            ->join('questionnaire_sent as sent', 'sent.id', '=', 'queue.sent_id')
            ->join('users', 'users.id', '=', 'sent.user_id')
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
