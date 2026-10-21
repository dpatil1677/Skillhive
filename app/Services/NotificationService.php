<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Order;
use App\Models\User;
use App\Models\Withdrawal;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Dispatch an in-app notification and email alert safely.
     */
    public static function send(User $user, string $title, string $message, ?string $url = null, string $icon = 'bi-bell-fill', string $type = 'general'): Notification
    {
        // 1. Create In-App Notification Record
        $notif = Notification::create([
            'user_id' => $user->id,
            'title'   => $title,
            'message' => $message,
            'url'     => $url,
            'icon'    => $icon,
            'type'    => $type,
            'is_read' => false,
        ]);

        // 2. Safely Dispatch Email (logged or sent via SMTP)
        try {
            if (!empty($user->email)) {
                $emailSubject = "SkillHive — {$title}";
                $emailBody = "Hello {$user->name},\n\n{$message}\n\n";
                if ($url) {
                    $emailBody .= "View details: {$url}\n\n";
                }
                $emailBody .= "Best regards,\nThe SkillHive Team\nhttps://skillhive.com";

                Mail::raw($emailBody, function ($mail) use ($user, $emailSubject) {
                    $mail->to($user->email)
                        ->subject($emailSubject);
                });
            }
        } catch (\Throwable $e) {
            Log::warning("NotificationService failed to send email to {$user->email}: " . $e->getMessage());
        }

        return $notif;
    }

    /**
     * Notify Freelancer when an order is placed.
     */
    public static function orderPlaced(Order $order): void
    {
        if ($order->freelancer) {
            self::send(
                $order->freelancer,
                "New Order Received (#{$order->order_number})",
                "Great news! {$order->client_name} placed an order for \"{$order->service?->title}\" (₹" . number_format($order->price) . "). Escrow payment is secured.",
                route('seller.orders.show', $order->order_number),
                'bi-bag-check-fill',
                'order'
            );
        }
    }

    /**
     * Notify Client when status updates.
     */
    public static function orderStatusUpdated(Order $order, string $status): void
    {
        $rawStatus = strtolower($status);
        $statusLabels = [
            'accepted'    => 'Order Accepted',
            'in_progress' => 'Order In Progress',
            'submitted'   => 'Deliverables Ready for Review',
            'completed'   => 'Order Completed & Approved',
            'cancelled'   => 'Order Cancelled',
        ];

        $title = $statusLabels[$rawStatus] ?? ("Order Status: " . ucfirst($status));

        if ($order->client) {
            self::send(
                $order->client,
                "{$title} (#{$order->order_number})",
                "Your order for \"{$order->service?->title}\" has been marked as {$status}.",
                route('orders.show', $order->order_number),
                'bi-clock-history',
                'order'
            );
        }

        // Send official invoice email when order is completed
        if ($rawStatus === 'completed') {
            self::sendInvoiceEmail($order);
        }
    }

    /**
     * Send official tax invoice email to the client upon order completion.
     */
    public static function sendInvoiceEmail(Order $order): void
    {
        $client = $order->client;
        if (!$client || empty($client->email)) {
            return;
        }

        // 1. Create in-app notification
        Notification::create([
            'user_id' => $client->id,
            'title'   => "Tax Invoice Issued (#{$order->order_number})",
            'message' => "Your tax invoice for Order #{$order->order_number} has been generated and sent to {$client->email}.",
            'url'     => route('orders.invoice', $order->order_number),
            'icon'    => 'bi-receipt-cutoff',
            'type'    => 'order',
            'is_read' => false,
        ]);

        // 2. Dispatch HTML Invoice Email
        try {
            Mail::to($client->email)->send(new \App\Mail\OrderInvoiceMail($order));
        } catch (\Throwable $e) {
            Log::warning("Failed to send invoice email for order #{$order->order_number} to {$client->email}: " . $e->getMessage());
        }
    }

    /**
     * Notify Freelancer when payout is processed or status changes.
     */
    public static function withdrawalStatusUpdated(Withdrawal $withdrawal): void
    {
        if ($withdrawal->user) {
            self::send(
                $withdrawal->user,
                "Withdrawal " . ucfirst($withdrawal->status) . " (#{$withdrawal->reference_id})",
                "Your payout request for ₹" . number_format($withdrawal->amount, 2) . " is now " . ucfirst($withdrawal->status) . ".",
                route('seller.payouts'),
                'bi-wallet2',
                'payout'
            );
        }
    }
}
