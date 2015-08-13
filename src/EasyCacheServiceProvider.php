<?php

namespace Websanova\EasyCache;

use Illuminate\Support\ServiceProvider;

class EasyCacheServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/config/easycache.php', 'easycache'
        );
    }

    public function boot()
    {
        require __DIR__ . '/Http/routes.php';
    }
}
