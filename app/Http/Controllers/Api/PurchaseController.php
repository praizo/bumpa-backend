<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Purchase;
use App\Models\User;
use App\Events\PurchaseMade;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Faker\Factory;
class PurchaseController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'items' => 'sometimes|array'
        ]);

        $reference = 'PUR_' . Str::uuid()->toString();
        $user = $request->user();

        if ($request->has('items')) {
            $items = $request->items;
        } else {
            $faker = Factory::create();
            $items = [
                [
                    'name' => $faker->catchPhrase(),
                    'price' => $request->amount,
                    'qty' => 1,
                ]
            ];
        }

        $purchase = Purchase::create([
            'user_id' => $user->id,
            'amount' => $request->amount,
            'reference' => $reference,
            'items' => $items,
        ]);

        // Dispatch event to trigger listeners (CheckAchievements, CheckBadges)
        PurchaseMade::dispatch($purchase, $user);

        return response()->json([
            'success' => true,
            'message' => 'Purchase recorded successfully',
            'data' => [
                'purchase_id' => $purchase->id,
                'reference' => $reference,
                'amount' => $purchase->amount,
            ]
        ], 201);
    }
}
