<?php

namespace App\Providers;

use App\Models\StudentWarning;
use App\Observers\BoxComplaintSuggestionObserver;
use App\Observers\ExamObserver;
use App\Observers\ExamOrderObserver;
use App\Observers\StudentWarningObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
    }
}
