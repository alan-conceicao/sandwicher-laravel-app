<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function store(Request $request, string $registrationCode)
    {
        /** @var User $user */
        $user = $request->user();

        $meal = Meal::getMealByRegistrationCode($registrationCode);

        if ($meal->getAttribute('status') == 0) {
            return redirect()->back()
                ->with('message','Meal registration not possible. The meal was already closed.');
        }

        $orderExistsForMeal = Order::orderExistsForMealAndUser(
            $meal->getAttribute('id'),
            $user->getAttribute('id')
        );

        if ($orderExistsForMeal) {
            return redirect()->back()
                ->with('message','A previous order was already placed for this meal.');
        }

        $order = new Order();
        $order->setAttribute('meal_id', (int) $meal->getAttribute('id'));
        $order->setAttribute('user_id', (int) $user->getAttribute('id'));
        $order->setAttribute('bread_type_id', (int) $request->input('bread_type'));
        $order->setAttribute('bread_size_id', (int) $request->input('bread_size'));
        $order->setAttribute('taste_id', (int) $request->input('taste'));
        $order->setAttribute('extra_id', (int) $request->input('extra'));
        $order->setAttribute('vegetable_id', (int) $request->input('vegetable'));
        $order->setAttribute('sauce_id', (int) $request->input('sauce'));
        $order->setAttribute('oven_baked', (bool) $request->input('oven_baked'));
        $order->push();

        return redirect()->back()->with('message','Meal order registered successfully.')->with('order', $order);
    }

    public function update(Request $request, string $registrationCode)
    {
        /** @var User $user */
        $user = $request->user();

        $meal = Meal::getMealByRegistrationCode($registrationCode);

        $order = Order::getByMealAndUser(
            $meal->getAttribute('id'),
            $user->getAttribute('id')
        );

        $order->setAttribute('bread_type_id', (int) $request->input('bread_type'));
        $order->setAttribute('bread_size_id', (int) $request->input('bread_size'));
        $order->setAttribute('taste_id', (int) $request->input('taste'));
        $order->setAttribute('extra_id', (int) $request->input('extra'));
        $order->setAttribute('vegetable_id', (int) $request->input('vegetable'));
        $order->setAttribute('sauce_id', (int) $request->input('sauce'));
        $order->setAttribute('oven_baked', (bool) $request->input('oven_baked'));
        $order->push();

        return redirect()->back()->with('message','Meal order updated successfully.');
    }
}
