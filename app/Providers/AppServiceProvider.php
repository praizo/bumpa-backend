<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\PurchaseMade;
use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Listeners\CheckAchievements;
use App\Listeners\CheckBadges;
use App\Listeners\AwardCashback;
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
        Event::listen(
            PurchaseMade::class,
            CheckAchievements::class,
        );

        Event::listen(
            AchievementUnlocked::class,
            CheckBadges::class,
        );

        Event::listen(
            BadgeUnlocked::class,
            AwardCashback::class,
        );
    }
}
