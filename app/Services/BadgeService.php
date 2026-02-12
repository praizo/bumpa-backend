<?php

namespace App\Services;

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
        
        // Updated column name: required_achievements
        $currentBadge = $user->badges()
            ->orderBy('badges.required_achievements', 'desc') 
            // Note: Join/ordering might be needed depending on DB driver, 
            // but standard relations usually handle this if setup correct. 
            // Simpler to getting the one with max required_achievements from collection:
            ->get()
            ->sortByDesc('required_achievements')
            ->first();

        if (!$nextBadge) {
            return [
                'next_badge' => null,
                'remaining_achievements' => 0,
                'current_badge' => $currentBadge ? $currentBadge->name : 'None',
            ];
        }

        return [
            'next_badge' => $nextBadge->name,
            'remaining_achievements' => $nextBadge->required_achievements - $currentCount,
            'current_badge' => $currentBadge ? $currentBadge->name : 'None',
        ];
    }
}
