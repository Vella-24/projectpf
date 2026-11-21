<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Notifikasi;

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
    public function boot(): void
    {
        View::composer(['layouts.data-entry', 'layouts.validator', 'layouts.superadmin', 'pertanyaan.*', 'validator.*', 'superadmin.*'], function ($view) {
            if (session()->has('user.id')) {
                $userId = session('user.id');
                $userRole = session('user.role');

                $notifikasis = Notifikasi::where(function ($q) use ($userId, $userRole) {
                    $q->where('user_id', $userId)
                      ->orWhere('role', $userRole);
                })
                ->latest()
                ->take(10)
                ->get();

                $unreadCount = Notifikasi::where(function ($q) use ($userId, $userRole) {
                    $q->where('user_id', $userId)
                      ->orWhere('role', $userRole);
                })
                ->where('dibaca', false)
                ->count();

                $view->with('globalNotifikasis', $notifikasis)
                     ->with('globalUnreadNotifikasiCount', $unreadCount);
            }
        });
    }
}
