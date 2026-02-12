<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\User;
use App\Repositories\Contracts\AchievementRepositoryInterface;
use Illuminate\Support\Collection;

class AchievementService
{
    public function __construct(
        protected AchievementRepositoryInterface $achievementRepository
    ) {}

    public function checkAndUnlock(User $user, Purchase $purchase): Collection
    {
        $unlocked = collect();
        $achievements = $this->achievementRepository->all();

        // Eager load existing to reduce queries & avoid duplicates
        $existingAchievementIds = $user->achievements()->pluck('achievements.id')->toArray();
        
        // Count total purchases including the new one
        $purchaseCount = $user->purchases()->count();

        foreach ($achievements as $achievement) {
            if (in_array($achievement->id, $existingAchievementIds)) {
                continue;
            }

            // Logic updated to use 'required_purchases'
            if ($purchaseCount >= $achievement->required_purchases) {
                // Attach with timestamp
                $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);
                $unlocked->push($achievement);
            }
        }

        return $unlocked;
    }
}
