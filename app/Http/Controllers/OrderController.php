<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display My Orders page for logged-in buyer/client.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();
        $tab    = strtolower($request->query('tab', 'all'));

        $allOrders = Order::with(['service.category', 'freelancer', 'client'])
            ->where('client_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        $filteredOrders = match ($tab) {
            'active'    => $allOrders->filter(fn($o) => in_array($o->raw_status, ['pending', 'accepted', 'in_progress', 'submitted'])),
            'completed' => $allOrders->filter(fn($o) => $o->raw_status === 'completed'),
            'cancelled' => $allOrders->filter(fn($o) => $o->raw_status === 'cancelled'),
            default     => $allOrders,
        };

        $counts = [
            'all'       => $allOrders->count(),
            'active'    => $allOrders->filter(fn($o) => in_array($o->raw_status, ['pending', 'accepted', 'in_progress', 'submitted']))->count(),
            'completed' => $allOrders->filter(fn($o) => $o->raw_status === 'completed')->count(),
            'cancelled' => $allOrders->filter(fn($o) => $o->raw_status === 'cancelled')->count(),
        ];

        return view('orders.index', [
            'orders'    => $filteredOrders,
            'activeTab' => $tab,
            'counts'    => $counts,
        ]);
    }

    /**
     * Display Order Details page (client/buyer perspective).
     * Freelancers viewing orders they are fulfilling get redirected to seller view.
     */
    public function show(string $orderNumber)
    {
        $order    = Order::with(['service.category', 'freelancer', 'client', 'review'])
            ->where('order_number', $orderNumber)
            ->first();

        if (!$order) {
            abort(404);
        }

        $userId   = Auth::id();
        $authUser = Auth::user();

        // Must be client, freelancer, or admin for this order
        if ($userId && $order->client_id !== $userId && $order->freelancer_id !== $userId && $authUser->role !== 'admin') {
            abort(403, 'Unauthorized access to order details.');
        }

        // Redirect sellers/freelancers to their dedicated order-management view
        if ($authUser && in_array($authUser->role, ['seller', 'freelancer']) && $order->freelancer_id === $userId) {
            return redirect()->route('seller.orders.show', $orderNumber);
        }

        return view('orders.show', ['order' => $order]);
    }

    /**
     * Update order status — shared by both client and freelancer.
     */
    public function updateStatus(string $orderNumber, Request $request)
    {
        $order    = Order::where('order_number', $orderNumber)->firstOrFail();
        $userId   = Auth::id();
        $authUser = Auth::user();

        if ($userId && $order->client_id !== $userId && $order->freelancer_id !== $userId && $authUser->role !== 'admin') {
            abort(403, 'Unauthorized to modify this order.');
        }

        $statusMap = [
            'pending'     => 'pending',    'Pending'     => 'pending',
            'accepted'    => 'accepted',   'Accepted'    => 'accepted',
            'in_progress' => 'in_progress','In Progress' => 'in_progress',
            'submitted'   => 'submitted',  'Submitted'   => 'submitted',
            'completed'   => 'completed',  'Completed'   => 'completed',
            'cancelled'   => 'cancelled',  'Cancelled'   => 'cancelled',
        ];

        $newStatus = $request->input('status');
        if (!isset($statusMap[$newStatus])) {
            return back()->with('toast_error', 'Invalid status value.');
        }

        $mappedStatus = $statusMap[$newStatus];
        $order->status = $mappedStatus;
        $order->save();

        // Optional notification message in chat
        $notifRecipient = ($userId === $order->client_id) ? $order->freelancer_id : $order->client_id;
        if ($notifRecipient) {
            $msgText = match ($mappedStatus) {
                'accepted'    => "I have accepted your order #{$order->order_number} and will start working shortly!",
                'in_progress' => "Order #{$order->order_number} is now in progress.",
                'completed'   => "Order #{$order->order_number} has been approved and completed. Funds released!",
                'cancelled'   => "Order #{$order->order_number} has been cancelled.",
                default       => null,
            };
            if ($msgText) {
                \App\Models\Message::create([
                    'sender_id'   => $userId,
                    'receiver_id' => $notifRecipient,
                    'message'     => $msgText,
                    'is_read'     => false,
                ]);
            }
        }

        NotificationService::orderStatusUpdated($order, $mappedStatus);

        return back()->with('toast_success', 'Order status updated to "' . $order->status . '".');
    }

    /**
     * Submit completed work deliverables (Freelancer action).
     */
    public function submitWork(string $orderNumber, Request $request)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $userId = Auth::id();

        if ($order->freelancer_id !== $userId && Auth::user()->role !== 'admin') {
            abort(403, 'Only the designated freelancer can submit deliverables for this order.');
        }

        $request->validate([
            'deliverables_note' => 'required|string|min:5|max:3000',
            'deliverables_link' => 'nullable|url|max:255',
            'deliverables_file' => 'nullable|file|max:25600', // max 25MB
        ]);

        $order->deliverables_note = trim($request->input('deliverables_note'));
        $order->deliverables_link = trim($request->input('deliverables_link', ''));

        if ($request->hasFile('deliverables_file')) {
            $path = $request->file('deliverables_file')->store('deliverables', 'public');
            $order->deliverables_file = '/storage/' . $path;
        }

        $order->status = 'submitted';
        $order->submitted_at = now();
        $order->save();

        // Send chat notification to client
        \App\Models\Message::create([
            'sender_id'   => $userId,
            'receiver_id' => $order->client_id,
            'message'     => "🚀 I have submitted the deliverables for Order #{$order->order_number}! Please review the files and notes on your order page and approve.",
            'is_read'     => false,
        ]);

        NotificationService::orderStatusUpdated($order, 'submitted');

        return back()->with('toast_success', 'Deliverables submitted successfully! The client has been notified to review and approve.');
    }

    /**
     * Cancel an order (Buyer or Admin action).
     */
    public function cancelOrder(string $orderNumber, Request $request)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();
        $userId = Auth::id();
        $authUser = Auth::user();

        if ($userId && $order->client_id !== $userId && $authUser->role !== 'admin') {
            abort(403, 'Only the client who placed this order can cancel it.');
        }

        if (in_array(strtolower($order->raw_status), ['completed', 'cancelled'])) {
            return back()->with('toast_error', 'This order cannot be cancelled as it is already ' . $order->status . '.');
        }

        $request->validate([
            'cancellation_reason' => 'required|string|min:5|max:1000',
        ]);

        $reason = trim($request->input('cancellation_reason'));

        $order->status = 'cancelled';
        $order->cancellation_reason = $reason;
        $order->cancelled_by = ($authUser->role === 'admin') ? 'admin' : 'client';
        $order->save();

        // Chat notification
        \App\Models\Message::create([
            'sender_id' => $userId,
            'receiver_id' => $order->freelancer_id,
            'message' => "⚠️ Order #{$order->order_number} has been cancelled by the client.\nReason: {$reason}\nEscrow payment of ₹" . number_format($order->total_amount) . " has been refunded.",
            'is_read' => false,
        ]);

        // In-app Notification for Freelancer
        \App\Models\Notification::create([
            'user_id' => $order->freelancer_id,
            'title' => 'Order Cancelled',
            'message' => "Order #{$order->order_number} was cancelled by client: \"{$reason}\"",
            'url' => route('seller.orders.show', $order->order_number),
            'icon' => 'bi-x-circle-fill',
            'type' => 'order',
        ]);

        return back()->with('toast_success', 'Order #' . $order->order_number . ' has been cancelled. Escrow payment (₹' . number_format($order->total_amount) . ') has been refunded.');
    }

    /**
     * Display printable / PDF tax invoice.
     */
    public function invoice(string $orderNumber)
    {
        $order = Order::with(['service.category', 'freelancer', 'client'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $userId = Auth::id();
        $authUser = Auth::user();

        if ($userId && $order->client_id !== $userId && $order->freelancer_id !== $userId && $authUser->role !== 'admin') {
            abort(403, 'Unauthorized to view this invoice.');
        }

        return view('orders.invoice', [
            'order' => $order,
        ]);
    }
}
