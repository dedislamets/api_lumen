<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */

    public function register()
    {
        $this->app->singleton(
	        'mailer',
	        function ($app) {
	            return $app->loadComponent('mail', 'Illuminate\Mail\MailServiceProvider', 'mailer');
	        }
	    );

	    // Aliases
	    $this->app->alias('mailer', \Illuminate\Contracts\Mail\Mailer::class);
	    // Make Queue
	    $this->app->make('queue');
	
    }
}
