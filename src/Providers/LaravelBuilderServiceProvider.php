<?php

namespace SongBai\LaravelBuilder\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelBuilderServiceProvider extends ServiceProvider
{

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \SongBai\LaravelBuilder\Commands\RequestCommand::class,
            ]);
        }
    }

    public function register()
    {

    }
}
