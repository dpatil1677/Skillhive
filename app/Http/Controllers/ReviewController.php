<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * Submit review for a completed order and recalculate service stats.
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string',
            'rating' => 'required|integer|min:1|max:5',
            'review_text' => 'required|string|min:5|max:1000',
        ]);

        $orderNumber = $request->input('order_number');
        $order = Order::where('order_number', $orderNumber)->first();

        if (!$order) {
            return back()->with('toast_error', 'Order not found.');
        }

        $userId = Auth::id();

        if (Review::where('order_id', $order->id)->where('user_id', $userId)->exists()) {
            return back()->with('toast_warning', 'You have already submitted a review for this order.');
        }

        $review = Review::create([
            'order_id' => $order->id,
            'user_id' => $userId,
            'freelancer_id' => $order->freelancer_id,
            'rating' => (int) $request->input('rating'),
            'comment' => $request->input('review_text'),
        ]);

        // Recalculate and update the service's aggregate rating & review count
        if ($order->service_id) {
            $service = Service::find($order->service_id);
            if ($service) {
                $serviceOrderIds = Order::where('service_id', $service->id)->pluck('id');
                $avg = Review::whereIn('order_id', $serviceOrderIds)->avg('rating');
                $cnt = Review::whereIn('order_id', $serviceOrderIds)->count();
                $service->rating = $avg ? round($avg, 1) : 5.0;
                $service->reviews_count = $cnt;
                $service->save();
            }
        }

        return back()->with('toast_success', 'Thank you! Your ' . $request->input('rating') . '-star review has been published.');
    }
}
