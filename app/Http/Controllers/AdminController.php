<?php

namespace App\Http\Controllers;

use App\Helpers\UserStore;
use App\Models\Category;
use App\Models\Order;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminController extends Controller
{
    /**
     * Admin Dashboard view with real database counts, Chart.js trends, and platform metrics.
     */
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalServices = Service::count();
        $totalOrders = Order::count();

        $completedOrders = Order::whereRaw('LOWER(status) = ?', ['completed'])->count();
        $pendingOrders = Order::whereIn(DB::raw('LOWER(status)'), ['pending', 'accepted', 'in_progress', 'in progress', 'submitted'])->count();

        $platformRevenue = (float) Order::whereRaw('LOWER(status) = ?', ['completed'])->sum('platform_fee');
        if ($platformRevenue <= 0) {
            $platformRevenue = 150.00; // baseline escrow platform fee
        }

        // Chart.js: Orders count for each of the last 7 days
        $dayLabels = [];
        $dayOrders = [];
        for ($i = 6; $i >= 0; $i--) {
            $dt = now()->subDays($i);
            $dayLabels[] = $dt->format('D');
            $cnt = Order::whereDate('created_at', $dt->format('Y-m-d'))->count();
            $dayOrders[] = $cnt;
        }

        if (array_sum($dayOrders) == 0 && $totalOrders > 0) {
            $dayOrders[6] = $totalOrders;
        }

        $chartOrders = [
            'labels' => $dayLabels,
            'data' => $dayOrders,
        ];

        // Chart.js: Categories distribution
        $categories = Category::withCount('services')->get();
        $chartCategories = [
            'labels' => $categories->pluck('name')->take(6)->toArray(),
            'data' => $categories->pluck('services_count')->take(6)->toArray(),
        ];

        // Recent users & orders from database
        $recentUsers = User::latest()->take(5)->get()->map(fn($u) => [
            'id' => $u->id,
            'name' => $u->name,
            'email' => $u->email,
            'avatar' => $u->avatar,
            'role' => ucfirst($u->role),
            'college' => $u->college ?: 'SkillHive Member',
            'joined' => $u->created_at ? $u->created_at->diffForHumans() : 'Recently',
            'status' => 'Active',
        ])->toArray();

        $recentOrders = Order::with(['service', 'client', 'freelancer'])->latest()->take(5)->get()->map(fn($o) => [
            'id' => $o->id,
            'order_number' => $o->order_number,
            'service' => ['title' => $o->service ? $o->service->title : 'Custom Student Service'],
            'client_name' => $o->client_name,
            'freelancer' => ['name' => $o->freelancer ? $o->freelancer->name : 'SkillHive Freelancer'],
            'total_amount' => $o->total_amount,
            'status' => $o->status,
            'created_at' => $o->created_at ? $o->created_at->format('M d, Y') : 'Recently',
        ])->toArray();

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'totalServices' => $totalServices,
            'totalOrders' => $totalOrders,
            'completedOrders' => $completedOrders,
            'pendingOrders' => $pendingOrders,
            'platformRevenue' => $platformRevenue,
            'chartOrders' => $chartOrders,
            'chartCategories' => $chartCategories,
            'recentUsers' => $recentUsers,
            'recentOrders' => $recentOrders,
        ]);
    }

    // ================================================================
    // USERS CRUD
    // ================================================================

    /**
     * Admin Users Management page querying MySQL users.
     */
    public function users(Request $request)
    {
        $query = strtolower(trim($request->query('q', '')));
        $statusFilter = strtolower($request->query('status', 'all'));
        $roleFilter = strtolower($request->query('role', 'all'));

        $usersQuery = User::withCount(['services', 'ordersAsClient', 'ordersAsFreelancer'])->latest();

        if ($query) {
            $usersQuery->where(function ($q) use ($query) {
                $q->whereRaw('LOWER(name) LIKE ?', ["%{$query}%"])
                  ->orWhereRaw('LOWER(email) LIKE ?', ["%{$query}%"])
                  ->orWhereRaw('LOWER(college) LIKE ?', ["%{$query}%"]);
            });
        }

        if ($roleFilter !== 'all') {
            $usersQuery->where('role', $roleFilter);
        }

        $users = $usersQuery->get()->map(function ($u) {
            $ordersCount = $u->orders_as_freelancer_count + $u->orders_as_client_count;
            return [
                'id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'avatar' => $u->avatar,
                'college' => $u->college ?: 'SkillHive Member',
                'course' => $u->course ?: '',
                'bio' => $u->bio ?: '',
                'role' => $u->role,
                'role_label' => ucfirst($u->role),
                'services_count' => $u->services_count,
                'orders_count' => $ordersCount,
                'rating' => 4.9,
                'joined' => $u->created_at ? $u->created_at->format('M Y') : 'Recently',
                'status' => 'Active',
            ];
        })->toArray();

        return view('admin.users', [
            'users' => $users,
            'query' => $query,
            'statusFilter' => $statusFilter,
            'roleFilter' => $roleFilter,
        ]);
    }

    /**
     * Create new user as Admin.
     */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:user,seller,admin',
            'college'  => 'nullable|string|max:255',
            'course'   => 'nullable|string|max:255',
            'bio'      => 'nullable|string|max:1000',
        ]);

        $user = User::create([
            'name'     => trim($request->input('name')),
            'email'    => strtolower(trim($request->input('email'))),
            'password' => Hash::make($request->input('password')),
            'role'     => $request->input('role', 'user'),
            'college'  => trim($request->input('college', '')),
            'course'   => trim($request->input('course', '')),
            'bio'      => trim($request->input('bio', 'New SkillHive member.')),
            'skills'   => ['Student', ucfirst($request->input('role'))],
        ]);

        return redirect()->route('admin.users')
            ->with('toast_success', 'User "' . $user->name . '" created successfully with role ' . ucfirst($user->role) . '!');
    }

    /**
     * Update user details as Admin.
     */
    public function updateUser(int $id, Request $request)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email,' . $id,
            'role'     => 'required|in:user,seller,admin',
            'college'  => 'nullable|string|max:255',
            'course'   => 'nullable|string|max:255',
            'bio'      => 'nullable|string|max:1000',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name    = trim($request->input('name'));
        $user->email   = strtolower(trim($request->input('email')));
        $user->role    = $request->input('role');
        $user->college = trim($request->input('college', ''));
        $user->course  = trim($request->input('course', ''));
        $user->bio     = trim($request->input('bio', ''));

        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('toast_success', 'User "' . $user->name . '" updated successfully!');
    }

    /**
     * Delete user as Admin.
     */
    public function deleteUser(int $id)
    {
        if ($id === Auth::id()) {
            return back()->with('toast_error', 'You cannot delete your own admin account while logged in!');
        }

        $user = User::findOrFail($id);
        $name = $user->name;

        // Clean up or cascade user's services and orders
        Service::where('user_id', $id)->delete();
        $user->delete();

        return redirect()->route('admin.users')
            ->with('toast_success', 'User "' . $name . '" deleted permanently from database.');
    }

    /**
     * Update user status (Suspend / Reactivate).
     */
    public function updateUserStatus(int $id, Request $request)
    {
        $status = $request->input('status', 'Suspended');
        UserStore::updateUserStatus($id, $status);
        return back()->with('toast_success', 'User status updated to ' . $status . '.');
    }

    /**
     * Impersonate / Access user or freelancer dashboard as Admin.
     */
    public function impersonateUser(int $id)
    {
        $targetUser = User::findOrFail($id);

        // Save original admin ID in session
        if (!session()->has('admin_impersonator_id')) {
            session(['admin_impersonator_id' => Auth::id()]);
        }

        Auth::login($targetUser);

        if ($targetUser->role === 'seller') {
            return redirect()->route('seller.dashboard')
                ->with('toast_success', 'Now viewing Freelancer Dashboard as ' . $targetUser->name . ' (Admin Impersonation Mode)');
        }

        return redirect()->route('dashboard')
            ->with('toast_success', 'Now viewing Client Dashboard as ' . $targetUser->name . ' (Admin Impersonation Mode)');
    }

    /**
     * Stop impersonation and return to Admin panel.
     */
    public function stopImpersonating()
    {
        $adminId = session()->pull('admin_impersonator_id');

        if ($adminId) {
            $admin = User::find($adminId);
            if ($admin && $admin->role === 'admin') {
                Auth::login($admin);
                return redirect()->route('admin.dashboard')
                    ->with('toast_success', 'Exited impersonation mode. Welcome back to Admin Panel!');
            }
        }

        return redirect()->route('login');
    }

    // ================================================================
    // SERVICES CRUD
    // ================================================================

    /**
     * Admin Services Management page querying MySQL services.
     */
    public function services(Request $request)
    {
        $query = strtolower(trim($request->query('q', '')));

        $servicesQuery = Service::with(['user', 'category'])->latest();

        if ($query) {
            $servicesQuery->search($query);
        }

        $services = $servicesQuery->get()->map(fn($s) => [
            'id' => $s->id,
            'title' => $s->title,
            'slug' => $s->slug,
            'image' => $s->image ?: 'https://placehold.co/900x540/e8e0f5/6D28D9?text=SkillHive',
            'delivery_days' => $s->delivery_days,
            'price' => $s->price,
            'rating' => $s->rating,
            'reviews' => $s->reviews_count,
            'description' => $s->description,
            'category_id' => $s->category_id,
            'category' => $s->category_name,
            'user_id' => $s->user_id,
            'freelancer' => [
                'name' => $s->user ? $s->user->name : 'SkillHive Member',
                'avatar' => $s->user ? $s->user->avatar : '',
            ],
        ])->toArray();

        $categories = Category::all();
        $freelancers = User::whereIn('role', ['seller', 'admin'])->get(['id', 'name', 'email']);

        return view('admin.services', [
            'services' => $services,
            'query' => $query,
            'categories' => $categories,
            'freelancers' => $freelancers,
        ]);
    }

    /**
     * Create new service as Admin.
     */
    public function storeService(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|min:5|max:255',
            'user_id'       => 'required|exists:users,id',
            'category_id'   => 'required|exists:categories,id',
            'price'         => 'required|numeric|min:50',
            'delivery_days' => 'required|numeric|min:1',
            'description'   => 'required|string|min:15',
            'image'         => 'nullable|string',
        ]);

        $title = trim($request->input('title'));
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (Service::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $price = (float) $request->input('price');
        $delivery = (int) $request->input('delivery_days', 3);

        $packages = [
            'basic' => [
                'name' => 'Basic',
                'price' => $price,
                'delivery' => $delivery,
                'revisions' => 1,
                'features' => ['Core deliverables', 'Source files'],
            ],
            'standard' => [
                'name' => 'Standard',
                'price' => round($price * 1.6),
                'delivery' => $delivery + 2,
                'revisions' => 3,
                'features' => ['Core deliverables', 'Source files', 'High resolution', 'Commercial use'],
            ],
            'premium' => [
                'name' => 'Premium',
                'price' => round($price * 2.5),
                'delivery' => $delivery + 4,
                'revisions' => 5,
                'features' => ['All deliverables', 'Full source code', 'Priority support', 'Commercial license'],
            ],
        ];

        Service::create([
            'user_id'       => $request->input('user_id'),
            'category_id'   => $request->input('category_id'),
            'title'         => $title,
            'slug'          => $slug,
            'description'   => $request->input('description'),
            'image'         => $request->input('image') ?: 'https://placehold.co/900x540/e8e0f5/6D28D9?text=SkillHive',
            'price'         => $price,
            'delivery_days' => $delivery,
            'revisions'     => 1,
            'status'        => 'active',
            'rating'        => 5.0,
            'reviews_count' => 0,
            'packages'      => $packages,
        ]);

        return redirect()->route('admin.services')
            ->with('toast_success', 'Service "' . $title . '" created successfully!');
    }

    /**
     * Update service as Admin.
     */
    public function updateService(int $id, Request $request)
    {
        $service = Service::findOrFail($id);

        $request->validate([
            'title'         => 'required|string|min:5|max:255',
            'category_id'   => 'required|exists:categories,id',
            'price'         => 'required|numeric|min:50',
            'delivery_days' => 'required|numeric|min:1',
            'description'   => 'required|string|min:15',
            'image'         => 'nullable|string',
        ]);

        $service->title         = trim($request->input('title'));
        $service->category_id   = $request->input('category_id');
        $service->price         = (float) $request->input('price');
        $service->delivery_days = (int) $request->input('delivery_days');
        $service->description   = $request->input('description');

        if ($request->filled('image')) {
            $service->image = $request->input('image');
        }

        // Update basic package price
        $packages = $service->packages ?: [];
        if (isset($packages['basic'])) {
            $packages['basic']['price'] = $service->price;
            $packages['basic']['delivery'] = $service->delivery_days;
            $service->packages = $packages;
        }

        $service->save();

        return redirect()->route('admin.services')
            ->with('toast_success', 'Service updated successfully!');
    }

    /**
     * Delete service as admin from MySQL database.
     */
    public function deleteService(int $id)
    {
        Service::destroy($id);
        UserStore::deleteService($id);
        return back()->with('toast_success', 'Service deleted successfully from marketplace.');
    }

    // ================================================================
    // ORDERS CRUD
    // ================================================================

    /**
     * Admin Orders Management page querying MySQL orders.
     */
    public function orders(Request $request)
    {
        $query = strtolower(trim($request->query('q', '')));
        $status = strtolower($request->query('status', 'all'));

        $ordersQuery = Order::with(['service', 'client', 'freelancer'])->latest();

        if ($query) {
            $ordersQuery->where(function ($q) use ($query) {
                $q->whereRaw('LOWER(order_number) LIKE ?', ["%{$query}%"])
                  ->orWhereHas('service', fn($sq) => $sq->whereRaw('LOWER(title) LIKE ?', ["%{$query}%"]))
                  ->orWhereHas('client', fn($cq) => $cq->whereRaw('LOWER(name) LIKE ?', ["%{$query}%"]));
            });
        }

        if ($status !== 'all') {
            $ordersQuery->whereRaw('LOWER(status) = ?', [$status]);
        }

        $orders = $ordersQuery->get()->map(fn($o) => [
            'id'                => $o->id,
            'order_number'      => $o->order_number,
            'service_title'     => $o->service ? $o->service->title : 'SkillHive Service',
            'client_name'       => $o->client_name,
            'freelancer_name'   => $o->freelancer ? $o->freelancer->name : 'SkillHive Freelancer',
            'package'           => $o->package ?: 'Standard',
            'price'             => $o->price,
            'platform_fee'      => $o->platform_fee,
            'total_amount'      => $o->total_amount,
            'status'            => $o->status,
            'raw_status'        => $o->raw_status,
            'requirements'      => $o->requirements,
            'deliverables_note' => $o->deliverables_note,
            'deliverables_link' => $o->deliverables_link,
            'deliverables_file' => $o->deliverables_file,
            'created_at'        => $o->created_at ? $o->created_at->format('M d, Y') : 'Recently',
        ])->toArray();

        $services = Service::where('status', 'active')->get(['id', 'title', 'price', 'user_id']);
        $clients = User::whereIn('role', ['user', 'admin'])->get(['id', 'name', 'email']);

        return view('admin.orders', [
            'orders'     => $orders,
            'query'      => $query,
            'statusFilter' => $status,
            'services'   => $services,
            'clients'    => $clients,
        ]);
    }

    /**
     * Create / Place order as Admin.
     */
    public function storeOrder(Request $request)
    {
        $request->validate([
            'service_id'   => 'required|exists:services,id',
            'client_id'    => 'required|exists:users,id',
            'package'      => 'nullable|string',
            'requirements' => 'required|string|min:5',
        ]);

        $service = Service::findOrFail($request->input('service_id'));
        $pkgName = ucfirst($request->input('package', 'Standard'));
        $pkgKey  = strtolower($pkgName);
        $pkgData = $service->packages[$pkgKey] ?? ($service->packages['standard'] ?? ['price' => $service->price, 'delivery' => 3]);

        $price = (float) ($pkgData['price'] ?? $service->price);
        $platformFee = 50.00;
        $totalAmount = $price + $platformFee;

        do {
            $orderNumber = 'SH-' . rand(1000, 9999);
        } while (Order::where('order_number', $orderNumber)->exists());

        $order = Order::create([
            'order_number'  => $orderNumber,
            'client_id'     => $request->input('client_id'),
            'freelancer_id' => $service->user_id,
            'service_id'    => $service->id,
            'package'       => $pkgName,
            'price'         => $price,
            'platform_fee'  => $platformFee,
            'total_amount'  => $totalAmount,
            'requirements'  => $request->input('requirements'),
            'status'        => 'pending',
            'delivery_date' => now()->addDays((int) ($pkgData['delivery'] ?? 3)),
        ]);

        return redirect()->route('admin.orders')
            ->with('toast_success', 'Order #' . $orderNumber . ' created successfully in database!');
    }

    /**
     * Update order status as Admin.
     */
    public function updateOrderStatus(string $orderNumber, Request $request)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $request->validate([
            'status' => 'required|in:pending,accepted,in_progress,submitted,completed,cancelled',
        ]);

        $order->status = $request->input('status');
        $order->save();

        \App\Services\NotificationService::orderStatusUpdated($order, $order->status);

        return redirect()->route('admin.orders')
            ->with('toast_success', 'Order #' . $orderNumber . ' status updated to ' . ucfirst($order->status) . '!');
    }

    /**
     * Delete order as Admin.
     */
    public function deleteOrder(string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $num = $order->order_number;
        $order->delete();

        return redirect()->route('admin.orders')
            ->with('toast_success', 'Order #' . $num . ' deleted successfully.');
    }

    // ================================================================
    // CATEGORIES CRUD
    // ================================================================

    /**
     * Admin Categories Management page from MySQL database.
     */
    public function categories()
    {
        $categories = Category::withCount('services')->get()->map(fn($c) => [
            'id' => $c->id,
            'name' => $c->name,
            'slug' => $c->slug,
            'icon' => $c->icon ?: 'bi-tag',
            'description' => $c->description ?: 'Browse ' . $c->name . ' services on SkillHive',
            'count' => $c->services_count,
            'subcategories' => $c->subcategories,
        ])->toArray();

        return view('admin.categories', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store new category as admin in MySQL database.
     */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'icon' => 'required|string',
        ]);

        $name = trim($request->input('name'));
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;
        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $category = Category::create([
            'name' => $name,
            'slug' => $slug,
            'icon' => $request->input('icon', 'bi-tag'),
            'description' => $request->input('description', 'Browse ' . $name . ' on SkillHive.'),
            'status' => 'active',
        ]);

        UserStore::addCategory([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'icon' => $category->icon,
            'description' => $category->description,
        ]);

        return redirect()->route('admin.categories')
            ->with('toast_success', 'Category "' . $name . '" created successfully in database!');
    }

    /**
     * Update category as admin in MySQL database.
     */
    public function updateCategory(int $id, Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $category = Category::findOrFail($id);
        $category->name = trim($request->input('name'));
        if ($request->filled('icon')) {
            $category->icon = $request->input('icon');
        }
        if ($request->filled('description')) {
            $category->description = $request->input('description');
        }
        $category->save();

        UserStore::updateCategory($id, [
            'name' => $category->name,
            'icon' => $category->icon,
            'description' => $category->description,
        ]);

        return redirect()->route('admin.categories')
            ->with('toast_success', 'Category updated successfully in database!');
    }

    /**
     * Delete category as admin from MySQL database.
     */
    public function deleteCategory(int $id)
    {
        Category::destroy($id);
        UserStore::deleteCategory($id);

        return redirect()->route('admin.categories')
            ->with('toast_success', 'Category deleted.');
    }

    // ================================================================
    // REVIEWS CRUD
    // ================================================================

    /**
     * Admin Reviews Management page from real MySQL reviews table.
     */
    public function reviews()
    {
        $reviews = Review::with(['user', 'freelancer', 'order.service'])
            ->latest()
            ->get()
            ->map(fn($r) => [
                'id' => $r->id,
                'reviewer' => $r->user ? $r->user->name : 'Verified Buyer',
                'user_id' => $r->user_id,
                'freelancer' => $r->freelancer ? $r->freelancer->name : 'SkillHive Freelancer',
                'freelancer_id' => $r->freelancer_id,
                'service' => $r->order && $r->order->service ? $r->order->service->title : 'SkillHive Service',
                'order_id' => $r->order_id,
                'rating' => $r->rating,
                'comment' => $r->comment,
                'date' => $r->created_at ? $r->created_at->diffForHumans() : 'Recently',
                'status' => 'Approved',
            ])->toArray();

        $freelancers = User::whereIn('role', ['seller', 'admin'])->get(['id', 'name']);
        $users = User::get(['id', 'name']);
        $services = Service::get(['id', 'title', 'user_id']);

        return view('admin.reviews', [
            'reviews'     => $reviews,
            'freelancers' => $freelancers,
            'users'       => $users,
            'services'    => $services,
        ]);
    }

    /**
     * Create review as Admin.
     */
    public function storeReview(Request $request)
    {
        $request->validate([
            'user_id'       => 'required|exists:users,id',
            'freelancer_id' => 'required|exists:users,id',
            'rating'        => 'required|integer|min:1|max:5',
            'comment'       => 'required|string|min:3|max:1000',
        ]);

        Review::create([
            'user_id'       => $request->input('user_id'),
            'freelancer_id' => $request->input('freelancer_id'),
            'order_id'      => $request->input('order_id') ?: 1,
            'rating'        => (int) $request->input('rating'),
            'comment'       => trim($request->input('comment')),
        ]);

        return redirect()->route('admin.reviews')
            ->with('toast_success', 'Review added successfully!');
    }

    /**
     * Update review as Admin.
     */
    public function updateReview(int $id, Request $request)
    {
        $review = Review::findOrFail($id);

        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'comment' => 'required|string|min:3|max:1000',
        ]);

        $review->rating = (int) $request->input('rating');
        $review->comment = trim($request->input('comment'));
        $review->save();

        return redirect()->route('admin.reviews')
            ->with('toast_success', 'Review updated successfully!');
    }

    /**
     * Delete review as Admin.
     */
    public function deleteReview(int $id)
    {
        Review::destroy($id);

        return redirect()->route('admin.reviews')
            ->with('toast_success', 'Review deleted successfully.');
    }

    /**
     * Admin Payout Requests Management.
     */
    public function withdrawals(Request $request)
    {
        $status = $request->query('status');
        $query = \App\Models\Withdrawal::with('user')->latest();
        if ($status && in_array($status, ['pending', 'processing', 'completed', 'rejected'])) {
            $query->where('status', $status);
        }
        $withdrawals = $query->get();

        return view('admin.withdrawals', [
            'withdrawals' => $withdrawals,
            'currentStatus' => $status,
        ]);
    }

    /**
     * Update withdrawal status as Admin.
     */
    public function updateWithdrawalStatus(int $id, Request $request)
    {
        $withdrawal = \App\Models\Withdrawal::with('user')->findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,completed,rejected',
            'admin_notes' => 'nullable|string|max:500',
        ]);

        $withdrawal->status = $request->input('status');
        $withdrawal->admin_notes = $request->input('admin_notes');
        if ($withdrawal->status === 'completed') {
            $withdrawal->processed_at = now();
        }
        $withdrawal->save();

        \App\Services\NotificationService::withdrawalStatusUpdated($withdrawal);

        return back()->with('toast_success', "Withdrawal #{$withdrawal->reference_id} status updated to " . ucfirst($withdrawal->status));
    }
}
