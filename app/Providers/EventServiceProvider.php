<?php

namespace App\Providers;

use App\Events\GameOver;
use App\Events\NewGame;
use App\Events\Play;
use App\Listeners\GameOverListener;
use App\Listeners\NewGameListener;
use App\Listeners\PlayListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        NewGame::class => [
            NewGameListener::class,
        ],
        Play::class => [
            PlayListener::class,
        ],
        GameOver::class => [
            GameOverListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
