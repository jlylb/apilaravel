<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'sy_area';
    
    protected $primaryKey = 'AreaId';
    
    protected $fillable = ['Fid','AreaName','Co_ID','area_manager','connect_phone'];
    
    public $timestamps = false;
    
    public function parentAreaName() {
        return $this->belongsTo('\App\Area', 'Fid', 'AreaId');
    }
    
    public function company() {
        return $this->belongsTo('\App\Company', 'Co_ID', 'Co_ID');
    }
}
