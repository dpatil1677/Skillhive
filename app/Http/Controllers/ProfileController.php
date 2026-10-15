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
        $request->validate([
            'name' => 'required|string|max:255',
            'college' => 'required|string|max:255',
            'course' => 'nullable|string|max:255',
            'bio' => 'nullable|string|max:1000',
            'skills' => 'nullable|string|max:500',
        ]);

        $user = Auth::user();
        $user->name = trim($request->input('name'));
        $user->college = trim($request->input('college'));
        $user->course = trim($request->input('course'));
        $user->bio = trim($request->input('bio'));

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
        ]);

        return redirect()->route('profile.edit')->with('toast_success', 'Profile settings updated successfully!');
    }
}
