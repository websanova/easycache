<?php

namespace Websanova\EasyCache;

use Illuminate\Support\ServiceProvider;

class EasyCacheServiceProvider extends ServiceProvider
{
    public function register()
    {
        
    }

    public function boot()
    {
        require __DIR__ . '/Http/routes.php';
    }
}
