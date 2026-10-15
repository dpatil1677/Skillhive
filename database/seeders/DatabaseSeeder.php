<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Service;
use App\Models\Order;
use App\Models\Message;
use App\Models\Portfolio;
use App\Models\Review;
use App\Models\Favorite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seed Admin User
        $admin = User::create([
            'name' => 'SkillHive Admin',
            'email' => 'admin@skillhive.edu.in',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'profile_image' => 'https://ui-avatars.com/api/?name=Admin&background=111827&color=fff&size=128',
            'college' => 'SkillHive Platform',
            'course' => 'System Administrator',
            'bio' => 'SkillHive platform administrator managing user accounts, service listings, orders, and system settings.',
            'skills' => ['Moderation', 'Analytics', 'System Administration'],
        ]);

        // 2. Seed 8 Indian Student Users
        $usersData = [
            [
                'name' => 'Dhruv Patil',
                'email' => 'dhruv.patil@mitpune.edu.in',
                'role' => 'user',
                'profile_image' => 'https://ui-avatars.com/api/?name=Dhruv+Patil&background=6D28D9&color=fff&size=128',
                'college' => 'MIT Pune',
                'course' => 'B.Tech Computer Science',
                'bio' => 'Full-stack web developer specializing in Laravel, React, and MySQL. Building web applications for college startups and student projects.',
                'skills' => ['Laravel', 'React', 'PHP', 'MySQL', 'Bootstrap'],
            ],
            [
                'name' => 'Aarav Shah',
                'email' => 'aarav.shah@nid.edu.in',
                'role' => 'user',
                'profile_image' => 'https://ui-avatars.com/api/?name=Aarav+Shah&background=0369a1&color=fff&size=128',
                'college' => 'NID Ahmedabad',
                'course' => 'B.Des Graphic Design',
                'bio' => 'Design student at NID focused on brand identity, minimalist logos, and visual storytelling for modern startups.',
                'skills' => ['Adobe Illustrator', 'Photoshop', 'Figma', 'Branding'],
            ],
            [
                'name' => 'Rahul Mehta',
                'email' => 'rahul.m@iitb.ac.in',
                'role' => 'user',
                'profile_image' => 'https://ui-avatars.com/api/?name=Rahul+Mehta&background=059669&color=fff&size=128',
                'college' => 'IIT Bombay',
                'course' => 'B.Tech Computer Science',
                'bio' => 'Python developer & automation enthusiast. Expert in web scraping, data processing scripts, and custom APIs.',
                'skills' => ['Python', 'Django', 'Pandas', 'Selenium', 'FastAPI'],
            ],
            [
                'name' => 'Mitali Patel',
                'email' => 'mitali.p@srishti.edu.in',
                'role' => 'seller',
                'profile_image' => 'https://ui-avatars.com/api/?name=Mitali+Patel&background=dc2626&color=fff&size=128',
                'college' => 'Srishti Bangalore',
                'course' => 'B.Des UI/UX Design',
                'bio' => 'UI/UX designer creating modern mobile apps and web user interfaces with clean wireframes and prototype systems.',
                'skills' => ['Figma', 'Adobe XD', 'User Research', 'Wireframing'],
            ],
            [
                'name' => 'Riya Desai',
                'email' => 'riya.desai@symbiosis.ac.in',
                'role' => 'user',
                'profile_image' => 'https://ui-avatars.com/api/?name=Riya+Desai&background=7c3aed&color=fff&size=128',
                'college' => 'Symbiosis Pune',
                'course' => 'B.Sc Film & Media',
                'bio' => 'Video editor and motion graphics artist creating engaging Instagram Reels, YouTube videos, and brand promotional clips.',
                'skills' => ['Premiere Pro', 'After Effects', 'DaVinci Resolve', 'Motion Graphics'],
            ],
            [
                'name' => 'Sneha Kulkarni',
                'email' => 'sneha.k@symbiosis.ac.in',
                'role' => 'user',
                'profile_image' => 'https://ui-avatars.com/api/?name=Sneha+K&background=6D28D9&color=fff&size=128',
                'college' => 'Symbiosis Pune',
                'course' => 'MBA Event Management',
                'bio' => 'Marketing student looking for tech and design talent for campus event projects and startup launches.',
                'skills' => ['Event Management', 'Digital Marketing', 'Content Strategy'],
            ],
            [
                'name' => 'Karan Joshi',
                'email' => 'karan.j@vit.ac.in',
                'role' => 'user',
                'profile_image' => 'https://ui-avatars.com/api/?name=Karan+J&background=1d4ed8&color=fff&size=128',
                'college' => 'VIT Vellore',
                'course' => 'B.Tech Information Technology',
                'bio' => 'Final year engineering student hiring student developers for major project assistance.',
                'skills' => ['C++', 'Java', 'SQL'],
            ],
            [
                'name' => 'Arjun Nair',
                'email' => 'arjun.n@iitm.ac.in',
                'role' => 'user',
                'profile_image' => 'https://ui-avatars.com/api/?name=Arjun+N&background=059669&color=fff&size=128',
                'college' => 'IIT Madras',
                'course' => 'B.Tech Electrical Engineering',
                'bio' => 'Engineering student looking for presentation designers and data analysts.',
                'skills' => ['Python', 'MATLAB', 'Circuit Design'],
            ],
        ];

        $createdUsers = [];
        foreach ($usersData as $u) {
            $createdUsers[] = User::create([
                'name' => $u['name'],
                'email' => $u['email'],
                'password' => Hash::make('password123'),
                'role' => $u['role'],
                'profile_image' => $u['profile_image'],
                'college' => $u['college'],
                'course' => $u['course'],
                'bio' => $u['bio'],
                'skills' => $u['skills'],
            ]);
        }

        // 3. Seed 8 Categories
        $categoriesData = [
            ['name' => 'Web Development', 'slug' => 'web-development', 'icon' => 'bi-code-slash', 'description' => 'Custom websites, web apps, landing pages and more built by student developers.'],
            ['name' => 'Graphic Design', 'slug' => 'graphic-design', 'icon' => 'bi-palette', 'description' => 'Logos, brand identity, posters and visual design by creative student designers.'],
            ['name' => 'Video & Animation', 'slug' => 'video-animation', 'icon' => 'bi-camera-video', 'description' => 'Video editing, motion graphics, animations and visual storytelling.'],
            ['name' => 'Writing & Content', 'slug' => 'writing-content', 'icon' => 'bi-pencil', 'description' => 'Blog posts, articles, copywriting and content creation services.'],
            ['name' => 'Programming', 'slug' => 'programming', 'icon' => 'bi-terminal', 'description' => 'Scripts, automation, APIs and software development for any project.'],
            ['name' => 'Data & Analytics', 'slug' => 'data-analytics', 'icon' => 'bi-bar-chart-line', 'description' => 'Data analysis, visualization, machine learning and research projects.'],
            ['name' => 'Business', 'slug' => 'business', 'icon' => 'bi-briefcase', 'description' => 'Business plans, market research, financial models and consulting.'],
            ['name' => 'Presentation Design', 'slug' => 'presentation-design', 'icon' => 'bi-easel', 'description' => 'Professional presentations, pitch decks and slide design.'],
        ];

        $createdCategories = [];
        foreach ($categoriesData as $cat) {
            $createdCategories[] = Category::create([
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'icon' => $cat['icon'],
                'description' => $cat['description'],
                'image' => 'https://placehold.co/600x400/e8e0f5/6D28D9?text=' . urlencode($cat['name']),
                'status' => 'active',
            ]);
        }

        // 4. Seed 20 Realistic Services
        $servicesSeed = [
            [
                'user_id' => $createdUsers[0]->id, // Dhruv
                'category_id' => $createdCategories[0]->id, // Web Dev
                'title' => 'I will build a modern Laravel website for your college project',
                'slug' => 'build-modern-laravel-website',
                'description' => 'Custom full-stack Laravel website with authentication, database integration, and responsive Bootstrap 5 UI.',
                'image' => 'https://placehold.co/900x540/e8e0f5/6D28D9?text=Laravel+Website',
                'price' => 2499,
                'delivery_days' => 5,
                'revisions' => 3,
                'rating' => 4.9,
                'reviews_count' => 23,
                'packages' => [
                    'basic' => ['name' => 'Basic', 'price' => 999, 'delivery' => 3, 'revisions' => 1, 'features' => ['Up to 3 pages', 'Contact form']],
                    'standard' => ['name' => 'Standard', 'price' => 2499, 'delivery' => 5, 'revisions' => 3, 'features' => ['Up to 8 pages', 'User authentication', 'MySQL database']],
                    'premium' => ['name' => 'Premium', 'price' => 3999, 'delivery' => 10, 'revisions' => 5, 'features' => ['Unlimited pages', 'REST API', 'Admin dashboard']],
                ],
            ],
            [
                'user_id' => $createdUsers[1]->id, // Aarav
                'category_id' => $createdCategories[1]->id, // Graphic Design
                'title' => 'I will design a minimal brand identity and logo for your startup',
                'slug' => 'minimal-brand-identity-logo',
                'description' => 'Clean minimalist logo design with brand color palette, typography guidelines, and vector source files.',
                'image' => 'https://placehold.co/900x540/dbeafe/0369a1?text=Brand+Identity',
                'price' => 1499,
                'delivery_days' => 3,
                'revisions' => 2,
                'rating' => 4.8,
                'reviews_count' => 18,
                'packages' => [
                    'basic' => ['name' => 'Basic', 'price' => 699, 'delivery' => 2, 'revisions' => 2, 'features' => ['1 concept']],
                    'standard' => ['name' => 'Standard', 'price' => 1499, 'delivery' => 3, 'revisions' => 3, 'features' => ['3 concepts', 'Source files']],
                    'premium' => ['name' => 'Premium', 'price' => 2999, 'delivery' => 6, 'revisions' => 5, 'features' => ['Unlimited concepts', 'Brand guidelines']],
                ],
            ],
            [
                'user_id' => $createdUsers[3]->id, // Mitali
                'category_id' => $createdCategories[7]->id, // Presentation Design
                'title' => 'I will create a professional presentation deck for your major project',
                'slug' => 'professional-presentation-deck',
                'description' => 'Stunning PowerPoint or Google Slides pitch deck design with custom graphics, infographics, and clean typography.',
                'image' => 'https://placehold.co/900x540/fef3c7/d97706?text=Presentation+Deck',
                'price' => 999,
                'delivery_days' => 2,
                'revisions' => 3,
                'rating' => 5.0,
                'reviews_count' => 14,
                'packages' => [
                    'basic' => ['name' => 'Basic', 'price' => 499, 'delivery' => 1, 'revisions' => 1, 'features' => ['Up to 5 slides']],
                    'standard' => ['name' => 'Standard', 'price' => 999, 'delivery' => 2, 'revisions' => 3, 'features' => ['Up to 15 slides', 'Custom graphics']],
                    'premium' => ['name' => 'Premium', 'price' => 1999, 'delivery' => 4, 'revisions' => 5, 'features' => ['Up to 30 slides', 'Animations & PDF']],
                ],
            ],
            [
                'user_id' => $createdUsers[2]->id, // Rahul
                'category_id' => $createdCategories[4]->id, // Programming
                'title' => 'I will write a Python web automation script or scraper',
                'slug' => 'python-automation-script-scraper',
                'description' => 'Fast Python script using BeautifulSoup, Selenium, or Pandas for automated data extraction and file formatting.',
                'image' => 'https://placehold.co/900x540/dcfce7/15803d?text=Python+Script',
                'price' => 799,
                'delivery_days' => 2,
                'revisions' => 1,
                'rating' => 4.7,
                'reviews_count' => 29,
                'packages' => [
                    'basic' => ['name' => 'Basic', 'price' => 499, 'delivery' => 1, 'revisions' => 1, 'features' => ['Simple script']],
                    'standard' => ['name' => 'Standard', 'price' => 799, 'delivery' => 2, 'revisions' => 2, 'features' => ['Complex parser & Excel export']],
                    'premium' => ['name' => 'Premium', 'price' => 1499, 'delivery' => 3, 'revisions' => 3, 'features' => ['Full web scraper with GUI']],
                ],
            ],
            [
                'user_id' => $createdUsers[4]->id, // Riya
                'category_id' => $createdCategories[2]->id, // Video
                'title' => 'I will edit short-form Instagram reels and YouTube shorts',
                'slug' => 'edit-instagram-reels-youtube-shorts',
                'description' => 'Dynamic short-form video editing with captions, sound effects, jump cuts, and color grading.',
                'image' => 'https://placehold.co/900x540/f3e8ff/7c3aed?text=Video+Editing',
                'price' => 599,
                'delivery_days' => 1,
                'revisions' => 2,
                'rating' => 4.9,
                'reviews_count' => 19,
                'packages' => [
                    'basic' => ['name' => 'Basic', 'price' => 399, 'delivery' => 1, 'revisions' => 1, 'features' => ['1 Reel (60s)']],
                    'standard' => ['name' => 'Standard', 'price' => 599, 'delivery' => 1, 'revisions' => 2, 'features' => ['3 Reels with subtitles']],
                    'premium' => ['name' => 'Premium', 'price' => 1299, 'delivery' => 2, 'revisions' => 3, 'features' => ['7 Reels content pack']],
                ],
            ],
        ];

        // Fill up to 20 total services dynamically
        for ($i = 6; $i <= 20; $i++) {
            $user = $createdUsers[$i % 5];
            $cat = $createdCategories[$i % 8];
            $title = "I will provide professional " . strtolower($cat->name) . " service #" . $i . " for your project";
            $servicesSeed[] = [
                'user_id' => $user->id,
                'category_id' => $cat->id,
                'title' => $title,
                'slug' => Str::slug($title),
                'description' => "High quality student-rendered service for " . $cat->name . ". Includes source files and fast delivery.",
                'image' => 'https://placehold.co/900x540/e8e0f5/6D28D9?text=Service+' . $i,
                'price' => 499 + ($i * 100),
                'delivery_days' => rand(2, 5),
                'revisions' => rand(1, 4),
                'rating' => 4.5 + (($i % 5) * 0.1),
                'reviews_count' => rand(5, 40),
                'packages' => [
                    'basic' => ['name' => 'Basic', 'price' => 499, 'delivery' => 2, 'revisions' => 1, 'features' => ['Standard delivery']],
                    'standard' => ['name' => 'Standard', 'price' => 999, 'delivery' => 3, 'revisions' => 2, 'features' => ['Standard delivery', 'Source code']],
                    'premium' => ['name' => 'Premium', 'price' => 1999, 'delivery' => 5, 'revisions' => 3, 'features' => ['Priority delivery', 'Full support']],
                ],
            ];
        }

        $createdServices = [];
        foreach ($servicesSeed as $s) {
            $createdServices[] = Service::create($s);
        }

        // 5. Seed 10 Portfolios
        $portfolioData = [
            [
                'user_id' => $createdUsers[0]->id, // Dhruv
                'title' => 'Campus Connect — Student Event Platform',
                'slug' => 'campus-connect-student-event-platform',
                'description' => 'A full-stack web application built with Laravel and MySQL that allows college students to discover, register for, and organize campus events.',
                'technologies' => ['Laravel', 'PHP', 'MySQL', 'Bootstrap', 'JavaScript'],
                'image' => 'https://placehold.co/800x500/e8e0f5/6D28D9?text=Campus+Connect',
                'project_url' => 'https://example.com/campus-connect',
            ],
            [
                'user_id' => $createdUsers[0]->id, // Dhruv
                'title' => 'ShopLocal — E-Commerce Dashboard',
                'slug' => 'shoplocal-ecommerce-dashboard',
                'description' => 'A responsive e-commerce admin dashboard with product management, order tracking, and sales analytics for local Pune businesses.',
                'technologies' => ['Laravel', 'Vue.js', 'MySQL', 'Chart.js'],
                'image' => 'https://placehold.co/800x500/dbeafe/1d4ed8?text=ShopLocal',
                'project_url' => 'https://example.com/shoplocal',
            ],
            [
                'user_id' => $createdUsers[1]->id, // Aarav
                'title' => 'Verdant — Sustainable Brand Identity',
                'slug' => 'verdant-sustainable-brand-identity',
                'description' => 'Complete brand identity for an eco-friendly product startup. Deliverables included logo, color system, typography, and guidelines document.',
                'technologies' => ['Illustrator', 'Photoshop', 'Figma'],
                'image' => 'https://placehold.co/800x500/dcfce7/166534?text=Verdant+Brand',
                'project_url' => 'https://example.com/verdant',
            ],
            [
                'user_id' => $createdUsers[3]->id, // Mitali
                'title' => 'EduTrack Mobile App Concept',
                'slug' => 'edutrack-mobile-app-concept',
                'description' => 'UI/UX wireframes and high-fidelity interactive Figma prototype for a student attendance and assignment tracking app.',
                'technologies' => ['Figma', 'User Research', 'Prototyping'],
                'image' => 'https://placehold.co/800x500/fef3c7/d97706?text=EduTrack+App',
                'project_url' => 'https://example.com/edutrack',
            ],
        ];

        for ($k = 5; $k <= 10; $k++) {
            $user = $createdUsers[$k % 5];
            $portfolioData[] = [
                'user_id' => $user->id,
                'title' => 'Student Project Portfolio Showcase #' . $k,
                'slug' => 'student-project-portfolio-showcase-' . $k,
                'description' => 'Academic showcase project demonstrating technical skills and creative design principles.',
                'technologies' => ['HTML/CSS', 'JavaScript', 'Design'],
                'image' => 'https://placehold.co/800x500/e8e0f5/6D28D9?text=Portfolio+' . $k,
                'project_url' => 'https://example.com/project-' . $k,
            ];
        }

        foreach ($portfolioData as $p) {
            Portfolio::create($p);
        }

        // 6. Seed Sample Orders
        $ordersData = [
            [
                'order_number' => 'SH-8492',
                'client_id' => $createdUsers[5]->id, // Sneha
                'freelancer_id' => $createdUsers[0]->id, // Dhruv
                'service_id' => $createdServices[0]->id,
                'package' => 'Standard',
                'price' => 2499,
                'platform_fee' => 50,
                'total_amount' => 2549,
                'requirements' => 'Need a Laravel website for my college event management startup with registration forms and user login.',
                'status' => 'in_progress',
                'delivery_date' => now()->addDays(3),
            ],
            [
                'order_number' => 'SH-7310',
                'client_id' => $createdUsers[0]->id, // Dhruv
                'freelancer_id' => $createdUsers[1]->id, // Aarav
                'service_id' => $createdServices[1]->id,
                'package' => 'Basic',
                'price' => 699,
                'platform_fee' => 50,
                'total_amount' => 749,
                'requirements' => 'Minimal logo design for a personal tech blog with dark mode aesthetics.',
                'status' => 'completed',
                'delivery_date' => now()->subDays(2),
            ],
            [
                'order_number' => 'SH-6105',
                'client_id' => $createdUsers[6]->id, // Karan
                'freelancer_id' => $createdUsers[3]->id, // Mitali
                'service_id' => $createdServices[2]->id,
                'package' => 'Standard',
                'price' => 999,
                'platform_fee' => 50,
                'total_amount' => 1049,
                'requirements' => '15 slide presentation deck for my final year major project evaluation.',
                'status' => 'completed',
                'delivery_date' => now()->subDays(5),
            ],
            [
                'order_number' => 'SH-5012',
                'client_id' => $createdUsers[7]->id, // Arjun
                'freelancer_id' => $createdUsers[2]->id, // Rahul
                'service_id' => $createdServices[3]->id,
                'package' => 'Basic',
                'price' => 499,
                'platform_fee' => 50,
                'total_amount' => 549,
                'requirements' => 'Python script to clean Excel CSV datasets.',
                'status' => 'cancelled',
                'delivery_date' => null,
            ],
        ];

        $createdOrders = [];
        foreach ($ordersData as $o) {
            $createdOrders[] = Order::create($o);
        }

        // 7. Seed Realistic Chat Messages (multiple conversation threads)
        $messagesData = [
            // === Dhruv ↔ Aarav (Logo Design Order) ===
            ['sender_id' => $createdUsers[1]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Hi Dhruv! Saw your order for the logo design. Excited to work on it!', 'is_read' => true, 'created_at' => now()->subDays(5)->setTime(9,10)],
            ['sender_id' => $createdUsers[0]->id, 'receiver_id' => $createdUsers[1]->id, 'message' => 'Hey Aarav! Yes, looking for something clean and minimal — dark theme accents please.', 'is_read' => true, 'created_at' => now()->subDays(5)->setTime(9,22)],
            ['sender_id' => $createdUsers[1]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Perfect. I\'ll work on 3 different concepts and share them by tomorrow evening.', 'is_read' => true, 'created_at' => now()->subDays(5)->setTime(9,30)],
            ['sender_id' => $createdUsers[0]->id, 'receiver_id' => $createdUsers[1]->id, 'message' => 'Sounds great! Take your time. Quality over speed 🙌', 'is_read' => true, 'created_at' => now()->subDays(5)->setTime(9,35)],
            ['sender_id' => $createdUsers[1]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Done! Uploaded the vector logo concepts ZIP to your order. Concept 2 is my personal favourite.', 'is_read' => true, 'created_at' => now()->subDays(4)->setTime(18,45)],
            ['sender_id' => $createdUsers[0]->id, 'receiver_id' => $createdUsers[1]->id, 'message' => 'Concept 2 is 🔥! Love the hexagon geometry. Can you try it in purple too?', 'is_read' => true, 'created_at' => now()->subDays(4)->setTime(20,10)],
            ['sender_id' => $createdUsers[1]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Of course! Purple version coming right up. Will have it ready in 30 mins.', 'is_read' => true, 'created_at' => now()->subDays(4)->setTime(20,15)],
            ['sender_id' => $createdUsers[1]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Purple variant uploaded! Also added a white-on-dark version as a bonus. Check it out!', 'is_read' => true, 'created_at' => now()->subDays(4)->setTime(20,55)],
            ['sender_id' => $createdUsers[0]->id, 'receiver_id' => $createdUsers[1]->id, 'message' => 'This is exactly what I needed! Marking the order complete. Thanks Aarav, will leave you a 5-star review 🙏', 'is_read' => true, 'created_at' => now()->subDays(3)->setTime(10,0)],

            // === Dhruv ↔ Mitali (Presentation Deck) ===
            ['sender_id' => $createdUsers[0]->id, 'receiver_id' => $createdUsers[3]->id, 'message' => 'Hi Mitali! I ordered the presentation deck for my final year project. 15 slides needed by Thursday.', 'is_read' => true, 'created_at' => now()->subDays(7)->setTime(11,0)],
            ['sender_id' => $createdUsers[3]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Hi Dhruv! Got your requirements. What\'s the topic and should I use a light or dark theme?', 'is_read' => true, 'created_at' => now()->subDays(7)->setTime(11,30)],
            ['sender_id' => $createdUsers[0]->id, 'receiver_id' => $createdUsers[3]->id, 'message' => 'Topic: Student Freelance Marketplace (SkillHive). Prefer a professional dark blue theme please!', 'is_read' => true, 'created_at' => now()->subDays(7)->setTime(11,45)],
            ['sender_id' => $createdUsers[3]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Perfect topic! I\'ll use a navy + gold accent palette. Starting now 🎨', 'is_read' => true, 'created_at' => now()->subDays(7)->setTime(12,0)],
            ['sender_id' => $createdUsers[3]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Draft is ready! 15 slides uploaded — intro, problem, solution, market, team, financials, and CTA. Check slide 4 font size.', 'is_read' => true, 'created_at' => now()->subDays(6)->setTime(17,20)],
            ['sender_id' => $createdUsers[0]->id, 'receiver_id' => $createdUsers[3]->id, 'message' => 'Loved the pitch deck design! Can you make slide 4 font slightly larger? Otherwise perfect!', 'is_read' => true, 'created_at' => now()->subDays(6)->setTime(18,10)],
            ['sender_id' => $createdUsers[3]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Updated slide 4 and uploaded the final PPT + PDF export. Good luck with your presentation! 🤞', 'is_read' => true, 'created_at' => now()->subDays(6)->setTime(18,50)],
            ['sender_id' => $createdUsers[3]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Thank you for approving the presentation slides! Hope your faculty loved it!', 'is_read' => false, 'created_at' => now()->subHours(2)],

            // === Karan ↔ Mitali (Major Project Presentation) ===
            ['sender_id' => $createdUsers[6]->id, 'receiver_id' => $createdUsers[3]->id, 'message' => 'Hi Mitali! Ordered your deck service. Need a 15-slide PPT for B.Tech project evaluation on Thursday.', 'is_read' => true, 'created_at' => now()->subDays(8)->setTime(9,0)],
            ['sender_id' => $createdUsers[3]->id, 'receiver_id' => $createdUsers[6]->id, 'message' => 'Sure Karan! Please share your project report and I\'ll design around your content.', 'is_read' => true, 'created_at' => now()->subDays(8)->setTime(10,15)],
            ['sender_id' => $createdUsers[6]->id, 'receiver_id' => $createdUsers[3]->id, 'message' => 'Report shared via email. Topic: Smart Traffic Signal with IoT + ML. Deadline is strict!', 'is_read' => true, 'created_at' => now()->subDays(8)->setTime(10,30)],
            ['sender_id' => $createdUsers[3]->id, 'receiver_id' => $createdUsers[6]->id, 'message' => 'Got it! Will deliver by Wednesday night. The IoT diagrams will look great as infographics.', 'is_read' => true, 'created_at' => now()->subDays(8)->setTime(10,45)],
            ['sender_id' => $createdUsers[3]->id, 'receiver_id' => $createdUsers[6]->id, 'message' => 'Deck is ready and uploaded to the order! All 15 slides with animations. Review and let me know 😊', 'is_read' => true, 'created_at' => now()->subDays(5)->setTime(22,0)],
            ['sender_id' => $createdUsers[6]->id, 'receiver_id' => $createdUsers[3]->id, 'message' => 'Mitali this is incredible! Faculty was seriously impressed. 5 stars from me!', 'is_read' => true, 'created_at' => now()->subDays(4)->setTime(15,0)],

            // === Sneha ↔ Dhruv (Laravel Website Order) ===
            ['sender_id' => $createdUsers[5]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Hi Dhruv! Placed an order for the Laravel website. Need user login, event listing, and registration forms.', 'is_read' => true, 'created_at' => now()->subDays(3)->setTime(10,0)],
            ['sender_id' => $createdUsers[0]->id, 'receiver_id' => $createdUsers[5]->id, 'message' => 'Hey Sneha! Got the requirements. I\'ll start with auth flows today. Do you have a brand color preference?', 'is_read' => true, 'created_at' => now()->subDays(3)->setTime(10,30)],
            ['sender_id' => $createdUsers[5]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Go with our college purple #6D28D9. We want it to match SkillHive vibes 😄', 'is_read' => true, 'created_at' => now()->subDays(3)->setTime(10,45)],
            ['sender_id' => $createdUsers[0]->id, 'receiver_id' => $createdUsers[5]->id, 'message' => 'Perfect! Shared initial wireframes on Figma. Check the invite in your email.', 'is_read' => true, 'created_at' => now()->subDays(2)->setTime(16,0)],
            ['sender_id' => $createdUsers[5]->id, 'receiver_id' => $createdUsers[0]->id, 'message' => 'Wireframes look amazing! Approve. Let\'s proceed with development.', 'is_read' => false, 'created_at' => now()->subHours(1)],
        ];

        foreach ($messagesData as $m) {
            Message::create($m);
        }

        // 8. Seed Sample Reviews
        Review::create([
            'order_id' => $createdOrders[1]->id, // SH-7310
            'user_id' => $createdUsers[0]->id, // Dhruv
            'freelancer_id' => $createdUsers[1]->id, // Aarav
            'rating' => 5,
            'comment' => 'Aarav designed a clean, minimal logo concept. Delivered fast and communicated well!',
        ]);

        Review::create([
            'order_id' => $createdOrders[2]->id, // SH-6105
            'user_id' => $createdUsers[6]->id, // Karan
            'freelancer_id' => $createdUsers[3]->id, // Mitali
            'rating' => 5,
            'comment' => 'Mitali created an exceptional pitch deck for my project presentation. Faculty appreciated the design quality.',
        ]);

        // 9. Seed Sample Favorites
        Favorite::create([
            'user_id' => $createdUsers[0]->id,
            'service_id' => $createdServices[1]->id,
        ]);

        Favorite::create([
            'user_id' => $createdUsers[0]->id,
            'service_id' => $createdServices[2]->id,
        ]);
    }
}
