<?php
 
namespace App\Providers;
 
use App\View\Composers\ProfileComposer;
use Carcosa\Core\ViewComposers\UserDataViewComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
 
class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ...
    }
 
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        
        // Assign logged-in user information to all views.
        View::composer('*', UserDataViewComposer::class);
 
    }
}