<?php

namespace App\Pakages\Log\Providers;

use luoyangpeng\ActionLog\ActionLogServiceProvider;
use ActionLog;

class CustomLogServiceProvider extends ActionLogServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        $dir = base_path('vendor/luoyangpeng/action-log/src');

        $this->publishes([
            $dir.'/migrations' => database_path('migrations'),
        ], 'migrations');


        $this->publishes([
            $dir.'/config/actionlog.php' => config_path('actionlog.php'),
        ], 'config');

        $model = config("actionlog");
        
        $post = app('request')->all();
  
		if($model){
			foreach($model as  $v) {
				
			$v::updated(function($data)use($post){
					ActionLog::createActionLog('update',"更新的id:".$data->getKey(). json_encode($post));
				});
			
			$v::created(function($data)use($post){
				ActionLog::createActionLog('add',"添加的id:".$data->getKey().json_encode($post));
			});
			
			$v::deleted(function($data)use($post){
				ActionLog::createActionLog('delete',"删除的id:".$data->getKey().json_encode($post));
			});
			
			}
		}
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton("ActionLog",function($app){
            return new \App\Pakages\Log\Repository\ActionLogRepository();
        });
    }
}
