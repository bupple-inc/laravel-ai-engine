<?php

namespace Bupple\Engine\Providers;

use Bupple\Engine\BuppleEngine;
use Bupple\Engine\Core\Memory\MemoryManager;
use Bupple\Engine\Core\Memory\Contracts\MemoryDriverInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class BuppleEngineServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/bupple.php', 'bupple');

        $this->app->singleton(MemoryManager::class, function ($app) {
            return new MemoryManager($app['config']->get('bupple.memory', []));
        });

        $this->app->singleton(BuppleEngine::class, function ($app) {
            return new BuppleEngine(
                $app->make(MemoryManager::class),
                $app['config']->get('bupple', [])
            );
        });

        $this->app->alias(BuppleEngine::class, 'bupple.engine');

        // Bind the default memory driver
        $this->app->bind(MemoryDriverInterface::class, function ($app) {
            return $app->make(MemoryManager::class)->driver();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishConfig();
            $this->publishMigrations();
        }
    }

    /**
     * Publish configuration file.
     */
    protected function publishConfig(): void
    {
        $this->publishes([
            __DIR__ . '/../config/bupple.php' => config_path('bupple.php'),
        ], 'bupple-config');
    }

    /**
     * Publish migrations if not using MongoDB.
     */
    protected function publishMigrations(): void
    {
        if (!config('bupple-engine.database.use_mongodb', false)) {
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ], 'bupple-engine-migrations');

            $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
        }
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<string>
     */
    public function provides(): array
    {
        return [
            BuppleEngine::class,
            'bupple.engine',
        ];
    }
}
