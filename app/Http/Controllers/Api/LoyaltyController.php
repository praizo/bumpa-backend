<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Services\BadgeService;
use Illuminate\Http\Request;

class LoyaltyController extends Controller
{
    public function __construct(
        protected BadgeService $badgeService
    ) {}

    public function show(Request $request, $userId)
    {
        if ($request->user()->id != $userId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user = $request->user();

        // Eager load relations
        $user->load(['achievements', 'badges']);

        $progress = $this->badgeService->getNextBadgeProgress($user);

        // Get all achievements to determine which are locked
        $allAchievements = Achievement::all();
        $unlockedIds = $user->achievements->pluck('id')->toArray();
        $nextAvailable = $allAchievements->whereNotIn('id', $unlockedIds)
            ->values()
            ->map(function ($achievement) use ($user) {
                return [
                    'name' => $achievement->name,
                    'required_spend' => (float) $achievement->required_spend,
                    'remaining_spend' => max(0, (float) $achievement->required_spend - (float) $user->total_spent),
                ];
            });

        return response()->json([
            'unlocked_achievements' => $user->achievements->pluck('name'),
            'next_available_achievements' => $nextAvailable,
            'current_badge' => $progress['current_badge'],
            'next_badge' => $progress['next_badge'],
            'remaining_to_unlock_next_badge' => $progress['remaining_achievements'],
            'next_achievement_progress' => $progress['next_achievement'], // New field for spend info
        ]);
    }
    public function notifications(Request $request)
    {
        return response()->json([
            'notifications' => $request->user()->notifications,
        ]);
    }
}
