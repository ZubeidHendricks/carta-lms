<?php

namespace Modules\Tavus\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Tavus\Services\TavusApiService;
use Modules\Tavus\Services\TavusService;

class TavusServiceProvider extends ServiceProvider
{
    protected string $moduleName = 'Tavus';
    protected string $moduleNameLower = 'tavus';

    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerConfig();
        $this->registerViews();
        $this->loadMigrationsFrom(module_path($this->moduleName, 'database/migrations'));
    }

    /**
     * Register the service provider.
     */
    public function register(): void
    {
        $this->app->register(RouteServiceProvider::class);
        
        // Register services
        $this->app->singleton(TavusApiService::class, function ($app) {
            return new TavusApiService();
        });

        $this->app->singleton(TavusService::class, function ($app) {
            return new TavusService($app->make(TavusApiService::class));
        });
    }

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $this->publishes([
            module_path($this->moduleName, 'config/tavus.php') => config_path('tavus.php'),
        ], 'config');

        $this->mergeConfigFrom(
            module_path($this->moduleName, 'config/tavus.php'),
            'tavus'
        );
    }

    /**
     * Register views.
     */
    public function registerViews(): void
    {
        $viewPath = resource_path('views/modules/' . $this->moduleNameLower);
        $sourcePath = module_path($this->moduleName, 'resources/views');

        $this->publishes([
            $sourcePath => $viewPath
        ], ['views', $this->moduleNameLower . '-module-views']);

        $this->loadViewsFrom(array_merge($this->getPublishableViewPaths(), [$sourcePath]), $this->moduleNameLower);
    }

    /**
     * Get the services provided by the provider.
     */
    public function provides(): array
    {
        return [
            TavusApiService::class,
            TavusService::class,
        ];
    }

    private function getPublishableViewPaths(): array
    {
        $paths = [];
        foreach ($this->app['config']->get('view.paths') as $path) {
            if (is_dir($path . '/modules/' . $this->moduleNameLower)) {
                $paths[] = $path . '/modules/' . $this->moduleNameLower;
            }
        }
        return $paths;
    }
}
