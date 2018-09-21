<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Warnuser extends Model
{
    use Notifiable;
    
    protected $table = 't_warnuser';
    
    protected $primaryKey = 'wu_index';
    
    protected $guarded = ['isAdd'];
    
    public $timestamps = false;
    
    public function routeNotificationForMail() {
        return $this->Wu_Emailaddr;
    }
    
    public function routeNotificationForVoice() {
        return $this->Wu_Telenumber;
    }
    
    public function routeNotificationForSms() {
        return $this->Wu_SmsNumber;
    }
}
