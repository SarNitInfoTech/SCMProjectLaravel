<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Blade;
use Carbon\Carbon;
use App\Models\Notification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }


    /**
     * Bootstrap any application services.
     */
    public function boot()
{
    View::composer('common.notification.notification', function ($view) {
        $view->with('notifications', Notification::where('is_read', false)->latest()->take(3)->get());
        $view->with('unreadCount', Notification::where('is_read', false)->count());
    });
    Blade::directive('dateformat', function ($expression) {
        return "<?php echo ($expression) ? Carbon::parse($expression)->format('d-m-Y') : '-'; ?>";
    });
}
}
