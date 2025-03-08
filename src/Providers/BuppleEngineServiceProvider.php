<?php

namespace BuppleEngine\Providers;

use BuppleEngine\BuppleEngine;
use BuppleEngine\Core\Memory\MemoryManager;
use BuppleEngine\Core\Memory\Contracts\MemoryDriverInterface;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class BuppleEngineServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(MemoryManager::class, function ($app) {
            return new MemoryManager($app['config']->get('bupple-engine.memory', []));
        });

        $this->app->singleton(BuppleEngine::class, function ($app) {
            return new BuppleEngine(
                $app->make(MemoryManager::class),
                $app['config']->get('bupple-engine', [])
            );
        });

        $this->app->alias(BuppleEngine::class, 'bupple.engine');

        // Bind the default memory driver
        $this->app->bind(MemoryDriverInterface::class, function ($app) {
            return $app->make(MemoryManager::class)->driver();
        });

        $this->mergeConfigFrom(
            __DIR__ . '/../config/auth.php',
            'auth'
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/bupple-engine.php' => config_path('bupple-engine.php'),
        ]);

        if (!config('bupple-engine.database.use_mongodb', false)) {
            $this->publishes([
                __DIR__ . '/../database/migrations' => database_path('migrations'),
            ]);
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
