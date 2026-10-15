<?php

namespace App\Http\Controllers;

use App\Helpers\MockData;
use App\Models\Category;
use App\Models\Service;
use App\Models\User;
use App\Models\Portfolio;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    /**
     * Homepage.
     */
    public function home(Request $request)
    {

        $categories = Category::all();
        $services = Service::with(['user', 'category'])
            ->where('status', 'active')
            ->orderBy('rating', 'desc')
            ->take(8)
            ->get();

        $freelancers = User::whereIn('role', ['user', 'seller'])
            ->has('services')
            ->take(6)
            ->get()
            ->map(fn($u) => $u->toFreelancerArray());

        return view('home', [
            'categories' => $categories,
            'services' => $services,
            'freelancers' => $freelancers,
            'testimonials' => MockData::testimonials(),
        ]);
    }

    /**
     * Explore / marketplace browse page.
     */
    public function explore(Request $request)
    {
        $query = Service::with(['user', 'category'])
            ->where('status', 'active');

        // Search query (term)
        if ($request->filled('q')) {
            $query->search($request->query('q'));
        }

        // Category filter
        if ($request->filled('category')) {
            $categoriesParam = $request->query('category');
            $query->byCategory($categoriesParam);
        }

        // Price range filter
        if ($request->filled('min_price') || $request->filled('max_price')) {
            $query->byPrice($request->query('min_price'), $request->query('max_price'));
        }

        // Rating filter
        if ($request->filled('rating')) {
            $query->byRating($request->query('rating'));
        }

        // Delivery filter
        if ($request->filled('delivery')) {
            $query->byDelivery($request->query('delivery'));
        }

        // Sorting
        if ($request->filled('sort')) {
            $query->sortBy($request->query('sort'));
        } else {
            $query->orderBy('rating', 'desc')->orderBy('id', 'asc');
        }

        $services = $query->get();
        $categories = Category::all();

        return view('explore', [
            'services' => $services,
            'categories' => $categories,
            'query' => $request->query('q', ''),
        ]);
    }

    /**
     * Category page.
     */
    public function category(string $slug)
    {
        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            abort(404);
        }

        $services = Service::with(['user', 'category'])
            ->where('category_id', $category->id)
            ->where('status', 'active')
            ->orderBy('rating', 'desc')
            ->get();

        $freelancerUsers = User::whereIn('role', ['user', 'seller'])
            ->whereHas('services', fn($q) => $q->where('category_id', $category->id))
            ->get();

        $freelancers = $freelancerUsers->map(fn($u) => $u->toFreelancerArray());
        $categories = Category::all();

        return view('category', [
            'category' => $category,
            'services' => $services,
            'freelancers' => $freelancers,
            'categories' => $categories,
        ]);
    }

    /**
     * Service Detail Page.
     */
    public function serviceDetail(string $slug)
    {
        $service = Service::with(['user', 'category'])
            ->where('slug', $slug)
            ->first();

        if (!$service) {
            abort(404);
        }

        $dbReviews = Review::with(['user', 'order'])
            ->where('freelancer_id', $service->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        $reviews = $dbReviews->map(function ($r) {
            return [
                'id' => $r->id,
                'user_name' => $r->user ? $r->user->name : 'Verified Buyer',
                'user_avatar' => $r->user ? $r->user->avatar : 'https://ui-avatars.com/api/?name=Buyer&background=6D28D9&color=fff&size=64',
                'rating' => $r->rating,
                'comment' => $r->comment,
                'date' => $r->created_at ? $r->created_at->diffForHumans() : 'Recently',
                'order_number' => $r->order ? $r->order->order_number : 'SH-1001',
            ];
        });

        if ($reviews->isEmpty()) {
            $reviews = collect(MockData::reviewsForFreelancer($service->user_id));
        }

        $ratingSummary = MockData::ratingSummary($reviews->toArray());

        $relatedServices = Service::with(['user', 'category'])
            ->where('category_id', $service->category_id)
            ->where('id', '!=', $service->id)
            ->where('status', 'active')
            ->take(4)
            ->get();

        return view('service-detail', [
            'service' => $service,
            'reviews' => $reviews,
            'ratingSummary' => $ratingSummary,
            'relatedServices' => $relatedServices,
        ]);
    }

    /**
     * Freelancer Profile Page.
     */
    public function freelancerProfile(string $slug)
    {
        $users = User::whereIn('role', ['user', 'seller'])->get();
        $user = $users->first(fn($u) => Str::slug($u->name) === $slug || (string)$u->id === $slug);

        if (!$user) {
            abort(404);
        }

        $freelancer = $user->toFreelancerArray();

        $services = Service::with(['user', 'category'])
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->get();

        $portfolios = Portfolio::where('user_id', $user->id)->get();
        $freelancer['portfolio'] = $portfolios->toArray();

        $dbReviews = Review::with(['user', 'order'])
            ->where('freelancer_id', $user->id)
            ->get();

        $reviews = $dbReviews->map(function ($r) {
            return [
                'id' => $r->id,
                'name' => $r->user ? $r->user->name : 'Verified Buyer',
                'user_name' => $r->user ? $r->user->name : 'Verified Buyer',
                'avatar' => $r->user ? $r->user->avatar : 'https://ui-avatars.com/api/?name=Buyer&background=6D28D9&color=fff&size=64',
                'user_avatar' => $r->user ? $r->user->avatar : 'https://ui-avatars.com/api/?name=Buyer&background=6D28D9&color=fff&size=64',
                'rating' => $r->rating,
                'text' => $r->comment,
                'comment' => $r->comment,
                'date' => $r->created_at ? $r->created_at->diffForHumans() : 'Recently',
                'service' => $r->order && $r->order->service ? $r->order->service->title : 'SkillHive Service',
                'verified' => true,
            ];
        });

        if ($reviews->isEmpty()) {
            $reviews = collect(MockData::reviewsForFreelancer($user->id));
        }

        $ratingSummary = MockData::ratingSummary($reviews->toArray());

        return view('freelancer-profile', [
            'freelancer' => $freelancer,
            'services' => $services,
            'reviews' => $reviews,
            'ratingSummary' => $ratingSummary,
        ]);
    }

    /**
     * Portfolio Detail Page.
     */
    public function portfolioDetail(string $freelancerSlug, string $portfolioSlug)
    {
        $users = User::whereIn('role', ['user', 'seller'])->get();
        $user = $users->first(fn($u) => Str::slug($u->name) === $freelancerSlug || (string)$u->id === $freelancerSlug);

        if (!$user) {
            abort(404);
        }

        $freelancer = $user->toFreelancerArray();

        $portfolioItem = Portfolio::where('user_id', $user->id)
            ->where('slug', $portfolioSlug)
            ->first();

        if (!$portfolioItem) {
            abort(404);
        }

        $otherPortfolios = Portfolio::where('user_id', $user->id)
            ->where('id', '!=', $portfolioItem->id)
            ->get();

        return view('portfolio-detail', [
            'freelancer' => $freelancer,
            'portfolio' => $portfolioItem,
            'otherPortfolios' => $otherPortfolios,
        ]);
    }

    /**
     * How It Works page.
     */
    public function howItWorks()
    {
        return view('how-it-works');
    }

    /**
     * About page.
     */
    public function about()
    {
        return view('about');
    }
}
