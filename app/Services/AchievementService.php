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
    $existingAchievementIds = $user->achievements()->pluck('achievements.id')->toArray();

    // Update User's Total Spend
    $newTotalSpent = $user->purchases()->sum('amount');
    $user->update(['total_spent' => $newTotalSpent]);

    foreach ($achievements as $achievement) {
      if (in_array($achievement->id, $existingAchievementIds)) {
        continue;
      }

      // Check Spend Threshold
      if ($newTotalSpent >= $achievement->required_spend) {
        $user->achievements()->attach($achievement->id, ['unlocked_at' => now()]);
        $unlocked->push($achievement);
      }
    }

    return $unlocked;
  }
}
