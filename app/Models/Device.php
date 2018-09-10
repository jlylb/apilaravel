<?php

namespace App\Models;


/**
 * 模型关联关系
 * @author litc
 */
trait Device {
   
    /**
     * 设备状态
     * 
     */
    public function deviceStatus() {
        return $this->hasOne('\App\Realstatus', 'pdi_index', 'pdi_index');
    }
    
    /**
     * 设备表
     */
    public function device() {
        return $this->hasOne('\App\PriDeviceInfo', 'pdi_index', 'pdi_index');
    }
}
