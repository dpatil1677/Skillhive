<?php

namespace App\Http\Controllers;

use App\Helpers\UserStore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Show profile edit form.
     */
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Update profile settings in database.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $isSeller = ($user->role === 'seller');

        $request->validate([
            'name' => 'required|string|max:255',
            'college' => $isSeller ? 'required|string|max:255' : 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'skills' => 'nullable|string|max:500',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $user->name = trim($request->input('name'));
        $user->college = trim($request->input('college'));
        $user->course = trim($request->input('course'));
        $user->bio = trim($request->input('bio'));

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->profile_image = '/storage/' . $path;
        }

        if ($request->filled('skills')) {
            $skillsArray = array_values(array_filter(array_map('trim', explode(',', $request->input('skills')))));
            $user->skills = $skillsArray;
        } else {
            $user->skills = [];
        }

        $user->save();

        // Keep UserStore session cache in sync
        UserStore::updateProfile([
            'name' => $user->name,
            'college' => $user->college,
            'course' => $user->course,
            'bio' => $user->bio,
            'skills' => $user->skills,
            'profile_image' => $user->profile_image,
        ]);

        return redirect()->route('profile.edit')->with('toast_success', 'Profile settings updated successfully!');
    }

    /**
     * Remove custom profile avatar and reset to default initials.
     */
    public function removeAvatar()
    {
        $user = Auth::user();
        $user->profile_image = null;
        $user->save();

        UserStore::updateProfile(['profile_image' => null]);

        return redirect()->route('profile.edit')->with('toast_success', 'Profile photo removed. Using default avatar.');
    }

    /**
     * Upgrade Buyer account to Freelancer / Seller account.
     */
    public function becomeSeller(Request $request)
    {
        $request->validate([
            'college' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'skills' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $user->role = 'seller';
        $user->college = trim($request->input('college'));
        $user->course = trim($request->input('course'));
        if ($request->filled('bio')) {
            $user->bio = trim($request->input('bio'));
        }
        if ($request->filled('skills')) {
            $user->skills = array_values(array_filter(array_map('trim', explode(',', $request->input('skills')))));
        }
        $user->save();

        UserStore::updateProfile([
            'role' => 'seller',
            'college' => $user->college,
            'course' => $user->course,
            'bio' => $user->bio,
            'skills' => $user->skills,
        ]);

        return redirect()->route('seller.dashboard')->with('toast_success', '🎉 Welcome to SkillHive Freelancer! Your seller account is active.');
    }

    /**
     * Switch view mode between Buyer and Freelancer.
     */
    public function switchRole(Request $request)
    {
        $user = Auth::user();
        $targetRole = $request->input('role');

        if (!in_array($targetRole, ['user', 'seller'])) {
            return back()->with('toast_error', 'Invalid role requested.');
        }

        if ($targetRole === 'seller' && empty($user->college)) {
            return redirect()->route('profile.edit')->with('toast_error', 'Please enter your college name in your profile before switching to Freelancer mode.');
        }

        $user->role = $targetRole;
        $user->save();

        UserStore::updateProfile(['role' => $targetRole]);

        $dest = ($targetRole === 'seller') ? route('seller.dashboard') : route('dashboard');
        return redirect($dest)->with('toast_success', 'Switched mode to ' . ($targetRole === 'seller' ? 'Freelancer' : 'Client / Buyer'));
    }
}
