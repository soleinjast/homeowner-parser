<?php

namespace Modules\HomeownerProcessor\Providers;
use Illuminate\Support\ServiceProvider;
use Modules\HomeownerProcessor\Commands\ProcessHomeownersCommand;

class HomeownerProcessorServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        // Register ProcessHomeownersCommand
        $this->commands([
            ProcessHomeownersCommand::class
        ]);
    }
}
