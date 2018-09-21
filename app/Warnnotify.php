<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warnnotify extends Model
{
    protected $table = 't_warnnotify';
    
    protected $primaryKey = 'wn_index';
    
    protected $guarded = ['isAdd', 'user','wu_name'];
    
    public $timestamps = false;
    
    protected $casts = [
       // 'Wn_notifytype' => 'string',
    ];
    
    public function setWnNotifytypeAttribute($value) {
        $this->attributes['Wn_notifytype'] = array_sum($value);
    }
    
    public function getWnNotifytypeAttribute($value) {
        return $this->getTypeValue($value);
    }
    
    public function getTypeValue($value) {
        $arr = [
            'sms'=>1,
            'email'=>2,
            'audio'=>4,
        ];

        return array_reduce($arr, function($carry, $v)use($value){
            $cur=$value & $v ;
            if($cur){
                $carry[]=$cur + 0;
            }
            return $carry;
        },[]);  
    }
    
    public function user() {
        return $this->hasOne('\App\Warnuser','wu_index','wu_index');
    }
}
