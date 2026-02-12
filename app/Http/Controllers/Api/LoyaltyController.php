<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        return response()->json([
            'unlocked_achievements' => $user->achievements->pluck('name'),
            'current_badge' => $progress['current_badge'],
            'next_badge' => $progress['next_badge'],
            'remaining_to_unlock_next_badge' => $progress['remaining_achievements'],
        ]);
    }
}
