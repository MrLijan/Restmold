<?php

namespace Mrlijan\Restmold;

use Illuminate\Support\ServiceProvider;

class RestmoldServiceProvider extends ServiceProvider
{
    // bootstreap web services
    // listen for events
    // publish configuration files or database migration
    public function boot()
    {
        if($this->isRunningConsole()) {
            $this->commands([
                Console\GenerateCommand::class
            ]);
        }
    }

    // Extending functionality from other classes
    // register service providers
    // create singleton classes
    public function register()
    {

    }

    /**
     * Check if application running in console
     * @return bool
     */
    private function isRunningConsole(): bool
    {
        return $this->app->runningInConsole();
    }
}
