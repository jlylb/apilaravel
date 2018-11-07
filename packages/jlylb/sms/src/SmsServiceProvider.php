<?php
namespace Jlylb\Sms;

use Illuminate\Support\ServiceProvider;
use Jlylb\Sms\Exceptions\InvalidConfiguration;
/**
 * 短信通道
 *
 * @author jlylb
 */
class SmsServiceProvider extends ServiceProvider{
    
    public function boot()
    {

    }

    public function register()
    {
        $this->app->singleton(SmsClient::class, function ($app) {
            $config = config('services.sms');
            if(is_null($config)){
                throw InvalidConfiguration::configurationNotSet();
            }
            return new SmsClient($config);
        });
    }
}
