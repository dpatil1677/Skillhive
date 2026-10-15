<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Mark a specific notification as read and redirect to its destination URL.
     */
    public function handleClick(int $id)
    {
        $userId = Auth::id();
        $notif = Notification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($notif) {
            $notif->update(['is_read' => true]);
            if (!empty($notif->url) && $notif->url !== '#') {
                return redirect($notif->url);
            }
        }

        return redirect()->back();
    }

    /**
     * Mark all notifications for the authenticated user as read.
     */
    public function markAllRead(Request $request)
    {
        $userId = Auth::id();
        if ($userId) {
            Notification::where('user_id', $userId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('toast_success', 'All notifications marked as read.');
    }

    /**
     * AJAX endpoint to mark a single notification as read.
     */
    public function markRead(int $id, Request $request)
    {
        $userId = Auth::id();
        $notif = Notification::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if ($notif) {
            $notif->update(['is_read' => true]);
            return response()->json(['success' => true]);
        }

        return response()->json(['error' => 'Notification not found'], 404);
    }
}
