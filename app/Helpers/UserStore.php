<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class UserStore
{
    /**
     * Get currently logged-in user as an array (compatible with existing Blade views).
     * Bridges real Auth::user() Eloquent model to the array format views expect.
     */
    public static function user(): ?array
    {
        // Prefer real Laravel Auth user
        if (Auth::check()) {
            $u = Auth::user();
            return [
                'id'            => $u->id,
                'name'          => $u->name,
                'email'         => $u->email,
                'role'          => $u->role ?? 'user',
                'college'       => $u->college ?? 'Student',
                'course'        => $u->course ?? 'Student Member',
                'bio'           => $u->bio ?? 'SkillHive student freelancer.',
                'skills'        => is_array($u->skills) ? $u->skills : ($u->skills ? json_decode($u->skills, true) : ['Web Development']),
                'avatar'        => $u->profile_image
                                    ?? 'https://ui-avatars.com/api/?name=' . urlencode($u->name) . '&background=6D28D9&color=fff&size=128',
                'profile_image' => $u->profile_image ?? null,
                // Seller stats (mock until seller stats table exists)
                'orders_count'  => 0,
                'rating'        => 4.9,
                'reviews_count' => 0,
                'response_time' => '1 hour',
            ];
        }

        // Fallback: legacy session key (used by demo/mock flow)
        return session('user', null);
    }

    /**
     * Check if a user is logged in (real Auth or legacy session).
     */
    public static function check(): bool
    {
        return Auth::check() || session()->has('user');
    }

    /**
     * Check if current user is admin.
     */
    public static function isAdmin(): bool
    {
        if (Auth::check()) {
            return Auth::user()->role === 'admin';
        }
        $user = session('user');
        return $user && ($user['role'] ?? '') === 'admin';
    }

    /**
     * Log in a user by storing to session (legacy/demo) AND initialising mock data.
     * Real Laravel auth is handled separately via Auth::attempt() / Auth::login() in AuthController.
     */
    public static function login(array $userData): void
    {
        session(['user' => $userData]);
        self::initOrders();
        self::initServices();
        self::initCategories();
        self::initPortfolio();
        self::initMessages();
        self::initNotifications();
        self::initUsers();
    }

    /**
     * Initialise mock session data for real Auth user (called after Auth::login).
     */
    public static function initForAuthUser(): void
    {
        self::initOrders();
        self::initServices();
        self::initCategories();
        self::initPortfolio();
        self::initMessages();
        self::initNotifications();
        self::initUsers();
    }

    /**
     * Log out current user.
     */
    public static function logout(): void
    {
        Auth::logout();
        session()->forget('user');
    }

    /**
     * Update logged-in user profile.
     * Updates the real DB user if authenticated, then refreshes the session.
     */
    public static function updateProfile(array $data): array
    {
        // Update real DB user if authenticated
        if (Auth::check()) {
            $dbUser = Auth::user();
            $dbUser->name    = $data['name'] ?? $dbUser->name;
            $dbUser->college = $data['college'] ?? $dbUser->college;
            $dbUser->course  = $data['course'] ?? $dbUser->course;
            $dbUser->bio     = $data['bio'] ?? $dbUser->bio;
            if (isset($data['skills'])) {
                $dbUser->skills = is_array($data['skills'])
                    ? $data['skills']
                    : array_map('trim', explode(',', $data['skills']));
            }
            $dbUser->save();
            return self::user(); // re-build from Auth::user()
        }

        // Legacy session fallback
        $user = session('user');
        if (!$user) return [];

        $updated = array_merge($user, [
            'name'    => $data['name'] ?? $user['name'],
            'college' => $data['college'] ?? $user['college'],
            'course'  => $data['course'] ?? $user['course'],
            'bio'     => $data['bio'] ?? $user['bio'],
            'skills'  => isset($data['skills']) ? (is_array($data['skills']) ? $data['skills'] : array_map('trim', explode(',', $data['skills']))) : $user['skills'],
        ]);

        if (!empty($data['avatar'])) {
            $updated['avatar'] = $data['avatar'];
        }

        session(['user' => $updated]);
        return $updated;
    }

    // ============================================================
    // CATEGORIES STORE (ADMIN & MARKETPLACE SYNC)
    // ============================================================

    public static function initCategories(): void
    {
        if (session()->has('app_categories')) return;
        session(['app_categories' => MockData::defaultCategories()]);
    }

    public static function categories(): array
    {
        self::initCategories();
        return session('app_categories', MockData::defaultCategories());
    }

    public static function addCategory(array $data): array
    {
        $cats = self::categories();
        $newCat = [
            'id' => rand(100, 999),
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
            'icon' => $data['icon'] ?? 'bi-grid-fill',
            'count' => 0,
            'description' => $data['description'] ?? '',
            'subcategories' => is_array($data['subcategories']) ? $data['subcategories'] : array_map('trim', explode(',', $data['subcategories'] ?? 'General')),
        ];

        $cats[] = $newCat;
        session(['app_categories' => $cats]);
        return $newCat;
    }

    public static function updateCategory(int $id, array $data): ?array
    {
        $cats = self::categories();
        $updated = null;

        foreach ($cats as &$c) {
            if ($c['id'] == $id) {
                $c['name'] = $data['name'] ?? $c['name'];
                $c['slug'] = Str::slug($c['name']);
                $c['icon'] = $data['icon'] ?? $c['icon'];
                $c['description'] = $data['description'] ?? $c['description'];
                if (isset($data['subcategories'])) {
                    $c['subcategories'] = is_array($data['subcategories']) ? $data['subcategories'] : array_map('trim', explode(',', $data['subcategories']));
                }
                $updated = $c;
                break;
            }
        }

        if ($updated) {
            session(['app_categories' => $cats]);
        }

        return $updated;
    }

    public static function deleteCategory(int $id): bool
    {
        $cats = self::categories();
        $filtered = array_values(array_filter($cats, fn($c) => $c['id'] != $id));
        session(['app_categories' => $filtered]);
        return true;
    }

    // ============================================================
    // USERS STORE (ADMIN USER MANAGEMENT)
    // ============================================================

    public static function initUsers(): void
    {
        if (session()->has('admin_users')) return;

        $users = [
            [
                'id' => 1,
                'name' => 'Dhruv Patil',
                'email' => 'dhruv.patil@mitpune.edu.in',
                'avatar' => 'https://ui-avatars.com/api/?name=Dhruv+Patil&background=6D28D9&color=fff&size=64',
                'college' => 'MIT Pune',
                'role' => 'Client & Seller',
                'services_count' => 3,
                'orders_count' => 5,
                'rating' => 4.9,
                'status' => 'Active',
                'joined' => 'Jan 2025',
            ],
            [
                'id' => 2,
                'name' => 'Aarav Shah',
                'email' => 'aarav.shah@nid.edu.in',
                'avatar' => 'https://ui-avatars.com/api/?name=Aarav+Shah&background=0369a1&color=fff&size=64',
                'college' => 'NID Ahmedabad',
                'role' => 'Freelancer',
                'services_count' => 2,
                'orders_count' => 38,
                'rating' => 4.8,
                'status' => 'Active',
                'joined' => 'Mar 2025',
            ],
            [
                'id' => 3,
                'name' => 'Rahul Mehta',
                'email' => 'rahul.m@iitb.ac.in',
                'avatar' => 'https://ui-avatars.com/api/?name=Rahul+Mehta&background=059669&color=fff&size=64',
                'college' => 'IIT Bombay',
                'role' => 'Freelancer',
                'services_count' => 2,
                'orders_count' => 31,
                'rating' => 4.7,
                'status' => 'Active',
                'joined' => 'Feb 2025',
            ],
            [
                'id' => 4,
                'name' => 'Mitali Patel',
                'email' => 'mitali.p@srishti.edu.in',
                'avatar' => 'https://ui-avatars.com/api/?name=Mitali+Patel&background=dc2626&color=fff&size=64',
                'college' => 'Srishti Bangalore',
                'role' => 'Freelancer',
                'services_count' => 2,
                'orders_count' => 24,
                'rating' => 5.0,
                'status' => 'Active',
                'joined' => 'Apr 2025',
            ],
            [
                'id' => 5,
                'name' => 'Sneha Kulkarni',
                'email' => 'sneha.k@symbiosis.ac.in',
                'avatar' => 'https://ui-avatars.com/api/?name=Sneha+K&background=7c3aed&color=fff&size=64',
                'college' => 'Symbiosis Pune',
                'role' => 'Client',
                'services_count' => 0,
                'orders_count' => 8,
                'rating' => 5.0,
                'status' => 'Active',
                'joined' => 'May 2025',
            ],
        ];

        session(['admin_users' => $users]);
    }

    public static function adminUsers(): array
    {
        self::initUsers();
        return session('admin_users', []);
    }

    public static function updateUserStatus(int $userId, string $status): bool
    {
        $users = self::adminUsers();
        foreach ($users as &$u) {
            if ($u['id'] == $userId) {
                $u['status'] = $status;
                break;
            }
        }
        session(['admin_users' => $users]);
        return true;
    }

    // ============================================================
    // ORDERS STORE
    // ============================================================

    public static function initOrders(): void
    {
        if (session()->has('orders')) return;

        $freelancers = MockData::freelancers();
        $services = MockData::services();

        $initialOrders = [
            [
                'order_number' => 'SH-8492',
                'service' => $services[0], // Modern Laravel Website
                'freelancer' => $freelancers[0], // Dhruv Patil
                'client_name' => 'Sneha Kulkarni',
                'client_avatar' => 'https://ui-avatars.com/api/?name=Sneha+K&background=6D28D9&color=fff&size=64',
                'package_name' => 'Standard',
                'price' => 2499,
                'platform_fee' => 50,
                'total_amount' => 2549,
                'delivery_days' => 5,
                'revisions' => 3,
                'requirements' => 'Need a Laravel website for my college event management startup. Features needed: user signup, event listings, registration form.',
                'status' => 'In Progress',
                'created_at' => '2 days ago',
                'due_date' => 'In 3 days',
                'reviewed' => false,
                'timeline' => [
                    ['title' => 'Order Placed', 'completed' => true, 'time' => 'Aug 20, 2026 10:30 AM'],
                    ['title' => 'Accepted by Freelancer', 'completed' => true, 'time' => 'Aug 20, 2026 11:15 AM'],
                    ['title' => 'In Progress', 'completed' => true, 'time' => 'Aug 20, 2026 02:00 PM'],
                    ['title' => 'Work Submitted', 'completed' => false, 'time' => 'Pending submission'],
                    ['title' => 'Completed & Approved', 'completed' => false, 'time' => 'Pending review'],
                ]
            ],
            [
                'order_number' => 'SH-7310',
                'service' => $services[1], // Minimal Brand Identity
                'freelancer' => $freelancers[1], // Aarav Shah
                'client_name' => 'Dhruv Patil',
                'client_avatar' => 'https://ui-avatars.com/api/?name=Dhruv+Patil&background=6D28D9&color=fff&size=64',
                'package_name' => 'Basic',
                'price' => 699,
                'platform_fee' => 50,
                'total_amount' => 749,
                'delivery_days' => 2,
                'revisions' => 2,
                'requirements' => 'Logo design for a tech blog. Minimal, dark mode theme with clean typography.',
                'status' => 'Completed',
                'created_at' => '1 week ago',
                'due_date' => 'Delivered on time',
                'reviewed' => true,
                'timeline' => [
                    ['title' => 'Order Placed', 'completed' => true, 'time' => 'Aug 14, 2026 04:00 PM'],
                    ['title' => 'Accepted by Freelancer', 'completed' => true, 'time' => 'Aug 14, 2026 05:20 PM'],
                    ['title' => 'In Progress', 'completed' => true, 'time' => 'Aug 15, 2026 09:00 AM'],
                    ['title' => 'Work Submitted', 'completed' => true, 'time' => 'Aug 16, 2026 01:10 PM'],
                    ['title' => 'Completed & Approved', 'completed' => true, 'time' => 'Aug 16, 2026 03:45 PM'],
                ]
            ],
            [
                'order_number' => 'SH-6105',
                'service' => $services[2] ?? $services[0], // Professional Presentation Design
                'freelancer' => $freelancers[3] ?? $freelancers[0], // Mitali Patel
                'client_name' => 'Dhruv Patil',
                'client_avatar' => 'https://ui-avatars.com/api/?name=Dhruv+Patil&background=6D28D9&color=fff&size=64',
                'package_name' => 'Standard',
                'price' => 999,
                'platform_fee' => 50,
                'total_amount' => 1049,
                'delivery_days' => 2,
                'revisions' => 3,
                'requirements' => '15 slide pitch deck for my final year major project presentation.',
                'status' => 'Completed',
                'created_at' => '2 weeks ago',
                'due_date' => 'Delivered on time',
                'reviewed' => false,
                'timeline' => [
                    ['title' => 'Order Placed', 'completed' => true, 'time' => 'Aug 07, 2026 11:00 AM'],
                    ['title' => 'Accepted by Freelancer', 'completed' => true, 'time' => 'Aug 07, 2026 11:30 AM'],
                    ['title' => 'In Progress', 'completed' => true, 'time' => 'Aug 07, 2026 02:00 PM'],
                    ['title' => 'Work Submitted', 'completed' => true, 'time' => 'Aug 09, 2026 10:00 AM'],
                    ['title' => 'Completed & Approved', 'completed' => true, 'time' => 'Aug 09, 2026 11:15 AM'],
                ]
            ],
            [
                'order_number' => 'SH-5012',
                'service' => $services[3] ?? $services[1], // Python Automation Script
                'freelancer' => $freelancers[2] ?? $freelancers[1], // Rahul Mehta
                'client_name' => 'Dhruv Patil',
                'client_avatar' => 'https://ui-avatars.com/api/?name=Dhruv+Patil&background=6D28D9&color=fff&size=64',
                'package_name' => 'Basic',
                'price' => 699,
                'platform_fee' => 50,
                'total_amount' => 749,
                'delivery_days' => 2,
                'revisions' => 1,
                'requirements' => 'Simple PDF to Excel parser script.',
                'status' => 'Cancelled',
                'created_at' => '3 weeks ago',
                'due_date' => 'Cancelled',
                'reviewed' => false,
                'timeline' => [
                    ['title' => 'Order Placed', 'completed' => true, 'time' => 'Aug 01, 2026 09:00 AM'],
                    ['title' => 'Cancelled', 'completed' => true, 'time' => 'Aug 01, 2026 10:15 AM'],
                ]
            ]
        ];

        session(['orders' => $initialOrders]);
    }

    public static function orders(): array
    {
        self::initOrders();
        return session('orders', []);
    }

    public static function getOrder(string $orderNumber): ?array
    {
        $orders = self::orders();
        foreach ($orders as $order) {
            if ($order['order_number'] === $orderNumber) {
                return $order;
            }
        }
        return null;
    }

    public static function addOrder(array $newOrder): array
    {
        $orders = self::orders();
        array_unshift($orders, $newOrder);
        session(['orders' => $orders]);

        self::addNotification([
            'title' => 'New Order Received',
            'message' => 'Order ' . $newOrder['order_number'] . ' placed for ' . $newOrder['service']['title'],
            'url' => route('orders.show', $newOrder['order_number']),
            'icon' => 'bi-bag-check-fill',
            'time' => 'Just now',
        ]);

        return $newOrder;
    }

    public static function updateOrderStatus(string $orderNumber, string $newStatus): ?array
    {
        $orders = self::orders();
        $updatedOrder = null;

        foreach ($orders as &$order) {
            if ($order['order_number'] === $orderNumber) {
                $order['status'] = $newStatus;
                
                if ($newStatus === 'Accepted') {
                    $order['timeline'][1]['completed'] = true;
                    $order['timeline'][1]['time'] = date('M d, Y h:i A');
                } elseif ($newStatus === 'In Progress') {
                    $order['timeline'][1]['completed'] = true;
                    $order['timeline'][2]['completed'] = true;
                    $order['timeline'][2]['time'] = date('M d, Y h:i A');
                } elseif ($newStatus === 'Submitted') {
                    $order['timeline'][1]['completed'] = true;
                    $order['timeline'][2]['completed'] = true;
                    $order['timeline'][3]['completed'] = true;
                    $order['timeline'][3]['time'] = date('M d, Y h:i A');
                } elseif ($newStatus === 'Completed') {
                    foreach ($order['timeline'] as &$t) {
                        $t['completed'] = true;
                    }
                    $order['timeline'][4]['time'] = date('M d, Y h:i A');
                } elseif ($newStatus === 'Cancelled') {
                    $order['timeline'] = [
                        ['title' => 'Order Placed', 'completed' => true, 'time' => $order['created_at']],
                        ['title' => 'Cancelled', 'completed' => true, 'time' => date('M d, Y h:i A')],
                    ];
                }
                
                $updatedOrder = $order;
                break;
            }
        }

        if ($updatedOrder) {
            session(['orders' => $orders]);

            self::addNotification([
                'title' => 'Order Status Updated',
                'message' => 'Order ' . $orderNumber . ' is now ' . $newStatus,
                'url' => route('orders.show', $orderNumber),
                'icon' => 'bi-info-circle-fill',
                'time' => 'Just now',
            ]);
        }

        return $updatedOrder;
    }

    public static function markOrderReviewed(string $orderNumber): void
    {
        $orders = self::orders();
        foreach ($orders as &$order) {
            if ($order['order_number'] === $orderNumber) {
                $order['reviewed'] = true;
                break;
            }
        }
        session(['orders' => $orders]);
    }

    // ============================================================
    // SERVICES STORE
    // ============================================================

    public static function initServices(): void
    {
        if (session()->has('user_services')) return;
        session(['user_services' => MockData::defaultServices()]);
    }

    public static function services(): array
    {
        self::initServices();
        return session('user_services', MockData::defaultServices());
    }

    public static function addService(array $serviceData): array
    {
        $services = self::services();
        $slug = Str::slug($serviceData['title']);
        
        $newService = array_merge([
            'id' => rand(100, 999),
            'title' => $serviceData['title'],
            'slug' => $slug,
            'freelancer' => self::user() ?? self::defaultDemoUser(),
            'category' => $serviceData['category'] ?? 'Web Development',
            'category_slug' => Str::slug($serviceData['category'] ?? 'web-development'),
            'price' => (int) ($serviceData['packages']['basic']['price'] ?? 999),
            'rating' => 5.0,
            'reviews' => 0,
            'delivery_days' => (int) ($serviceData['packages']['basic']['delivery'] ?? 3),
            'image' => $serviceData['image'] ?? 'https://placehold.co/900x540/e8e0f5/6D28D9?text=New+Service',
            'gallery' => [$serviceData['image'] ?? 'https://placehold.co/900x540/e8e0f5/6D28D9?text=New+Service'],
            'tags' => $serviceData['tags'] ?? ['Service', 'Student'],
            'featured' => false,
            'description' => $serviceData['description'] ?? '',
            'what_you_get' => ['High quality deliverables', 'Source files included', 'Professional student support'],
            'technologies' => ['PHP', 'Laravel', 'HTML/CSS', 'Figma'],
            'packages' => $serviceData['packages'] ?? [
                'basic' => ['name' => 'Basic', 'price' => 499, 'delivery' => 2, 'revisions' => 1, 'features' => ['Standard delivery']],
                'standard' => ['name' => 'Standard', 'price' => 999, 'delivery' => 4, 'revisions' => 2, 'features' => ['Standard delivery', 'Source code']],
                'premium' => ['name' => 'Premium', 'price' => 1999, 'delivery' => 7, 'revisions' => 3, 'features' => ['Priority delivery', 'Full support']],
            ],
            'faq' => [
                ['q' => 'What is the delivery process?', 'a' => 'Deliverables will be sent through SkillHive.'],
            ]
        ], $serviceData);

        array_unshift($services, $newService);
        session(['user_services' => $services]);

        self::addNotification([
            'title' => 'Service Published',
            'message' => 'Your service "' . Str::limit($newService['title'], 30) . '" is live on the marketplace!',
            'url' => route('service.show', $newService['slug']),
            'icon' => 'bi-shop',
            'time' => 'Just now',
        ]);

        return $newService;
    }

    public static function updateService(int $id, array $data): ?array
    {
        $services = self::services();
        $updated = null;

        foreach ($services as &$s) {
            if ($s['id'] == $id) {
                if (isset($data['title'])) {
                    $s['title'] = $data['title'];
                    $s['slug'] = Str::slug($data['title']);
                }
                if (isset($data['category'])) {
                    $s['category'] = $data['category'];
                    $s['category_slug'] = Str::slug($data['category']);
                }
                if (isset($data['description'])) $s['description'] = $data['description'];
                if (isset($data['image'])) $s['image'] = $data['image'];
                if (isset($data['packages'])) {
                    $s['packages'] = $data['packages'];
                    $s['price'] = (int) ($data['packages']['basic']['price'] ?? $s['price']);
                }
                $updated = $s;
                break;
            }
        }

        if ($updated) {
            session(['user_services' => $services]);
        }

        return $updated;
    }

    public static function deleteService(int $id): bool
    {
        $services = self::services();
        $filtered = array_values(array_filter($services, fn($s) => $s['id'] != $id));
        session(['user_services' => $filtered]);
        return true;
    }

    // ============================================================
    // PORTFOLIO STORE
    // ============================================================

    public static function initPortfolio(): void
    {
        if (session()->has('user_portfolio')) return;
        session(['user_portfolio' => MockData::portfolioForFreelancer(1)]);
    }

    public static function portfolio(): array
    {
        self::initPortfolio();
        return session('user_portfolio', MockData::portfolioForFreelancer(1));
    }

    public static function addPortfolioProject(array $data): array
    {
        $portfolio = self::portfolio();
        $newItem = [
            'id' => rand(100, 999),
            'title' => $data['title'],
            'slug' => Str::slug($data['title']),
            'description' => $data['description'],
            'image' => $data['image'] ?? 'https://placehold.co/800x500/e8e0f5/6D28D9?text=Portfolio+Project',
            'technologies' => is_array($data['technologies']) ? $data['technologies'] : array_map('trim', explode(',', $data['technologies'])),
            'project_url' => $data['project_url'] ?? '#',
            'category' => $data['category'] ?? 'Web Development',
        ];

        array_unshift($portfolio, $newItem);
        session(['user_portfolio' => $portfolio]);
        return $newItem;
    }

    public static function updatePortfolioProject(int $id, array $data): ?array
    {
        $portfolio = self::portfolio();
        $updated = null;

        foreach ($portfolio as &$item) {
            if ($item['id'] == $id) {
                $item['title'] = $data['title'] ?? $item['title'];
                $item['slug'] = Str::slug($item['title']);
                $item['description'] = $data['description'] ?? $item['description'];
                $item['image'] = $data['image'] ?? $item['image'];
                $item['project_url'] = $data['project_url'] ?? $item['project_url'];
                if (isset($data['technologies'])) {
                    $item['technologies'] = is_array($data['technologies']) ? $data['technologies'] : array_map('trim', explode(',', $data['technologies']));
                }
                $updated = $item;
                break;
            }
        }

        if ($updated) {
            session(['user_portfolio' => $portfolio]);
        }

        return $updated;
    }

    public static function deletePortfolioProject(int $id): bool
    {
        $portfolio = self::portfolio();
        $filtered = array_values(array_filter($portfolio, fn($item) => $item['id'] != $id));
        session(['user_portfolio' => $filtered]);
        return true;
    }

    // ============================================================
    // CHAT / MESSAGES STORE
    // ============================================================

    public static function initMessages(): void
    {
        if (session()->has('user_conversations')) return;

        $conversations = [
            [
                'id' => 1,
                'name' => 'Aarav Shah',
                'avatar' => 'https://ui-avatars.com/api/?name=Aarav+Shah&background=0369a1&color=fff&size=64',
                'role' => 'Graphic Designer • NID Ahmedabad',
                'online' => true,
                'unread' => 1,
                'last_message' => 'I have uploaded the initial logo draft files for your review.',
                'last_time' => '10m ago',
                'messages' => [
                    ['sender' => 'them', 'text' => 'Hi Dhruv! I saw your order for the logo design concept.', 'time' => '10:15 AM'],
                    ['sender' => 'me', 'text' => 'Hey Aarav! Yes, looking for something clean and minimal with dark theme accents.', 'time' => '10:20 AM'],
                    ['sender' => 'them', 'text' => 'Got it! I will start working on 3 concepts today.', 'time' => '10:25 AM'],
                    ['sender' => 'them', 'text' => 'I have uploaded the initial logo draft files for your review.', 'time' => '10:45 AM'],
                ]
            ],
            [
                'id' => 2,
                'name' => 'Mitali Patel',
                'avatar' => 'https://ui-avatars.com/api/?name=Mitali+Patel&background=dc2626&color=fff&size=64',
                'role' => 'UI/UX Designer • Srishti',
                'online' => false,
                'unread' => 0,
                'last_message' => 'Thank you for approving the presentation slides!',
                'last_time' => '2h ago',
                'messages' => [
                    ['sender' => 'me', 'text' => 'Hi Mitali, loved the pitch deck design! Can you make slide 4 font slightly larger?', 'time' => 'Yesterday'],
                    ['sender' => 'them', 'text' => 'Sure Dhruv, updated slide 4 and uploaded the final PPT file.', 'time' => 'Yesterday'],
                    ['sender' => 'them', 'text' => 'Thank you for approving the presentation slides!', 'time' => '2h ago'],
                ]
            ],
            [
                'id' => 3,
                'name' => 'Rahul Mehta',
                'avatar' => 'https://ui-avatars.com/api/?name=Rahul+Mehta&background=059669&color=fff&size=64',
                'role' => 'Python Developer • IIT Bombay',
                'online' => true,
                'unread' => 0,
                'last_message' => 'Let me know if you need any adjustments to the Python script.',
                'last_time' => '1d ago',
                'messages' => [
                    ['sender' => 'them', 'text' => 'Hey, I finished setting up the pandas parsing script.', 'time' => 'Aug 21'],
                    ['sender' => 'them', 'text' => 'Let me know if you need any adjustments to the Python script.', 'time' => 'Aug 21'],
                ]
            ]
        ];

        session(['user_conversations' => $conversations]);
    }

    public static function conversations(): array
    {
        self::initMessages();
        return session('user_conversations', []);
    }

    public static function addChatMessage(int $conversationId, string $messageText): array
    {
        $conversations = self::conversations();
        $newMessage = ['sender' => 'me', 'text' => $messageText, 'time' => date('g:i A')];

        foreach ($conversations as &$conv) {
            if ($conv['id'] == $conversationId) {
                $conv['messages'][] = $newMessage;
                $conv['last_message'] = $messageText;
                $conv['last_time'] = 'Just now';
                $conv['unread'] = 0;
                break;
            }
        }

        session(['user_conversations' => $conversations]);
        return $newMessage;
    }

    // ============================================================
    // NOTIFICATIONS STORE
    // ============================================================

    public static function initNotifications(): void
    {
        // Database backed; no static mock needed.
    }

    public static function notifications(): array
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        if ($userId) {
            $dbNotifs = \App\Models\Notification::where('user_id', $userId)
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();

            return $dbNotifs->map(function ($n) {
                return [
                    'id'      => $n->id,
                    'title'   => $n->title,
                    'message' => $n->message,
                    'url'     => route('notifications.click', $n->id),
                    'icon'    => $n->icon ?: 'bi-bell-fill',
                    'time'    => $n->created_at ? $n->created_at->diffForHumans() : 'Just now',
                    'read'    => (bool) $n->is_read,
                ];
            })->toArray();
        }

        return [];
    }

    public static function unreadNotificationCount(): int
    {
        $userId = \Illuminate\Support\Facades\Auth::id();
        if ($userId) {
            return \App\Models\Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->count();
        }
        return 0;
    }

    public static function addNotification(array $n, ?int $userId = null): void
    {
        $targetUserId = $userId ?: (\Illuminate\Support\Facades\Auth::id() ?? 1);
        \App\Models\Notification::create([
            'user_id' => $targetUserId,
            'title'   => $n['title'] ?? 'Notification',
            'message' => $n['message'] ?? '',
            'url'     => $n['url'] ?? null,
            'icon'    => $n['icon'] ?? 'bi-bell-fill',
            'type'    => $n['type'] ?? 'general',
            'is_read' => false,
        ]);
    }

    // ============================================================
    // DEFAULT DEMO ACCOUNTS
    // ============================================================

    public static function defaultDemoUser(): array
    {
        return [
            'id' => 101,
            'name' => 'Dhruv Patil',
            'email' => 'dhruv.patil@mitpune.edu.in',
            'avatar' => 'https://ui-avatars.com/api/?name=Dhruv+Patil&background=6D28D9&color=fff&size=128',
            'college' => 'MIT Pune',
            'course' => 'B.Tech Computer Science',
            'bio' => 'Computer Science student specializing in full-stack web development. Passionate about building clean, scalable applications.',
            'skills' => ['Laravel', 'PHP', 'JavaScript', 'Bootstrap', 'MySQL'],
            'role' => 'client_seller',
        ];
    }

    public static function defaultDemoSeller(): array
    {
        return [
            'id' => 102,
            'name' => 'Mitali Patel',
            'email' => 'mitali.patel@srishti.edu.in',
            'avatar' => 'https://ui-avatars.com/api/?name=Mitali+Patel&background=dc2626&color=fff&size=128',
            'college' => 'Srishti Bangalore',
            'course' => 'B.Des UI/UX Design',
            'bio' => 'Design student passionate about creating intuitive user experiences and clean interfaces.',
            'skills' => ['Figma', 'Adobe XD', 'Prototyping', 'User Research'],
            'role' => 'client_seller',
        ];
    }

    public static function defaultDemoAdmin(): array
    {
        return [
            'id' => 999,
            'name' => 'SkillHive Admin',
            'email' => 'admin@skillhive.edu.in',
            'avatar' => 'https://ui-avatars.com/api/?name=Admin&background=111827&color=fff&size=128',
            'college' => 'SkillHive Platform',
            'course' => 'Platform Administrator',
            'bio' => 'System administrator managing SkillHive platform users, services, categories, orders, and content.',
            'skills' => ['Platform Moderation', 'Analytics', 'Management'],
            'role' => 'admin',
        ];
    }
}
