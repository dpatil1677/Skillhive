@php
    $brandHref = Auth::check()
        ? match(Auth::user()->role) {
            'admin'  => route('admin.dashboard'),
            'seller' => route('seller.dashboard'),
            default  => route('dashboard'),
          }
        : route('home');
@endphp

{{-- Impersonation Notice Banner --}}
@if(session()->has('admin_impersonator_id'))
<div style="background:linear-gradient(90deg,#7c3aed,#db2777);color:#fff;font-size:13px;padding:8px 20px;display:flex;align-items:center;justify-content:space-between;gap:12px;">
    <span><i class="bi bi-shield-fill-check me-2"></i><strong>Admin Impersonation Active</strong> — Acting as <strong>{{ Auth::user()->name }}</strong></span>
    <a href="{{ route('admin.stop-impersonating') }}" class="btn btn-sm btn-light fw-semibold" style="color:#7c3aed;"><i class="bi bi-door-open me-1"></i> Exit Impersonation</a>
</div>
@endif

<nav class="navbar navbar-expand-lg navbar-sh">
    <div class="container">
        <a class="navbar-brand" href="{{ $brandHref }}">
            <i class="bi bi-hexagon-fill" style="font-size: 1.4rem; margin-right: 2px;"></i>
            SkillHive
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
            <i class="bi bi-list" style="font-size: 1.5rem;"></i>
        </button>

        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('explore') ? 'active' : '' }}" href="{{ route('explore') }}">Explore</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle {{ request()->routeIs('category') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Categories</a>
                    <ul class="dropdown-menu">
                        @foreach(\App\Models\Category::all() as $cat)
                            <li>
                                <a class="dropdown-item" href="{{ route('category', $cat['slug']) }}">
                                    <i class="{{ $cat['icon'] }} me-2 text-muted"></i>{{ $cat['name'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('how-it-works') ? 'active' : '' }}" href="{{ route('how-it-works') }}">How It Works</a>
                </li>
            </ul>

            <div class="navbar-nav-right">
                @if(Auth::check())
                    @php
                        $authUser = Auth::user();
                        $currentUserName = $authUser->name;
                        $currentUserAvatar = $authUser->avatar;
                        $currentUserCollege = $authUser->college ?: 'SkillHive Member';
                        $unreadNotifs = \App\Helpers\UserStore::unreadNotificationCount();
                        $notifications = \App\Helpers\UserStore::notifications();
                        $navRole  = $authUser->role;
                        $isAdmin  = $navRole === 'admin';
                        $isSeller = $navRole === 'seller';
                        $unreadMsgs = \App\Models\Message::where('receiver_id', $authUser->id)->where('is_read', false)->count();
                    @endphp

                    {{-- "Create Service" quick button — only for sellers --}}
                    @if($isSeller)
                    <a href="{{ route('seller.services.create') }}" class="btn-sh btn-sh-sm btn-sh-secondary d-none d-md-inline-flex me-2">
                        <i class="bi bi-plus-lg me-1"></i> Create Service
                    </a>
                    @endif

                    {{-- Messages Icon --}}
                    <a href="{{ route('chat.index') }}" class="nav-link px-2 d-inline-flex align-items-center text-dark me-2" title="Messages">
                        <div class="position-relative d-inline-flex align-items-center">
                            <i class="bi bi-chat-dots fs-5"></i>
                            @if($unreadMsgs > 0)
                                <span class="position-absolute badge rounded-pill bg-danger" style="font-size: 9px; padding: 2px 5px; top: -5px; right: -8px;">
                                    {{ $unreadMsgs }}
                                </span>
                            @endif
                        </div>
                    </a>

                    {{-- Notifications Dropdown --}}
                    <div class="dropdown me-2">
                        <a href="#" class="nav-link px-2 d-inline-flex align-items-center text-dark dropdown-toggle border-0 bg-transparent p-0" data-bs-toggle="dropdown" aria-expanded="false" title="Notifications">
                            <div class="position-relative d-inline-flex align-items-center">
                                <i class="bi bi-bell fs-5"></i>
                                @if($unreadNotifs > 0)
                                    <span class="position-absolute badge rounded-pill bg-danger" style="font-size: 9px; padding: 2px 5px; top: -5px; right: -8px;">
                                        {{ $unreadNotifs }}
                                    </span>
                                @endif
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end shadow-sm p-0" style="width: 330px; max-height: 420px; overflow-y: auto;">
                            <div class="p-3 border-bottom d-flex align-items-center justify-content-between bg-alt">
                                <div>
                                    <strong class="text-dark" style="font-size: var(--fs-sm);">Notifications</strong>
                                    @if($unreadNotifs > 0)
                                        <span class="badge bg-danger ms-1" style="font-size: 10px;">{{ $unreadNotifs }} new</span>
                                    @endif
                                </div>
                                @if($unreadNotifs > 0)
                                    <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="m-0 p-0 d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-link p-0 text-primary text-decoration-none" style="font-size: 11px; font-weight: 500;">
                                            Mark all read
                                        </button>
                                    </form>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 10px;">{{ count($notifications) }}</span>
                                @endif
                            </div>
                            @forelse($notifications as $n)
                                <a href="{{ $n['url'] }}" class="dropdown-item p-3 border-bottom text-wrap text-decoration-none {{ !$n['read'] ? 'bg-light' : '' }}" style="{{ !$n['read'] ? 'border-left: 3px solid var(--color-primary);' : '' }}">
                                    <div class="d-flex gap-2">
                                        <i class="bi {{ $n['icon'] }} text-primary fs-5 mt-1 flex-shrink-0"></i>
                                        <div class="flex-grow-1 min-w-0">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <strong class="d-block text-dark text-truncate" style="font-size: var(--fs-xs);">{{ $n['title'] }}</strong>
                                                @if(!$n['read'])
                                                    <span class="badge rounded-pill bg-primary" style="width: 7px; height: 7px; padding: 0;"></span>
                                                @endif
                                            </div>
                                            <span class="text-secondary d-block" style="font-size: 11px; line-height: 1.3;">{{ $n['message'] }}</span>
                                            <span class="text-muted d-block mt-1" style="font-size: 10px;">{{ $n['time'] }}</span>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="p-4 text-center text-muted" style="font-size: var(--fs-xs);">
                                    <i class="bi bi-bell-slash text-muted d-block mb-1 fs-4"></i>
                                    No notifications yet.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- User Profile Dropdown --}}
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle border-0 bg-transparent p-0 ms-1" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="{{ $currentUserAvatar }}" alt="{{ $currentUserName }}" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-primary);">
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li class="px-3 py-2 border-bottom">
                                <strong class="d-block text-dark">{{ $currentUserName }}</strong>
                                <span class="text-muted" style="font-size: 0.75rem;">
                                    {{ $currentUserCollege }}
                                    @if($isSeller) &nbsp;·&nbsp; <span class="text-success fw-semibold">Freelancer ✓</span> @endif
                                </span>
                            </li>

                            @if($isAdmin)
                                {{-- Admin links --}}
                                <li><h6 class="dropdown-header text-uppercase text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Admin Area</h6></li>
                                <li><a class="dropdown-item py-2" href="{{ route('admin.dashboard') }}"><i class="bi bi-shield-lock me-2 text-danger"></i> Admin Dashboard</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('admin.users') }}"><i class="bi bi-people me-2 text-danger"></i> Manage Users</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('admin.services') }}"><i class="bi bi-grid me-2 text-danger"></i> Manage Services</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('admin.orders') }}"><i class="bi bi-bag-check me-2 text-danger"></i> Manage Orders</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('admin.reviews') }}"><i class="bi bi-star-half me-2 text-danger"></i> Manage Reviews</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2" href="{{ route('dashboard') }}"><i class="bi bi-grid me-2 text-secondary"></i> View Client Dashboard</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('seller.dashboard') }}"><i class="bi bi-person-workspace me-2 text-secondary"></i> View Freelancer Dashboard</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @elseif($isSeller)
                                {{-- Seller / Freelancer links --}}
                                <li><h6 class="dropdown-header text-uppercase text-muted" style="font-size: 10px; letter-spacing: 0.5px;">Freelancer Area</h6></li>
                                <li><a class="dropdown-item py-2" href="{{ route('seller.dashboard') }}"><i class="bi bi-speedometer2 me-2 text-success"></i> Freelancer Dashboard</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('seller.services') }}"><i class="bi bi-journal-text me-2 text-success"></i> My Services</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('seller.services.create') }}"><i class="bi bi-plus-circle me-2 text-success"></i> Create New Service</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('seller.orders') }}"><i class="bi bi-bag-check me-2 text-success"></i> Client Orders</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('seller.portfolio') }}"><i class="bi bi-images me-2 text-success"></i> Portfolio Manager</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2" href="{{ route('chat.index') }}"><i class="bi bi-chat-dots me-2 text-info"></i> Messages</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2 text-secondary"></i> Profile Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @else
                                {{-- Regular Client / Buyer links --}}
                                <li><h6 class="dropdown-header text-uppercase text-muted" style="font-size: 10px; letter-spacing: 0.5px;">My Account</h6></li>
                                <li><a class="dropdown-item py-2" href="{{ route('dashboard') }}"><i class="bi bi-grid me-2 text-primary"></i> My Dashboard</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('orders.index') }}"><i class="bi bi-bag me-2 text-primary"></i> My Orders</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item py-2" href="{{ route('chat.index') }}"><i class="bi bi-chat-dots me-2 text-info"></i> Messages</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2 text-secondary"></i> Profile Settings</a></li>
                                <li><hr class="dropdown-divider"></li>
                            @endif

                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item py-2 text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('explore') }}" class="btn-sh btn-sh-sm btn-sh-secondary">Become a Seller</a>
                    <span class="nav-divider d-none d-lg-block"></span>
                    <a href="{{ route('login') }}" class="btn-sh btn-sh-sm btn-sh-white">Sign In</a>
                    <a href="{{ route('register') }}" class="btn-sh btn-sh-sm btn-sh-primary">Join SkillHive</a>
                @endif
            </div>
        </div>
    </div>
</nav>
