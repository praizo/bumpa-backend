<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\AchievementRepositoryInterface;
use App\Models\Achievement;
use Illuminate\Database\Eloquent\Collection;

class AchievementRepository implements AchievementRepositoryInterface
{
    public function all(): Collection
    {
        return Achievement::all();
    }

    public function find(int $id): ?Achievement
    {
        return Achievement::find($id);
    }
}
