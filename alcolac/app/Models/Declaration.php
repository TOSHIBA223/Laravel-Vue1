<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Declaration extends Model
{

    use SoftDeletes;
    protected $fillable = [
        'name', 'fields', 'dob_validation', 'address_validation','csv_upload',
        'valid_until', 'short_valid_until','sms_template_id','location_id', 'success', 'failure', 'warn',
        'sms_enable','pre_sms_template','expire_hour','expire_day','never_expire','sms_dob_req','sms_address_req','success_color','success_font','failure_color','failure_font','warn_color','warn_font','fail_sms_template'
    ];

    protected $attributes = [
        'dob_validation' => 0,
        'address_validation' => 0,
        'valid_until' => '1',
        'short_valid_until' => '15',
        'sms_template_id' => 3,
        'fields' => null
    ];
    /**
     * Get the pollsent that owns the poll.
     */
    public function smsTemplate()
    {
        return $this->belongsTo('App\Models\SMSTemplate', 'sms_template_id', 'id');
    }
    public function cronTimes()
    {
        return $this->hasMany('App\Models\SetCrons');
    }
    public function sent()
    {
        return $this->hasMany('App\Models\DeclarationSent');
    }
}
