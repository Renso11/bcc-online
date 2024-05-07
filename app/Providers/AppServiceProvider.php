<?php

namespace App\Providers;

use App\Models\CompteCommission;
use App\Services\PaiementService;
use Illuminate\Support\ServiceProvider;
use \Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {   
        $this->app->bind(PaiementService::class, function ($app) {
            return new PaiementService();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        View::composer('base', function ($view) {
            $compteCommissions = CompteCommission::where('deleted',0)->get();         
            $view->with('compteCommissions', $compteCommissions);
        });
    }
}
