<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BadgeRepositoryInterface;
use App\Models\Badge;
use Illuminate\Database\Eloquent\Collection;

class BadgeRepository implements BadgeRepositoryInterface
{
    public function all(): Collection
    {
        return Badge::all();
    }

    public function find(int $id): ?Badge
    {
        return Badge::find($id);
    }

    public function getNextBadge(int $currentAchievementCount): ?Badge
    {
        // Updated column name: required_achievements
        return Badge::where('required_achievements', '>', $currentAchievementCount)
            ->orderBy('required_achievements', 'asc')
            ->first();
    }
}
