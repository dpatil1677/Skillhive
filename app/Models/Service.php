<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'slug',
        'description',
        'image',
        'price',
        'delivery_days',
        'revisions',
        'status',
        'rating',
        'reviews_count',
        'packages',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'rating' => 'decimal:2',
        'packages' => 'array',
    ];

    protected $appends = [
        'freelancer',
        'category_slug',
        'category_name',
        'reviews',
    ];

    public function getFreelancerAttribute()
    {
        if ($this->relationLoaded('user') && $this->user) {
            return $this->user->toFreelancerArray();
        }
        $u = $this->user;
        return $u ? $u->toFreelancerArray() : [
            'id' => 0,
            'name' => 'SkillHive Student',
            'slug' => 'student',
            'avatar' => 'https://ui-avatars.com/api/?name=Student&background=6D28D9&color=fff&size=128',
            'college' => 'SkillHive Member',
            'course' => 'Student Member',
            'skill' => 'Student Freelancer',
            'bio' => '',
            'skills' => [],
            'verified' => true,
            'rating' => 4.9,
            'reviews_count' => 0,
        ];
    }

    public function getCategorySlugAttribute(): string
    {
        if ($this->relationLoaded('category') && $this->category) {
            return $this->category->slug;
        }
        return $this->category ? $this->category->slug : 'web-development';
    }

    public function getCategoryNameAttribute(): string
    {
        if ($this->relationLoaded('category') && $this->category) {
            return $this->category->name;
        }
        return $this->category ? $this->category->name : 'General';
    }

    public function getReviewsAttribute(): int
    {
        return (int) ($this->reviews_count ?? 0);
    }

    public function getPackagesAttribute($value): array
    {
        $packages = is_string($value) ? json_decode($value, true) : $value;
        if (!is_array($packages) || empty($packages)) {
            $basePrice = (float) ($this->attributes['price'] ?? 499);
            $baseDelivery = (int) ($this->attributes['delivery_days'] ?? 3);
            return [
                'basic' => [
                    'name' => 'Basic',
                    'price' => $basePrice,
                    'delivery' => $baseDelivery,
                    'revisions' => 1,
                    'features' => ['Basic deliverable', 'Source file included', '1 Revision'],
                ],
                'standard' => [
                    'name' => 'Standard',
                    'price' => round($basePrice * 1.8),
                    'delivery' => max(1, $baseDelivery + 2),
                    'revisions' => 3,
                    'features' => ['Everything in Basic', 'Database setup', 'Admin dashboard', '3 Revisions'],
                ],
                'premium' => [
                    'name' => 'Premium',
                    'price' => round($basePrice * 3.2),
                    'delivery' => max(1, $baseDelivery + 5),
                    'revisions' => 5,
                    'features' => ['Full production app', 'Deployment help', 'Commercial license', '14 days support'],
                ],
            ];
        }

        foreach (['basic', 'standard', 'premium'] as $tier) {
            if (isset($packages[$tier]) && is_array($packages[$tier])) {
                $packages[$tier]['name'] = $packages[$tier]['name'] ?? ucfirst($tier);
                $packages[$tier]['price'] = (float) ($packages[$tier]['price'] ?? 0);
                $packages[$tier]['delivery'] = (int) ($packages[$tier]['delivery'] ?? 3);
                $packages[$tier]['revisions'] = (int) ($packages[$tier]['revisions'] ?? 1);
                if (!isset($packages[$tier]['features']) || !is_array($packages[$tier]['features'])) {
                    $packages[$tier]['features'] = ['Deliverable included'];
                }
            }
        }

        return $packages;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Scope for searching across title, description, category name, freelancer name, and user skills.
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $term = strtolower(trim($term));

        return $query->where(function ($q) use ($term) {
            $q->whereRaw('LOWER(title) LIKE ?', ["%{$term}%"])
              ->orWhereRaw('LOWER(description) LIKE ?', ["%{$term}%"])
              ->orWhereHas('category', function ($catQ) use ($term) {
                  $catQ->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                       ->orWhereRaw('LOWER(slug) LIKE ?', ["%{$term}%"]);
              })
              ->orWhereHas('user', function ($userQ) use ($term) {
                  $userQ->whereRaw('LOWER(name) LIKE ?', ["%{$term}%"])
                        ->orWhereRaw('LOWER(skills) LIKE ?', ["%{$term}%"]);
              });
        });
    }

    /**
     * Scope for filtering by category slug(s).
     */
    public function scopeByCategory($query, $categorySlugs)
    {
        if (empty($categorySlugs)) {
            return $query;
        }

        $slugs = is_array($categorySlugs) ? $categorySlugs : [$categorySlugs];

        return $query->whereHas('category', function ($q) use ($slugs) {
            $q->whereIn('slug', $slugs);
        });
    }

    /**
     * Scope for budget filtering.
     */
    public function scopeByPrice($query, $minPrice = null, $maxPrice = null)
    {
        if ($minPrice !== null && $minPrice !== '') {
            $query->where('price', '>=', (float) $minPrice);
        }
        if ($maxPrice !== null && $maxPrice !== '') {
            $query->where('price', '<=', (float) $maxPrice);
        }
        return $query;
    }

    /**
     * Scope for seller rating filtering.
     */
    public function scopeByRating($query, $minRating = null)
    {
        if ($minRating !== null && $minRating !== '' && (float) $minRating > 0) {
            $query->where('rating', '>=', (float) $minRating);
        }
        return $query;
    }

    /**
     * Scope for delivery days filtering.
     */
    public function scopeByDelivery($query, $maxDelivery = null)
    {
        if ($maxDelivery !== null && $maxDelivery !== '' && (int) $maxDelivery < 999) {
            $query->where('delivery_days', '<=', (int) $maxDelivery);
        }
        return $query;
    }

    /**
     * Scope for sorting.
     */
    public function scopeSortBy($query, ?string $sortBy)
    {
        return match ($sortBy) {
            'price-low' => $query->orderBy('price', 'asc'),
            'price-high' => $query->orderBy('price', 'desc'),
            'rating' => $query->orderBy('rating', 'desc')->orderBy('reviews_count', 'desc'),
            'newest' => $query->orderBy('created_at', 'desc'),
            default => $query->orderBy('rating', 'desc')->orderBy('id', 'asc'),
        };
    }
}
