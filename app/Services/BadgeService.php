<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\User;
use App\Repositories\Contracts\BadgeRepositoryInterface;
use Illuminate\Support\Collection;

class BadgeService
{
  public function __construct(
    protected BadgeRepositoryInterface $badgeRepository
  ) {}

  public function checkAndUnlock(User $user): Collection
  {
    $unlocked = collect();

    $achievementCount = $user->achievements()->count();
    $badges = $this->badgeRepository->all();
    $existingBadgeIds = $user->badges()->pluck('badges.id')->toArray();

    foreach ($badges as $badge) {
      if (in_array($badge->id, $existingBadgeIds)) {
        continue;
      }

      // Updated column name: required_achievements
      if ($achievementCount >= $badge->required_achievements) {
        $user->badges()->attach($badge->id, ['unlocked_at' => now()]);
        $unlocked->push($badge);
      }
    }

    return $unlocked;
  }

  public function getNextBadgeProgress(User $user): array
  {
    $currentCount = $user->achievements()->count();
    $nextBadge = $this->badgeRepository->getNextBadge($currentCount);

    $currentBadge = $user->badges()
      ->orderBy('badges.required_achievements', 'desc')
      ->get()
      ->sortByDesc('required_achievements')
      ->first();

    // Calculate progress towards next badge
    $progress = [
      'next_badge' => null,
      'remaining_achievements' => 0,
      'current_badge' => $currentBadge ? $currentBadge->name : 'None',
      'next_achievement' => null, // New field
    ];

    if ($nextBadge) {
      $progress['next_badge'] = $nextBadge->name;
      $progress['remaining_achievements'] = $nextBadge->required_achievements - $currentCount;

      // Find the NEXT achievement based on spend, excluding unlocked ones
      $totalSpent = $user->total_spent ?? 0;
      $unlockedIds = $user->achievements()->pluck('achievements.id')->toArray();

      $nextAchievement = Achievement::where('required_spend', '>', $totalSpent)
        ->whereNotIn('id', $unlockedIds)
        ->orderBy('required_spend', 'asc')
        ->first();

      if ($nextAchievement) {
        $progress['next_achievement'] = [
          'name' => $nextAchievement->name,
          'required_spend' => (float) $nextAchievement->required_spend,
          'remaining_spend' => max(0, $nextAchievement->required_spend - $totalSpent),
        ];
      }
    }

    return $progress;
  }
}
