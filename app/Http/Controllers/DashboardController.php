<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Display personal user / client dashboard with database records.
     */
    public function index()
    {
        $user = Auth::user();
        $userId = Auth::id();

        $allOrders = Order::with(['service', 'freelancer'])
            ->where('client_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics from DB
        $activeOrdersCount = $allOrders->filter(fn($o) => in_array(strtolower($o->status), ['pending', 'accepted', 'in_progress', 'in progress', 'submitted']))->count();
        $completedOrdersCount = $allOrders->filter(fn($o) => strtolower($o->status) === 'completed')->count();
        $totalSpent = (float) $allOrders->filter(fn($o) => strtolower($o->status) === 'completed')->sum('total_amount');

        // Recent orders (up to 3)
        $recentOrders = $allOrders->take(3);

        // Recent messages from real database records
        $dbMessages = Message::with(['sender'])
            ->where('receiver_id', $userId)
            ->latest()
            ->take(3)
            ->get();

        $recentMessages = $dbMessages->map(function ($m) {
            return [
                'sender_id' => $m->sender_id,
                'sender_name' => $m->sender ? $m->sender->name : 'SkillHive Member',
                'sender_avatar' => $m->sender ? $m->sender->avatar : 'https://ui-avatars.com/api/?name=Member&background=6D28D9&color=fff&size=64',
                'message' => $m->message,
                'time' => $m->created_at ? $m->created_at->diffForHumans() : 'Recently',
                'unread' => !$m->is_read,
            ];
        })->toArray();

        // Recommended active services from MySQL database
        $recommendedServices = Service::with(['user', 'category'])
            ->where('status', 'active')
            ->orderBy('rating', 'desc')
            ->take(4)
            ->get();

        return view('dashboard', [
            'user' => $user,
            'activeOrdersCount' => $activeOrdersCount,
            'completedOrdersCount' => $completedOrdersCount,
            'totalSpent' => $totalSpent,
            'avgRating' => 4.9,
            'recentOrders' => $recentOrders,
            'recentMessages' => $recentMessages,
            'recommendedServices' => $recommendedServices,
        ]);
    }
}
