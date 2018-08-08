<?php

namespace App\Http\Controllers;

/**
 * Description of ApiController
 *
 * @author Administrator
 */
class ApiController extends Controller{
    
    protected function user() {
        return auth()->guard('api')->user();
    }
    
    
    protected function isSuper() {
        return $this->user()->isA('superadmin');
    }
   
}
