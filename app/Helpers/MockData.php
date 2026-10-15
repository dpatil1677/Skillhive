<?php

namespace App\Helpers;

class MockData
{
    public static function defaultCategories(): array
    {
        return [
            ['id' => 1, 'name' => 'Web Development', 'slug' => 'web-development', 'icon' => 'bi-code-slash', 'count' => 124, 'description' => 'Custom websites, web apps, landing pages and more built by student developers.', 'subcategories' => ['Frontend', 'Backend', 'Full Stack', 'WordPress', 'E-Commerce', 'Landing Pages']],
            ['id' => 2, 'name' => 'Graphic Design', 'slug' => 'graphic-design', 'icon' => 'bi-palette', 'count' => 98, 'description' => 'Logos, brand identity, posters and visual design by creative student designers.', 'subcategories' => ['Logo Design', 'Brand Identity', 'Poster Design', 'Social Media', 'Print Design']],
            ['id' => 3, 'name' => 'Video & Animation', 'slug' => 'video-animation', 'icon' => 'bi-camera-video', 'count' => 56, 'description' => 'Video editing, motion graphics, animations and visual storytelling.', 'subcategories' => ['Video Editing', 'Motion Graphics', 'Animation', 'Intros & Outros', 'Short Form']],
            ['id' => 4, 'name' => 'Writing & Content', 'slug' => 'writing-content', 'icon' => 'bi-pencil', 'count' => 72, 'description' => 'Blog posts, articles, copywriting and content creation services.', 'subcategories' => ['Blog Posts', 'Copywriting', 'Technical Writing', 'Editing', 'Resumes']],
            ['id' => 5, 'name' => 'Programming', 'slug' => 'programming', 'icon' => 'bi-terminal', 'count' => 89, 'description' => 'Scripts, automation, APIs and software development for any project.', 'subcategories' => ['Python', 'Java', 'C++', 'APIs', 'Automation', 'Databases']],
            ['id' => 6, 'name' => 'Data & Analytics', 'slug' => 'data-analytics', 'icon' => 'bi-bar-chart-line', 'count' => 34, 'description' => 'Data analysis, visualization, machine learning and research projects.', 'subcategories' => ['Data Analysis', 'Visualization', 'Machine Learning', 'Excel', 'Research']],
            ['id' => 7, 'name' => 'Business', 'slug' => 'business', 'icon' => 'bi-briefcase', 'count' => 45, 'description' => 'Business plans, market research, financial models and consulting.', 'subcategories' => ['Business Plans', 'Market Research', 'Financial Models', 'Consulting']],
            ['id' => 8, 'name' => 'Presentation Design', 'slug' => 'presentation-design', 'icon' => 'bi-easel', 'count' => 67, 'description' => 'Professional presentations, pitch decks and slide design.', 'subcategories' => ['PowerPoint', 'Google Slides', 'Pitch Decks', 'Keynote', 'Infographics']],
        ];
    }

    public static function defaultServices(): array
    {
        $freelancers = self::freelancers();

        return [
            [
                'id' => 1,
                'title' => 'I will build a modern Laravel website for your project',
                'slug' => 'modern-laravel-website',
                'freelancer' => $freelancers[0],
                'category' => 'Web Development',
                'category_slug' => 'web-development',
                'price' => 2499,
                'rating' => 4.9,
                'reviews' => 23,
                'delivery_days' => 5,
                'image' => 'https://placehold.co/900x540/e8e0f5/6D28D9?text=Laravel+Website',
                'gallery' => [
                    'https://placehold.co/900x540/e8e0f5/6D28D9?text=Laravel+Website',
                    'https://placehold.co/900x540/ede9fe/4c1d95?text=Admin+Panel',
                ],
                'tags' => ['Laravel', 'PHP', 'Web App'],
                'featured' => true,
                'description' => "I will build you a professional, production-ready Laravel website tailored to your requirements.",
                'what_you_get' => ['Fully functional Laravel project', 'Clean code', 'Responsive design'],
                'technologies' => ['Laravel', 'PHP 8', 'MySQL', 'Bootstrap 5'],
                'packages' => [
                    'basic' => ['name' => 'Basic', 'price' => 999, 'delivery' => 3, 'revisions' => 1, 'features' => ['Up to 3 pages', 'Contact form']],
                    'standard' => ['name' => 'Standard', 'price' => 2499, 'delivery' => 5, 'revisions' => 3, 'features' => ['Up to 8 pages', 'User authentication', 'MySQL database']],
                    'premium' => ['name' => 'Premium', 'price' => 3999, 'delivery' => 10, 'revisions' => 5, 'features' => ['Unlimited pages', 'REST API', 'Admin dashboard']],
                ],
            ],
            [
                'id' => 2,
                'title' => 'I will design a minimal brand identity for your startup',
                'slug' => 'minimal-brand-identity',
                'freelancer' => $freelancers[1],
                'category' => 'Graphic Design',
                'category_slug' => 'graphic-design',
                'price' => 1499,
                'rating' => 4.8,
                'reviews' => 18,
                'delivery_days' => 3,
                'image' => 'https://placehold.co/900x540/dbeafe/0369a1?text=Brand+Identity',
                'gallery' => ['https://placehold.co/900x540/dbeafe/0369a1?text=Brand+Identity'],
                'tags' => ['Branding', 'Logo'],
                'featured' => true,
                'description' => "Minimal brand identity design.",
                'what_you_get' => ['SVG, PNG files', 'Color palette'],
                'technologies' => ['Adobe Illustrator', 'Figma'],
                'packages' => [
                    'basic' => ['name' => 'Basic', 'price' => 699, 'delivery' => 2, 'revisions' => 2, 'features' => ['1 concept']],
                    'standard' => ['name' => 'Standard', 'price' => 1499, 'delivery' => 3, 'revisions' => 3, 'features' => ['3 concepts', 'Source files']],
                    'premium' => ['name' => 'Premium', 'price' => 2999, 'delivery' => 6, 'revisions' => 5, 'features' => ['Unlimited concepts', 'Brand guidelines']],
                ],
            ]
        ];
    }

    public static function categories(): array
    {
        if (session()->has('app_categories')) {
            return session('app_categories');
        }
        return static::defaultCategories();
    }

    public static function services(): array
    {
        if (session()->has('user_services')) {
            return session('user_services');
        }
        return static::defaultServices();
    }

    public static function freelancers(): array
    {
        return [
            [
                'id' => 1,
                'name' => 'Dhruv Patil',
                'slug' => 'dhruv-patil',
                'avatar' => 'https://ui-avatars.com/api/?name=Dhruv+Patil&background=6D28D9&color=fff&size=128',
                'skill' => 'Full Stack Developer',
                'skills' => ['Laravel', 'React', 'PHP', 'MySQL', 'REST APIs', 'Git'],
                'rating' => 4.9,
                'reviews' => 47,
                'verified' => true,
                'level' => 'Pro',
                'bio' => 'I am a Computer Science student at MIT Pune specializing in full-stack web development. I have been building web applications for over 3 years and love working on clean, scalable projects.',
                'college' => 'MIT Pune',
                'completed_orders' => 52,
                'member_since' => 'Jan 2025',
                'response_time' => 'Within 2 hours',
                'languages' => ['English', 'Hindi', 'Marathi'],
                'portfolio' => self::portfolioForFreelancer(1),
                'services_offered' => [1, 7, 10],
                'reviews_list' => self::reviewsForFreelancer(1),
            ],
            [
                'id' => 2,
                'name' => 'Aarav Shah',
                'slug' => 'aarav-shah',
                'avatar' => 'https://ui-avatars.com/api/?name=Aarav+Shah&background=0369a1&color=fff&size=128',
                'skill' => 'Graphic Designer',
                'skills' => ['Adobe Illustrator', 'Photoshop', 'Figma', 'Branding', 'Typography', 'Print Design'],
                'rating' => 4.8,
                'reviews' => 35,
                'verified' => true,
                'level' => 'Rising Talent',
                'bio' => 'Design student at NID Ahmedabad with a deep focus on brand identity and visual communication.',
                'college' => 'NID Ahmedabad',
                'completed_orders' => 38,
                'member_since' => 'Mar 2025',
                'response_time' => 'Within 4 hours',
                'languages' => ['English', 'Gujarati', 'Hindi'],
                'portfolio' => self::portfolioForFreelancer(2),
                'services_offered' => [2, 8],
                'reviews_list' => self::reviewsForFreelancer(2),
            ],
        ];
    }

    public static function portfolioForFreelancer(int $freelancerId): array
    {
        if ($freelancerId === 1 && session()->has('user_portfolio')) {
            return session('user_portfolio');
        }

        return [
            [
                'id' => 1,
                'title' => 'Campus Connect — Student Event Platform',
                'slug' => 'campus-connect',
                'description' => 'A full-stack web application built with Laravel and MySQL that allows college students to discover, register for, and organize campus events.',
                'image' => 'https://placehold.co/800x500/e8e0f5/6D28D9?text=Campus+Connect',
                'technologies' => ['Laravel', 'PHP', 'MySQL', 'Bootstrap', 'JavaScript'],
                'project_url' => '#',
                'category' => 'Web Development',
            ],
        ];
    }

    public static function reviewsForFreelancer(int $freelancerId): array
    {
        return [
            ['id' => 1, 'name' => 'Sneha Kulkarni', 'avatar' => 'https://ui-avatars.com/api/?name=Sneha+K&background=6D28D9&color=fff&size=64', 'rating' => 5, 'text' => 'Dhruv built exactly what I needed — a clean, fast website. Highly recommended!', 'date' => '2 weeks ago', 'service' => 'Portfolio Website', 'verified' => true],
        ];
    }

    public static function ratingSummary(array $reviews): array
    {
        $counts = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        $total = count($reviews);
        foreach ($reviews as $r) {
            $star = (int) round($r['rating']);
            if (isset($counts[$star])) $counts[$star]++;
        }
        $summary = [];
        for ($i = 5; $i >= 1; $i--) {
            $summary[$i] = [
                'count' => $counts[$i],
                'percent' => $total > 0 ? round(($counts[$i] / $total) * 100) : 0,
            ];
        }
        return $summary;
    }

    public static function servicesByCategory(string $categorySlug): array
    {
        return array_values(array_filter(self::services(), fn($s) => $s['category_slug'] === $categorySlug));
    }

    public static function category(string $slug): ?array
    {
        foreach (self::categories() as $category) {
            if ($category['slug'] === $slug) return $category;
        }
        return null;
    }

    public static function service(string $slug): ?array
    {
        foreach (self::services() as $service) {
            if ($service['slug'] === $slug) return $service;
        }
        return null;
    }

    public static function featuredServices(): array
    {
        return array_values(array_filter(self::services(), fn($s) => $s['featured'] ?? false));
    }

    public static function freelancer(string $slug): ?array
    {
        foreach (self::freelancers() as $f) {
            if ($f['slug'] === $slug) return $f;
        }
        return null;
    }

    public static function servicesForFreelancer(string $slug): array
    {
        return array_values(array_filter(self::services(), fn($s) => ($s['freelancer']['slug'] ?? '') === $slug));
    }

    public static function portfolioItem(string $freelancerSlug, string $portfolioSlug): ?array
    {
        $freelancer = self::freelancer($freelancerSlug);
        if (!$freelancer) return null;
        foreach ($freelancer['portfolio'] as $item) {
            if ($item['slug'] === $portfolioSlug) return $item;
        }
        return null;
    }

    public static function freelancersByCategory(string $categorySlug): array
    {
        $services = self::servicesByCategory($categorySlug);
        $freelancerIds = array_unique(array_column(array_column($services, 'freelancer'), 'id'));
        return array_values(array_filter(self::freelancers(), fn($f) => in_array($f['id'], $freelancerIds)));
    }

    public static function relatedServices(string $serviceSlug, int $limit = 4): array
    {
        $service = self::service($serviceSlug);
        if (!$service) return [];
        $related = array_filter(self::services(), fn($s) => $s['category_slug'] === $service['category_slug'] && $s['slug'] !== $serviceSlug);
        return array_slice(array_values($related), 0, $limit);
    }

    public static function testimonials(): array
    {
        return [
            ['text' => 'I needed a portfolio website for my internship applications. Dhruv built exactly what I envisioned — clean, fast and professional.', 'name' => 'Sneha Kulkarni', 'role' => 'MBA Student, Symbiosis Pune', 'avatar' => 'https://ui-avatars.com/api/?name=Sneha+K&background=6D28D9&color=fff&size=80', 'rating' => 5],
            ['text' => 'Mitali designed the entire UI for our final year project. Her attention to detail was beyond what we expected.', 'name' => 'Arjun Nair', 'role' => 'Engineering Student, VIT Vellore', 'avatar' => 'https://ui-avatars.com/api/?name=Arjun+N&background=059669&color=fff&size=80', 'rating' => 5],
        ];
    }
}
