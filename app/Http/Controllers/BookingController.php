<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Order;
use App\Models\Service;
use App\Services\NotificationService;
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

        $packages = $service->packages;
        if (!is_array($packages) || empty($packages)) {
            $basePrice = (float) ($service->price ?? 499);
            $baseDelivery = (int) ($service->delivery_days ?? 3);
            $packages = [
                'basic' => [
                    'name' => 'Basic',
                    'price' => $basePrice,
                    'delivery' => $baseDelivery,
                    'revisions' => 1,
                    'features' => ['Basic deliverable', 'Source file included', '1 Revision'],
                ],
                'standard' => [
                    'name' => 'Standard',
                    'price' => round($basePrice * 1.8),
                    'delivery' => max(1, $baseDelivery + 2),
                    'revisions' => 3,
                    'features' => ['Everything in Basic', 'Database setup', 'Admin dashboard', '3 Revisions'],
                ],
                'premium' => [
                    'name' => 'Premium',
                    'price' => round($basePrice * 3.2),
                    'delivery' => max(1, $baseDelivery + 5),
                    'revisions' => 5,
                    'features' => ['Full production app', 'Deployment help', 'Commercial license', '14 days support'],
                ],
            ];
            $service->packages = $packages;
        }

        $packageKey = strtolower(trim((string) $request->query('package', 'standard')));
        if (!isset($packages[$packageKey])) {
            $packageKey = isset($packages['standard']) ? 'standard' : (array_key_first($packages) ?? 'basic');
        }

        $package = $packages[$packageKey];
        $packagePrice = (float) ($package['price'] ?? ($service->price ?? 499));
        $packageDelivery = max(1, (int) ($package['delivery'] ?? ($service->delivery_days ?? 3)));
        $packageName = $package['name'] ?? ucfirst($packageKey);
        $packageFeatures = is_array($package['features'] ?? null)
            ? $package['features']
            : (array_filter(array_map('trim', explode("\n", (string) ($package['features'] ?? '')))) ?: ['Standard deliverable']);

        $package['price'] = $packagePrice;
        $package['delivery'] = $packageDelivery;
        $package['name'] = $packageName;
        $package['features'] = $packageFeatures;

        $platformFee = 50.0;
        $totalAmount = $packagePrice + $platformFee;
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

        $packages = $service->packages;
        if (!is_array($packages) || empty($packages)) {
            $basePrice = (float) ($service->price ?? 499);
            $baseDelivery = (int) ($service->delivery_days ?? 3);
            $packages = [
                'basic' => ['name' => 'Basic', 'price' => $basePrice, 'delivery' => $baseDelivery, 'revisions' => 1, 'features' => ['Basic deliverable']],
                'standard' => ['name' => 'Standard', 'price' => round($basePrice * 1.8), 'delivery' => max(1, $baseDelivery + 2), 'revisions' => 3, 'features' => ['Standard deliverable']],
                'premium' => ['name' => 'Premium', 'price' => round($basePrice * 3.2), 'delivery' => max(1, $baseDelivery + 5), 'revisions' => 5, 'features' => ['Premium deliverable']],
            ];
        }

        $packageKey = strtolower(trim((string) $request->input('package_key', 'standard')));
        if (!isset($packages[$packageKey])) {
            $packageKey = isset($packages['standard']) ? 'standard' : (array_key_first($packages) ?? 'basic');
        }

        $package = $packages[$packageKey];
        $clientId = Auth::id();
        $packagePrice = (float) ($package['price'] ?? ($service->price ?? 499));
        $platformFee = 50.0;
        $totalAmount = $packagePrice + $platformFee;
        $deliveryDays = max(1, (int) ($package['delivery'] ?? ($service->delivery_days ?? 5)));
        $packageName = $package['name'] ?? ucfirst($packageKey);

        do {
            $orderNumber = 'SH-' . rand(1000, 9999);
        } while (Order::where('order_number', $orderNumber)->exists());

        $order = Order::create([
            'order_number' => $orderNumber,
            'client_id' => $clientId,
            'freelancer_id' => $service->user_id,
            'service_id' => $service->id,
            'package' => $packageName,
            'price' => $packagePrice,
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

        // Dispatch transactional email and in-app notifications
        NotificationService::orderPlaced($order);

        return redirect()->route('orders.show', ['orderNumber' => $orderNumber])
            ->with('toast_success', 'Order ' . $orderNumber . ' placed successfully!');
    }
}
