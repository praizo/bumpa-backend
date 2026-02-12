<?php

namespace Tests\Feature;

use App\Events\AchievementUnlocked;
use App\Events\BadgeUnlocked;
use App\Events\PurchaseMade;
use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class LoyaltySystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_purchase_unlocks_achievement_and_badge(): void
    {
        Event::fake([
            // We want to fake specific events to assert they were dispatched,
            // BUT we want the listeners to actually run to test the full flow?
            // Usually, "Event::fake()" stops listeners. 
            // Better approach for Integration Test: Don't fake, just assert database state.
            // OR explicitely fire events.
        ]);

        // 1. Setup Data
        $user = User::factory()->create();
        
        $achievement = Achievement::factory()->create([
            'name' => 'First Buy',
            'required_purchases' => 1,
        ]);

        $badge = Badge::factory()->create([
            'name' => 'Novice Badge',
            'required_achievements' => 1,
            'cashback_amount' => 50,
        ]);

        // 2. Perform Action (Simulate Purchase)
        // We can manually dispatch the event or call a service, 
        // assuming your Purchase creation logic fires the event.
        
        // Let's assume you have a way to create a purchase that triggers the event.
        // For now, we simulate the code that would be in your PurchaseController:
        
        $purchase = Purchase::create([
            'user_id' => $user->id,
            'amount' => 1000,
            'reference' => 'REF-' . uniqid(),
        ]);

        // Manually dispatch because we don't have the observer/controller wire-up shown yet.
        // If you added `PurchaseMade::dispatch($purchase, $user)` in your Controller, this mimics it.
        PurchaseMade::dispatch($purchase, $user);

        // 3. Assertions
        
        // Assert Achievement Unlocked
        $this->assertDatabaseHas('user_achievements', [
            'user_id' => $user->id,
            'achievement_id' => $achievement->id,
        ]);

        // Using events, the checks might be queued. 
        // If your listeners are "ShouldQueue", use "stopFakingQueue" or run synchronously for tests.
        // If strictly synchronous (or sync driver in phpunit.xml):
        
        // Assert Badge Unlocked (Triggered by AchievementUnlocked)
        // We need to ensure the recursive events fired. 
        // Since we fired PurchaseMade, CheckAchievements ran -> unlocked achievement -> fired AchievementUnlocked -> CheckBadges ran -> unlocked badge.
        
        $this->assertDatabaseHas('user_badges', [
            'user_id' => $user->id,
            'badge_id' => $badge->id,
        ]);
    }
    
    public function test_api_returns_correct_progress(): void
    {
        $user = User::factory()->create();
        $achievement = Achievement::factory()->create(['name' => 'Test Ach']);
        $user->achievements()->attach($achievement);
        
        $response = $this->actingAs($user)->getJson("/api/users/{$user->id}/achievements");
        
        $response->assertStatus(200)
            ->assertJsonFragment(['unlocked_achievements' => ['Test Ach']]);
    }
}
