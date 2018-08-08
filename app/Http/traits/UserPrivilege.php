<?php

namespace App\Http\traits;
use Bouncer;

/**
 * Description of UserPrivilege
 *
 * @author Administrator
 */
Trait UserPrivilege {
    
    protected function getGuard() {
        return 'api';
    }
    
    protected function user() {
        return auth()->guard($this->getGuard())->user();
    }
    
    protected function isSuper($user) {
        return $user->isA('superadmin');
    }
    
    protected function auth() {
        return auth()->guard($this->getGuard());
    }
    
    protected function getCompanyLeader($user) {
        return Bouncer::Role()->a('superadmin');
    }
}
