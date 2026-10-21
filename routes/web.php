<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController;

// ================================================================
// PUBLIC / GUEST ROUTES (no auth required)
// ================================================================
Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/explore', [PageController::class, 'explore'])->name('explore');
Route::get('/category/{slug}', [PageController::class, 'category'])->name('category');
Route::get('/how-it-works', [PageController::class, 'howItWorks'])->name('how-it-works');
Route::get('/about', [PageController::class, 'about'])->name('about');

// Marketplace detail pages (public — guests can view)
Route::get('/service/{slug}', [PageController::class, 'serviceDetail'])->name('service.show');
Route::get('/freelancer/{slug}', [PageController::class, 'freelancerProfile'])->name('freelancer.show');
Route::get('/freelancer/{freelancerSlug}/portfolio/{portfolioSlug}', [PageController::class, 'portfolioDetail'])->name('portfolio.show');

// ================================================================
// AUTH ROUTES (guests only: redirect logged-in users away)
// ================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
});

// Auth actions (no guest middleware — demo login & logout need to work regardless)
Route::post('/login', [AuthController::class, 'processLogin'])->name('login.process');
Route::get('/login/demo', [AuthController::class, 'demoLogin'])->name('login.demo');
Route::post('/register', [AuthController::class, 'processRegister'])->name('register.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// AJAX helpers
Route::get('/api/check-email', [AuthController::class, 'checkEmailAvailability'])->name('api.check-email');

// ================================================================
// AUTHENTICATED USER ROUTES (login required)
// ================================================================
Route::middleware('auth')->group(function () {

    // Checkout & booking (auth required to place orders)
    Route::get('/checkout/{serviceSlug}', [BookingController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [BookingController::class, 'processCheckout'])->name('checkout.process');

    // Client / Buyer Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{orderNumber}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{orderNumber}/submit-work', [OrderController::class, 'submitWork'])->name('orders.submit-work');

    // Profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/avatar/remove', [ProfileController::class, 'removeAvatar'])->name('profile.avatar.remove');
    Route::post('/profile/become-seller', [ProfileController::class, 'becomeSeller'])->name('profile.become-seller');
    Route::post('/profile/switch-role', [ProfileController::class, 'switchRole'])->name('profile.switch-role');

    // Orders Cancellation & Invoice
    Route::post('/orders/{orderNumber}/cancel', [OrderController::class, 'cancelOrder'])->name('orders.cancel');
    Route::get('/orders/{orderNumber}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

    // Seller / Freelancer Suite
    Route::prefix('seller')->name('seller.')->middleware('seller')->group(function () {
        Route::get('/dashboard', [SellerController::class, 'dashboard'])->name('dashboard');
        Route::get('/services', [SellerController::class, 'services'])->name('services');
        Route::get('/services/create', [SellerController::class, 'createService'])->name('services.create');
        Route::post('/services/create', [SellerController::class, 'storeService'])->name('services.store');
        Route::get('/services/{id}/edit', [SellerController::class, 'editService'])->name('services.edit');
        Route::post('/services/{id}/edit', [SellerController::class, 'updateService'])->name('services.update');
        Route::post('/services/{id}/delete', [SellerController::class, 'destroyService'])->name('services.destroy');
        Route::get('/orders', [SellerController::class, 'sellerOrders'])->name('orders');
        Route::get('/orders/{orderNumber}', [SellerController::class, 'showOrder'])->name('orders.show');

        // Payouts & Earnings
        Route::get('/payouts', [SellerController::class, 'payouts'])->name('payouts');
        Route::post('/payouts/request', [SellerController::class, 'requestPayout'])->name('payouts.request');

        // Portfolio Manager
        Route::get('/portfolio', [PortfolioController::class, 'index'])->name('portfolio');
        Route::post('/portfolio', [PortfolioController::class, 'store'])->name('portfolio.store');
        Route::post('/portfolio/{id}/update', [PortfolioController::class, 'update'])->name('portfolio.update');
        Route::post('/portfolio/{id}/delete', [PortfolioController::class, 'destroy'])->name('portfolio.destroy');
    });

    // Chat / Messages & Video Call
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/messages', [ChatController::class, 'messages'])->name('chat.messages');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/chat/poll', [ChatController::class, 'poll'])->name('chat.poll');
    Route::post('/chat/meeting/start', [ChatController::class, 'startMeeting'])->name('chat.meeting.start');

    // Reviews
    Route::post('/reviews/submit', [ReviewController::class, 'store'])->name('reviews.store');

    // Favorites
    Route::post('/favorites/toggle', [\App\Http\Controllers\FavoriteController::class, 'toggle'])->name('favorites.toggle');

    // Notifications
    Route::get('/notifications/{id}/click', [\App\Http\Controllers\NotificationController::class, 'handleClick'])->name('notifications.click');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.mark-read');
});

// ================================================================
// ADMIN ROUTES (auth + admin role required)
// ================================================================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Users CRUD & Impersonation
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::post('/users/{id}/update', [AdminController::class, 'updateUser'])->name('users.update');
    Route::post('/users/{id}/delete', [AdminController::class, 'deleteUser'])->name('users.delete');
    Route::post('/users/{id}/status', [AdminController::class, 'updateUserStatus'])->name('users.status');
    Route::get('/users/{id}/impersonate', [AdminController::class, 'impersonateUser'])->name('users.impersonate');

    // Services CRUD
    Route::get('/services', [AdminController::class, 'services'])->name('services');
    Route::post('/services', [AdminController::class, 'storeService'])->name('services.store');
    Route::post('/services/{id}/update', [AdminController::class, 'updateService'])->name('services.update');
    Route::post('/services/{id}/delete', [AdminController::class, 'deleteService'])->name('services.delete');

    // Orders CRUD
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::post('/orders', [AdminController::class, 'storeOrder'])->name('orders.store');
    Route::post('/orders/{orderNumber}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.status');
    Route::post('/orders/{orderNumber}/delete', [AdminController::class, 'deleteOrder'])->name('orders.delete');

    // Categories CRUD
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::post('/categories/{id}/update', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::post('/categories/{id}/delete', [AdminController::class, 'deleteCategory'])->name('categories.delete');

    // Reviews CRUD
    Route::get('/reviews', [AdminController::class, 'reviews'])->name('reviews');
    Route::post('/reviews', [AdminController::class, 'storeReview'])->name('reviews.store');
    Route::post('/reviews/{id}/update', [AdminController::class, 'updateReview'])->name('reviews.update');
    Route::post('/reviews/{id}/delete', [AdminController::class, 'deleteReview'])->name('reviews.delete');

    // Withdrawals Management
    Route::get('/withdrawals', [AdminController::class, 'withdrawals'])->name('withdrawals');
    Route::post('/withdrawals/{id}/status', [AdminController::class, 'updateWithdrawalStatus'])->name('withdrawals.status');
});

// Impersonation return route (accessible to any authenticated user who has admin_impersonator_id)
Route::middleware('auth')->get('/admin/stop-impersonating', [AdminController::class, 'stopImpersonating'])->name('admin.stop-impersonating');
