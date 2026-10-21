<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') — SkillHive</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    {{-- Bootstrap 5.3 & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    {{-- SkillHive Styles --}}
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <style>
        body { background-color: #f8fafc; }
        .admin-wrapper { display: flex; min-height: 100vh; }
        .admin-sidebar {
            width: 260px;
            background: #0f172a;
            color: #94a3b8;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .admin-sidebar-brand {
            padding: 1.5rem;
            color: #fff;
            font-size: 1.25rem;
            font-weight: 700;
            text-decoration: none;
            border-bottom: 1px solid #1e293b;
        }
        .admin-nav-link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.5rem;
            color: #94a3b8;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.15s ease;
            border-left: 4px solid transparent;
        }
        .admin-nav-link:hover, .admin-nav-link.active {
            color: #fff;
            background: #1e293b;
            border-left-color: var(--color-primary, #6D28D9);
        }
        .admin-nav-label {
            padding: 1rem 1.5rem 0.25rem;
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 0.8px;
            text-transform: uppercase;
            color: #475569;
        }
        .admin-content { flex-grow: 1; padding: 2rem; overflow-x: hidden; }
        .impersonation-bar {
            background: linear-gradient(90deg,#7c3aed,#db2777);
            color: #fff;
            font-size: 13px;
            padding: 8px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            flex-shrink: 0;
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- Impersonation Banner (shown when admin is impersonating a user) --}}
@if(session()->has('admin_impersonator_id'))
<div class="impersonation-bar">
    <span>
        <i class="bi bi-shield-fill-check me-2"></i>
        <strong>Admin Impersonation Active</strong> — You are viewing the site as <strong>{{ Auth::user()->name }}</strong>. Actions you take will be attributed to this user.
    </span>
    <a href="{{ route('admin.stop-impersonating') }}" class="btn btn-sm btn-light text-purple fw-semibold" style="color:#7c3aed;">
        <i class="bi bi-door-open me-1"></i> Exit &amp; Return to Admin
    </a>
</div>
@endif

<div class="admin-wrapper">
    {{-- Admin Sidebar --}}
    <aside class="admin-sidebar shadow-sm">
        <div>
            <a href="{{ route('admin.dashboard') }}" class="admin-sidebar-brand d-flex align-items-center gap-2">
                <i class="bi bi-hexagon-fill text-primary"></i>
                <span>SkillHive Admin</span>
            </a>

            <div class="py-2">
                {{-- Main Navigation --}}
                <div class="admin-nav-label">Navigation</div>
                <a href="{{ route('admin.dashboard') }}" class="admin-nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 fs-5"></i> Dashboard
                </a>
                <a href="{{ route('admin.users') }}" class="admin-nav-link {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <i class="bi bi-people fs-5"></i> Users
                </a>
                <a href="{{ route('admin.services') }}" class="admin-nav-link {{ request()->routeIs('admin.services*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text fs-5"></i> Services
                </a>
                <a href="{{ route('admin.orders') }}" class="admin-nav-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                    <i class="bi bi-bag-check fs-5"></i> Orders
                </a>
                <a href="{{ route('admin.categories') }}" class="admin-nav-link {{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                    <i class="bi bi-folder-symlink fs-5"></i> Categories
                </a>
                <a href="{{ route('admin.reviews') }}" class="admin-nav-link {{ request()->routeIs('admin.reviews*') ? 'active' : '' }}">
                    <i class="bi bi-star-half fs-5"></i> Reviews
                </a>
                <a href="{{ route('admin.withdrawals') }}" class="admin-nav-link {{ request()->routeIs('admin.withdrawals*') ? 'active' : '' }}">
                    <i class="bi bi-wallet2 fs-5"></i> Payouts
                </a>

                {{-- View Dashboards --}}
                <div class="admin-nav-label mt-3">View As User</div>
                <a href="{{ route('dashboard') }}" class="admin-nav-link" target="_blank" rel="noopener noreferrer" title="Open Client Dashboard in new tab">
                    <i class="bi bi-grid fs-5"></i> Client Dashboard
                    <i class="bi bi-box-arrow-up-right ms-auto" style="font-size: 11px; opacity: .5;"></i>
                </a>
                <a href="{{ route('seller.dashboard') }}" class="admin-nav-link" target="_blank" rel="noopener noreferrer" title="Open Freelancer Dashboard in new tab">
                    <i class="bi bi-person-workspace fs-5"></i> Freelancer Dashboard
                    <i class="bi bi-box-arrow-up-right ms-auto" style="font-size: 11px; opacity: .5;"></i>
                </a>
                <a href="{{ route('explore') }}" class="admin-nav-link" target="_blank" rel="noopener noreferrer" title="View Marketplace">
                    <i class="bi bi-shop fs-5"></i> Marketplace
                    <i class="bi bi-box-arrow-up-right ms-auto" style="font-size: 11px; opacity: .5;"></i>
                </a>
            </div>
        </div>

        {{-- Sidebar Footer --}}
        <div class="p-3 border-top border-secondary border-opacity-25">
            <div class="d-flex align-items-center gap-2 mb-2">
                <img src="{{ Auth::user()->avatar }}" alt="{{ Auth::user()->name }}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                <div style="min-width:0">
                    <div class="text-white fw-semibold" style="font-size:12px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ Auth::user()->name }}</div>
                    <div style="font-size:10px;color:#94a3b8;">Administrator</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn-sh btn-sh-sm w-100 text-light border-secondary" style="background:transparent;border:1px solid #334155;border-radius:6px;padding:6px 0;">
                    <i class="bi bi-box-arrow-right me-1"></i> Sign Out
                </button>
            </form>
        </div>
    </aside>

    {{-- Main Admin Content --}}
    <main class="admin-content">
        @yield('content')
    </main>
</div>

{{-- Toast Container --}}
@include('components.toast')

{{-- Scripts --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
