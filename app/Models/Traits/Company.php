<?php
namespace App\Models\Traits;

use App\Models\Scope\CompanyScope;

/**
 * 公司限制
 * @author litc
 */
trait Company {
    
    
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CompanyScope);
    }
}
