<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Toggle favorite status for a service.
     */
    public function toggle(Request $request)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Unauthenticated',
                'requires_auth' => true,
                'message' => 'Please sign in to save favorites.'
            ], 401);
        }

        $request->validate([
            'service_id' => 'required|integer|exists:services,id',
        ]);

        $userId = Auth::id();
        $serviceId = (int) $request->input('service_id');

        $favorite = Favorite::where('user_id', $userId)
            ->where('service_id', $serviceId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'status' => 'success',
                'favorited' => false,
                'message' => 'Removed from favorites'
            ]);
        }

        Favorite::create([
            'user_id' => $userId,
            'service_id' => $serviceId,
        ]);

        return response()->json([
            'status' => 'success',
            'favorited' => true,
            'message' => 'Saved to favorites'
        ]);
    }
}
