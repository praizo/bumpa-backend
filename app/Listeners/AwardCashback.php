<?php

namespace App\Listeners;

use App\Events\BadgeUnlocked;
use App\Notifications\CashbackEarned;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class AwardCashback implements ShouldQueue
{
    public function handle(BadgeUnlocked $event): void
    {
        $amount = $event->badge->cashback_amount ?? 0;

        if ($amount > 0) {
            Log::info("CASHBACK AWARDED: User {$event->user->id} earned {$amount} Naira for badge '{$event->badge->name}'.");

            // Send Notification
            $event->user->notify(new CashbackEarned($amount, $event->badge->name));
        }
    }
}
