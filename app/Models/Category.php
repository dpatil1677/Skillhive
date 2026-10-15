<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'image',
        'status',
    ];

    protected $appends = [
        'count',
        'subcategories',
    ];

    public function getCountAttribute(): int
    {
        return $this->services()->where('status', 'active')->count();
    }

    public function getSubcategoriesAttribute(): array
    {
        $map = [
            'web-development' => ['Frontend', 'Backend', 'Full Stack', 'WordPress', 'E-Commerce', 'Landing Pages'],
            'graphic-design' => ['Logo Design', 'Brand Identity', 'Poster Design', 'Social Media', 'Print Design'],
            'video-animation' => ['Video Editing', 'Motion Graphics', 'Animation', 'Intros & Outros', 'Short Form'],
            'writing-content' => ['Blog Posts', 'Copywriting', 'Technical Writing', 'Editing', 'Resumes'],
            'programming' => ['Python', 'Java', 'C++', 'APIs', 'Automation', 'Databases'],
            'data-analytics' => ['Data Analysis', 'Visualization', 'Machine Learning', 'Excel', 'Research'],
            'business' => ['Business Plans', 'Market Research', 'Financial Models', 'Consulting'],
            'presentation-design' => ['PowerPoint', 'Google Slides', 'Pitch Decks', 'Keynote', 'Infographics'],
        ];

        return $map[$this->slug] ?? ['General Services', 'Custom Deliverables'];
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }
}
