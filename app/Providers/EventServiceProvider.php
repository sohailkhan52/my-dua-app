<?php

namespace App\Providers;

use App\Events\TaskCreated;
use App\Listeners\SendTaskCreatedEmail;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
   protected $listen=[
    TaskCreated::class=>[SendTaskCreatedEmail::class,],
   ];

     /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
