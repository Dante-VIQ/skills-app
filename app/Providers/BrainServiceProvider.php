<?php

namespace App\Providers;

use App\Services\Brain\PersonalizationBrain;
use Illuminate\Support\ServiceProvider;

class BrainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/brain.php',
            'brain'
        );

        $this->app->singleton(PersonalizationBrain::class, function ($app) {
            return new PersonalizationBrain(
                $app->make(\App\Services\Discovery\AutomatedContentDiscovery::class),
                $app->make(\App\Services\TutorMatchingService::class),
            );
        });
    }

    public function boot(): void
    {
        //
    }
}
