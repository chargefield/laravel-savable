<?php

namespace Chargefield\Savable;

use Illuminate\Support\ServiceProvider;

class SavableServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerPublishables();
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/savable.php',
            'savable'
        );

        $this->registerCommands();
    }

    protected function registerCommands(): void
    {
        $this->commands([
            Commands\FieldMakeCommand::class,
        ]);
    }

    protected function registerPublishables(): void
    {
        $this->publishes([
            __DIR__ . '/../config/savable.php' => config_path('savable.php'),
        ], 'config');
    }
}
