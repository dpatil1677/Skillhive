<?php

namespace App\Http\Controllers;

use App\Helpers\UserStore;
use App\Models\Portfolio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PortfolioController extends Controller
{
    /**
     * Display Portfolio Manager page for seller.
     */
    public function index()
    {
        $user = Auth::user();
        $portfolio = Portfolio::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('seller.portfolio', [
            'user' => $user,
            'portfolio' => $portfolio,
        ]);
    }

    /**
     * Add new project to portfolio in database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'technologies' => 'required|string',
        ]);

        $user = Auth::user();
        $title = trim($request->input('title'));
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;
        while (Portfolio::where('user_id', $user->id)->where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $techs = array_values(array_filter(array_map('trim', explode(',', $request->input('technologies')))));
        $image = $request->input('image_url') ?: 'https://placehold.co/800x500/e8e0f5/6D28D9?text=' . urlencode(substr($title, 0, 25));

        $project = Portfolio::create([
            'user_id' => $user->id,
            'title' => $title,
            'slug' => $slug,
            'description' => $request->input('description'),
            'technologies' => $techs,
            'image' => $image,
            'project_url' => $request->input('project_url', '#'),
        ]);

        // Keep UserStore session in sync
        UserStore::addPortfolioProject([
            'id' => $project->id,
            'title' => $title,
            'slug' => $slug,
            'description' => $request->input('description'),
            'technologies' => $techs,
            'image' => $image,
            'project_url' => $request->input('project_url', '#'),
        ]);

        return redirect()->route('seller.portfolio')
            ->with('toast_success', 'Portfolio project "' . $title . '" published successfully!');
    }

    /**
     * Update portfolio project in database.
     */
    public function update(int $id, Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
        ]);

        $user = Auth::user();
        $project = Portfolio::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        $techs = array_values(array_filter(array_map('trim', explode(',', $request->input('technologies', '')))));

        $project->title = trim($request->input('title'));
        $project->description = $request->input('description');
        $project->technologies = $techs;
        if ($request->filled('image_url')) {
            $project->image = $request->input('image_url');
        }
        if ($request->filled('project_url')) {
            $project->project_url = $request->input('project_url');
        }
        $project->save();

        UserStore::updatePortfolioProject($id, [
            'title' => $project->title,
            'description' => $project->description,
            'technologies' => $project->technologies,
            'image' => $project->image,
            'project_url' => $project->project_url,
        ]);

        return redirect()->route('seller.portfolio')
            ->with('toast_success', 'Portfolio project updated successfully!');
    }

    /**
     * Delete portfolio project from database.
     */
    public function destroy(int $id)
    {
        $user = Auth::user();
        Portfolio::where('id', $id)->where('user_id', $user->id)->delete();
        UserStore::deletePortfolioProject($id);

        return redirect()->route('seller.portfolio')
            ->with('toast_success', 'Portfolio project deleted.');
    }
}
