<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // 本番のみ BROWSERSHOT_HOME=/var/www/.browsershot を実行
        if(app()->environment('production')) {
            if($home = env('BROWSERSHOT_HOME')) {
                putenv("HOME={$home}");
            }
        }
    }
}
