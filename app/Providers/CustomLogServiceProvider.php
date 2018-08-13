<?php

namespace App\Providers;

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
  
		if($model){
			foreach($model as  $v) {
				
			$v::updated(function($data){
					ActionLog::createActionLog('update',"更新的id:".$data->id);
				});
			
			$v::created(function($data){
				ActionLog::createActionLog('add',"添加的id:".$data->id);
			});
			
			$v::deleted(function($data){
				ActionLog::createActionLog('delete',"删除的id:".$data->id);

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
        parent::register();
    }
}
