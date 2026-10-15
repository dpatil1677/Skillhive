<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Show Checkout Page.
     */
    public function checkout(string $serviceSlug, Request $request)
    {
        $service = Service::with(['user', 'category'])->where('slug', $serviceSlug)->first();
        if (!$service) {
            abort(404);
        }

        // Prevent freelancer from purchasing their own service (allow admin to place test orders)
        if (Auth::check() && $service->user_id === Auth::id() && Auth::user()->role !== 'admin') {
            return redirect()->route('seller.dashboard')
                ->with('toast_error', 'You are the creator of this service! Freelancers do not pay for their own services — clients hire you and pay into escrow.');
        }

        $packageKey = strtolower($request->query('package', 'standard'));
        if (!isset($service['packages'][$packageKey])) {
            $packageKey = 'standard';
        }

        $package = $service['packages'][$packageKey];
        $platformFee = 50;
        $totalAmount = $package['price'] + $platformFee;
        $currentUser = Auth::user();

        return view('booking.checkout', [
            'service' => $service,
            'packageKey' => $packageKey,
            'package' => $package,
            'platformFee' => $platformFee,
            'totalAmount' => $totalAmount,
            'currentUser' => $currentUser,
        ]);
    }

    /**
     * Process order checkout — creates real Order and starts chat message in MySQL.
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'service_slug' => 'required|string',
            'package_key' => 'required|string',
            'requirements' => 'required|string|min:10',
        ]);

        $service = Service::with(['user', 'category'])->where('slug', $request->input('service_slug'))->first();
        if (!$service) {
            abort(404);
        }

        if (Auth::check() && $service->user_id === Auth::id() && Auth::user()->role !== 'admin') {
            return redirect()->route('seller.dashboard')
                ->with('toast_error', 'You cannot place an order on your own service.');
        }

        $packageKey = strtolower($request->input('package_key', 'standard'));
        $package = $service['packages'][$packageKey] ?? ($service['packages']['standard'] ?? ['price' => 499, 'delivery' => 3]);

        $clientId = Auth::id();
        $platformFee = 50;
        $totalAmount = $package['price'] + $platformFee;
        $deliveryDays = (int) ($package['delivery'] ?? 5);

        do {
            $orderNumber = 'SH-' . rand(1000, 9999);
        } while (Order::where('order_number', $orderNumber)->exists());

        $order = Order::create([
            'order_number' => $orderNumber,
            'client_id' => $clientId,
            'freelancer_id' => $service->user_id,
            'service_id' => $service->id,
            'package' => ucfirst($packageKey),
            'price' => $package['price'],
            'platform_fee' => $platformFee,
            'total_amount' => $totalAmount,
            'requirements' => $request->input('requirements'),
            'status' => 'pending',
            'delivery_date' => now()->addDays($deliveryDays),
        ]);

        // Start order conversation in chat system
        if ($clientId !== $service->user_id) {
            Message::create([
                'sender_id' => $clientId,
                'receiver_id' => $service->user_id,
                'message' => "Order #{$orderNumber} placed for '{$service->title}' ({$order->package} package). Requirements: " . $request->input('requirements'),
                'is_read' => false,
            ]);
        }

        // Create notifications for both parties
        if ($service->user_id) {
            \App\Models\Notification::create([
                'user_id' => $service->user_id,
                'title'   => 'New Order Received! 🎉',
                'message' => "Order #{$orderNumber} placed for '{$service->title}' by " . (Auth::user()->name ?? 'a client') . '.',
                'url'     => route('seller.orders.show', $orderNumber),
                'icon'    => 'bi-bag-check-fill',
                'type'    => 'order',
                'is_read' => false,
            ]);
        }

        \App\Models\Notification::create([
            'user_id' => $clientId,
            'title'   => 'Order Confirmed',
            'message' => "Your order #{$orderNumber} for '{$service->title}' has been successfully placed.",
            'url'     => route('orders.show', $orderNumber),
            'icon'    => 'bi-bag-check',
            'type'    => 'order',
            'is_read' => false,
        ]);

        return redirect()->route('orders.show', ['orderNumber' => $orderNumber])
            ->with('toast_success', 'Order ' . $orderNumber . ' placed successfully!');
    }
}
