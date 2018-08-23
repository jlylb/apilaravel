<?php

namespace App\Providers;

use Illuminate\Auth\EloquentUserProvider;

class Md5UserProvider extends EloquentUserProvider
{
    public function __construct($hasher, $model) 
    { 
        parent::__construct($hasher, $model); 
    }
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
