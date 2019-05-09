<?php


namespace App\Engineroom;


use Illuminate\Support\ServiceProvider;

class Provider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Client::class, function() {
            return new Client(
                env('ENGINEROOM_API_URI'),
                env('ENGINEROOM_API_EMAIL'),
                env('ENGINEROOM_API_PASSWORD')
            );
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
