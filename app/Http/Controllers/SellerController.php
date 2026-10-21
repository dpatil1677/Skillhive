<?php

namespace App\Http\Controllers;

use App\Helpers\UserStore;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use App\Models\Withdrawal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SellerController extends Controller
{
    /**
     * Seller Dashboard with Chart.js revenue chart data from real database records.
     */
    public function dashboard()
    {
        $user = Auth::user();
        $sellerId = Auth::id();

        $allOrders = Order::with(['service', 'client'])
            ->where('freelancer_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate Seller Stats from DB
        $totalEarnings = (float) $allOrders->filter(fn($o) => strtolower($o->status) === 'completed')->sum('price');
        $activeOrdersCount = $allOrders->filter(fn($o) => in_array(strtolower($o->status), ['pending', 'accepted', 'in_progress', 'in progress', 'submitted']))->count();
        $completedOrdersCount = $allOrders->filter(fn($o) => strtolower($o->status) === 'completed')->count();

        // Real reviews from DB
        $dbReviews = Review::with(['user', 'order.service'])
            ->where('freelancer_id', $sellerId)
            ->latest()
            ->get();

        $avgRating = $dbReviews->count() > 0 ? round($dbReviews->avg('rating'), 1) : 5.0;

        // Chart.js revenue data from past 6 months
        $months = [];
        $earningsData = [];
        for ($i = 5; $i >= 0; $i--) {
            $dt = now()->subMonths($i);
            $monthLabel = $dt->format('M');
            $months[] = $monthLabel;
            $monthlySum = $allOrders
                ->filter(fn($o) => strtolower($o->status) === 'completed' && $o->created_at && $o->created_at->format('Y-m') === $dt->format('Y-m'))
                ->sum('price');
            $earningsData[] = (int) $monthlySum;
        }

        // If no past multi-month history but has current completed earnings, display realistically
        if (array_sum($earningsData) == 0 && $totalEarnings > 0) {
            $earningsData[5] = (int) $totalEarnings;
        }

        $chartData = [
            'labels' => $months,
            'earnings' => $earningsData,
        ];

        // Seller services from DB
        $myServices = Service::with(['category'])
            ->where('user_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        // Seller recent orders from DB
        $recentOrders = $allOrders->take(3);

        $recentReviews = $dbReviews->take(3)->map(function ($r) {
            return [
                'id' => $r->id,
                'name' => $r->user ? $r->user->name : 'Verified Client',
                'avatar' => $r->user ? $r->user->avatar : 'https://ui-avatars.com/api/?name=Client&background=6D28D9&color=fff&size=64',
                'rating' => $r->rating,
                'text' => $r->comment,
                'date' => $r->created_at ? $r->created_at->diffForHumans() : 'Recently',
                'service' => $r->order && $r->order->service ? $r->order->service->title : 'SkillHive Project',
            ];
        })->toArray();

        return view('seller.dashboard', [
            'user' => $user,
            'totalEarnings' => $totalEarnings,
            'activeOrdersCount' => $activeOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'avgRating' => $avgRating,
            'chartData' => $chartData,
            'myServices' => $myServices,
            'recentOrders' => $recentOrders,
            'recentReviews' => $recentReviews,
        ]);
    }

    /**
     * My Services list from MySQL database.
     */
    public function services()
    {
        $sellerId = Auth::id();
        $services = Service::with(['category', 'user'])
            ->where('user_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('seller.services', [
            'services' => $services,
        ]);
    }

    /**
     * Show Multi-step Create Service wizard.
     */
    public function createService()
    {
        $categories = Category::all();

        return view('seller.create-service', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store new service from Create Service wizard in database.
     */
    public function storeService(Request $request)
    {
        $request->validate([
            'title' => 'required|string|min:10|max:255',
            'category' => 'required|string',
            'description' => 'required|string|min:20',
            'basic_price' => 'required|numeric|min:100',
            'basic_delivery' => 'required|numeric|min:1',
        ]);

        $userId = Auth::id();
        $categoryName = $request->input('category');

        $category = Category::where('name', $categoryName)
            ->orWhere('slug', Str::slug($categoryName))
            ->first();

        $categoryId = $category ? $category->id : 1;

        $title = trim($request->input('title'));
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (Service::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $basicPrice = (float) $request->input('basic_price', 499);
        $basicDelivery = (int) $request->input('basic_delivery', 2);
        $basicRevisions = (int) $request->input('basic_revisions', 1);

        $packages = [
            'basic' => [
                'name' => 'Basic',
                'price' => $basicPrice,
                'delivery' => $basicDelivery,
                'revisions' => $basicRevisions,
                'features' => array_values(array_filter(array_map('trim', explode("\n", $request->input('basic_features', "Basic deliverable\nStandard support"))))),
            ],
            'standard' => [
                'name' => 'Standard',
                'price' => (float) ($request->input('standard_price') ?: $basicPrice * 2),
                'delivery' => (int) ($request->input('standard_delivery') ?: $basicDelivery + 2),
                'revisions' => (int) ($request->input('standard_revisions') ?: 3),
                'features' => array_values(array_filter(array_map('trim', explode("\n", $request->input('standard_features', "Everything in Basic\nSource files included\nPriority support"))))),
            ],
            'premium' => [
                'name' => 'Premium',
                'price' => (float) ($request->input('premium_price') ?: $basicPrice * 4),
                'delivery' => (int) ($request->input('premium_delivery') ?: $basicDelivery + 5),
                'revisions' => (int) ($request->input('premium_revisions') ?: 5),
                'features' => array_values(array_filter(array_map('trim', explode("\n", $request->input('premium_features', "Everything in Standard\nCommercial license\nFull documentation\n14 days support"))))),
            ],
        ];

        $imageUrl = $request->input('image_url') ?: 'https://placehold.co/900x540/e8e0f5/6D28D9?text=' . urlencode(substr($title, 0, 25));

        $service = Service::create([
            'user_id' => $userId,
            'category_id' => $categoryId,
            'title' => $title,
            'slug' => $slug,
            'description' => $request->input('description'),
            'image' => $imageUrl,
            'price' => $basicPrice,
            'delivery_days' => $basicDelivery,
            'revisions' => $basicRevisions,
            'status' => 'active',
            'rating' => 5.0,
            'reviews_count' => 0,
            'packages' => $packages,
        ]);

        return redirect()->route('service.show', $service->slug)
            ->with('toast_success', 'Service "' . $service->title . '" published successfully to the marketplace!');
    }

    /**
     * Edit existing service.
     */
    public function editService(int $id)
    {
        $service = Service::with(['user', 'category'])
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $categories = Category::all();

        return view('seller.edit-service', [
            'service' => $service,
            'categories' => $categories,
        ]);
    }

    /**
     * Update existing service in database.
     */
    public function updateService(int $id, Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'required|string|min:20',
        ]);

        $service = Service::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $categoryName = $request->input('category');
        $category = Category::where('name', $categoryName)
            ->orWhere('slug', Str::slug($categoryName))
            ->first();

        $basicPrice = (float) $request->input('basic_price', $service->packages['basic']['price'] ?? 499);
        $standardPrice = (float) $request->input('standard_price', $service->packages['standard']['price'] ?? 999);
        $premiumPrice = (float) $request->input('premium_price', $service->packages['premium']['price'] ?? 1999);

        $packages = $service->packages ?: [];
        $packages['basic'] = array_merge($packages['basic'] ?? [], [
            'name' => 'Basic',
            'price' => $basicPrice,
            'delivery' => (int) ($packages['basic']['delivery'] ?? 2),
            'revisions' => (int) ($packages['basic']['revisions'] ?? 1),
            'features' => $packages['basic']['features'] ?? ['Basic deliverable'],
        ]);
        $packages['standard'] = array_merge($packages['standard'] ?? [], [
            'name' => 'Standard',
            'price' => $standardPrice,
            'delivery' => (int) ($packages['standard']['delivery'] ?? 4),
            'revisions' => (int) ($packages['standard']['revisions'] ?? 3),
            'features' => $packages['standard']['features'] ?? ['Standard deliverable', 'Source files'],
        ]);
        $packages['premium'] = array_merge($packages['premium'] ?? [], [
            'name' => 'Premium',
            'price' => $premiumPrice,
            'delivery' => (int) ($packages['premium']['delivery'] ?? 7),
            'revisions' => (int) ($packages['premium']['revisions'] ?? 5),
            'features' => $packages['premium']['features'] ?? ['Premium deliverable', 'Full source code', 'Priority support'],
        ]);

        $service->title = trim($request->input('title'));
        if ($category) {
            $service->category_id = $category->id;
        }
        $service->description = $request->input('description');
        if ($request->filled('image_url')) {
            $service->image = $request->input('image_url');
        }
        $service->price = $basicPrice;
        $service->packages = $packages;
        $service->save();

        return redirect()->route('seller.services')
            ->with('toast_success', 'Service "' . $service->title . '" updated successfully in database!');
    }

    /**
     * Delete service from database.
     */
    public function destroyService(int $id)
    {
        Service::where('id', $id)->where('user_id', Auth::id())->delete();
        UserStore::deleteService($id);

        return redirect()->route('seller.services')
            ->with('toast_success', 'Service deleted successfully.');
    }

    /**
     * Seller Orders Management list view.
     */
    public function sellerOrders(Request $request)
    {
        $sellerId = Auth::id();
        $tab      = strtolower($request->query('tab', 'all'));

        $allOrders = Order::with(['service.category', 'client'])
            ->where('freelancer_id', $sellerId)
            ->orderBy('created_at', 'desc')
            ->get();

        $rawStatus = fn($o) => $o->raw_status;

        $filteredOrders = match ($tab) {
            'pending'   => $allOrders->filter(fn($o) => $rawStatus($o) === 'pending'),
            'active'    => $allOrders->filter(fn($o) => in_array($rawStatus($o), ['accepted', 'in_progress', 'submitted'])),
            'completed' => $allOrders->filter(fn($o) => $rawStatus($o) === 'completed'),
            'cancelled' => $allOrders->filter(fn($o) => $rawStatus($o) === 'cancelled'),
            default     => $allOrders,
        };

        $counts = [
            'all'       => $allOrders->count(),
            'pending'   => $allOrders->filter(fn($o) => $rawStatus($o) === 'pending')->count(),
            'active'    => $allOrders->filter(fn($o) => in_array($rawStatus($o), ['accepted', 'in_progress', 'submitted']))->count(),
            'completed' => $allOrders->filter(fn($o) => $rawStatus($o) === 'completed')->count(),
            'cancelled' => $allOrders->filter(fn($o) => $rawStatus($o) === 'cancelled')->count(),
        ];

        return view('seller.orders', [
            'orders'    => $filteredOrders,
            'activeTab' => $tab,
            'counts'    => $counts,
        ]);
    }

    /**
     * Seller / Freelancer Order Detail view — shows client requirements,
     * allows accept, submit work, cancel, and message client.
     */
    public function showOrder(string $orderNumber)
    {
        $sellerId = Auth::id();

        $order = Order::with(['service.category', 'client', 'review'])
            ->where('order_number', $orderNumber)
            ->where('freelancer_id', $sellerId)
            ->firstOrFail();

        return view('seller.order-show', ['order' => $order]);
    }

    /**
     * Freelancer Payouts & Earnings Management.
     */
    public function payouts()
    {
        $sellerId = Auth::id();
        $user = Auth::user();

        $completedOrdersSum = (float) Order::where('freelancer_id', $sellerId)
            ->where('status', 'completed')
            ->sum('price');

        $withdrawnSum = (float) Withdrawal::where('user_id', $sellerId)
            ->whereIn('status', ['pending', 'processing', 'completed'])
            ->sum('amount');

        $completedWithdrawnSum = (float) Withdrawal::where('user_id', $sellerId)
            ->where('status', 'completed')
            ->sum('amount');

        $availableBalance = max(0, $completedOrdersSum - $withdrawnSum);

        $withdrawals = Withdrawal::where('user_id', $sellerId)
            ->latest()
            ->get();

        return view('seller.payouts', [
            'user' => $user,
            'totalEarnings' => $completedOrdersSum,
            'withdrawnSum' => $completedWithdrawnSum,
            'availableBalance' => $availableBalance,
            'withdrawals' => $withdrawals,
        ]);
    }

    /**
     * Submit a payout / withdrawal request.
     */
    public function requestPayout(Request $request)
    {
        $sellerId = Auth::id();

        $completedOrdersSum = (float) Order::where('freelancer_id', $sellerId)
            ->where('status', 'completed')
            ->sum('price');

        $withdrawnSum = (float) Withdrawal::where('user_id', $sellerId)
            ->whereIn('status', ['pending', 'processing', 'completed'])
            ->sum('amount');

        $availableBalance = max(0, $completedOrdersSum - $withdrawnSum);

        $request->validate([
            'amount' => 'required|numeric|min:100',
            'method' => 'required|in:upi,bank',
            'upi_id' => 'required_if:method,upi|nullable|string|max:100',
            'account_number' => 'required_if:method,bank|nullable|string|min:8|max:25',
            'ifsc' => 'required_if:method,bank|nullable|string|max:15',
            'bank_name' => 'required_if:method,bank|nullable|string|max:100',
            'account_holder' => 'required_if:method,bank|nullable|string|max:100',
        ]);

        $requestedAmount = (float) $request->input('amount');

        if ($requestedAmount > $availableBalance) {
            return back()->with('toast_error', 'Withdrawal amount exceeds your available balance of ₹' . number_format($availableBalance, 2));
        }

        if ($request->input('method') === 'upi') {
            $payoutDetails = [
                'upi_id' => trim($request->input('upi_id')),
            ];
        } else {
            $payoutDetails = [
                'account_holder' => trim($request->input('account_holder')),
                'account_number' => trim($request->input('account_number')),
                'ifsc' => strtoupper(trim($request->input('ifsc'))),
                'bank_name' => trim($request->input('bank_name')),
            ];
        }

        $refId = 'WD-' . strtoupper(Str::random(8));

        Withdrawal::create([
            'user_id' => $sellerId,
            'reference_id' => $refId,
            'amount' => $requestedAmount,
            'method' => $request->input('method'),
            'payout_details' => $payoutDetails,
            'status' => 'pending',
        ]);

        Notification::create([
            'user_id' => $sellerId,
            'title' => 'Payout Requested',
            'message' => "Your withdrawal request for ₹" . number_format($requestedAmount, 2) . " ({$refId}) is submitted and being processed.",
            'url' => route('seller.payouts'),
            'icon' => 'bi-wallet2',
            'type' => 'payout',
        ]);

        return redirect()->route('seller.payouts')->with('toast_success', 'Withdrawal request of ₹' . number_format($requestedAmount, 2) . ' submitted successfully!');
    }
}
