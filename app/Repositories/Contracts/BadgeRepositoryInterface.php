<?php

namespace App\Repositories\Contracts;

use App\Models\Badge;
use Illuminate\Database\Eloquent\Collection;

interface BadgeRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Badge;
    public function getNextBadge(int $currentAchievementCount): ?Badge;
}
