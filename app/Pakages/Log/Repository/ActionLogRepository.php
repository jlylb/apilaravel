<?php

namespace App\Pakages\Log\Repository;


use luoyangpeng\ActionLog\Services\clientService;
/**
 * 操作日志
 *
 * @author litc
 */
class ActionLogRepository {
       /**
     * 记录用户操作日志
     * @param $type
     * @param $content
     * @param ActionLog $actionLog
     * @return bool
     */
    public function createActionLog($type,$content)
    {

    	$actionLog = new \App\Pakages\Log\Models\UserLog();
        
        $defaultGuard = app('auth')->getDefaultDriver();
 
    	if(auth($defaultGuard)->check()){
    		$actionLog->userid = auth($defaultGuard)->user()->userid;
    		$actionLog->username = auth($defaultGuard)->user()->username;
    	}else{
    		$actionLog->uid = 0;
    		$actionLog->username ="访客";
    	}
        
        $request = app('request');
        
       	$actionLog->browser = clientService::getBrowser($request->server('HTTP_USER_AGENT'), true);
       	$actionLog->system = clientService::getPlatForm($request->server('HTTP_USER_AGENT'), true);
       	$actionLog->url = request()->getRequestUri();
        $actionLog->ip = request()->getClientIp();
        $actionLog->type = $type;
        $actionLog->content = $content;
        $actionLog->guard = $defaultGuard;
        $actionLog->updatetime = date('Y-m-d H:i:s');
        $res = $actionLog->save();

        return $res;
    }
}
