<?php

namespace SongBai\LaravelBuilder\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelBuilderServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->loadCommands();
        }
    }

    public function register()
    {
        $this->registerConfig();
    }

    protected function registerConfig()
    {
        $path = __DIR__ . '/../../config/';
        $files = scandir($path);
        $paths = [];
        foreach ($files as $key => $name) {
            if ($name == '.' || $name == '..') {
                continue;
            }
            $paths[$path . $name] = config_path($name);
        }
        $this->publishes($paths);
    }

    protected function loadCommands()
    {
        $this->commands([
            \SongBai\LaravelBuilder\Commands\LbCommand::class,
            \SongBai\LaravelBuilder\Commands\RequestCommand::class,
            \SongBai\LaravelBuilder\Commands\TableToModelCommand::class,
        ]);
    }
}
