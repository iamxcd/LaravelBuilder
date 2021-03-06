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
        $this->publishStubs();
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

    protected function publishStubs()
    {
        $path = __DIR__ . '/../../stubs/';
        $files = scandir($path);
        $paths = [];
        foreach ($files as $key => $name) {
            if ($name == '.' || $name == '..') {
                continue;
            }
            $paths[$path . $name] = resource_path('stubs/' . $name);
        }
        $this->publishes($paths);
    }

    protected function loadCommands()
    {
        $this->commands([
            \SongBai\LaravelBuilder\Commands\LbCommand::class,
            \SongBai\LaravelBuilder\Commands\RequestCommand::class,
            \SongBai\LaravelBuilder\Commands\TableToModelCommand::class,
            \SongBai\LaravelBuilder\Commands\ControllerCommand::class,
            \SongBai\LaravelBuilder\Commands\getColumnInfoCommand::class,
            \SongBai\LaravelBuilder\Commands\CreateDicFilCommand::class
        ]);
    }
}
