<?php

namespace CalculatieTool\IntMaint;

use Illuminate\Support\ServiceProvider;

class IntMaintServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('intmaint', function () {
            return new MaintenanceManager();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['intmaint'];
    }
}
