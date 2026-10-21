@extends('layouts.guest')

@section('title', 'Messages — SkillHive')
@section('meta_description', 'Chat with freelancers and clients on SkillHive marketplace.')

@push('styles')
<style>
/* ─── Chat Layout ─────────────────────────────────────────────── */
.chat-wrapper {
    display: flex;
    height: calc(100vh - 140px);
    min-height: 560px;
    background: #fff;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid var(--color-border);
}

/* ─── Sidebar ─────────────────────────────────────────────────── */
.chat-sidebar {
    width: 320px;
    flex-shrink: 0;
    display: flex;
    flex-direction: column;
    border-right: 1px solid var(--color-border);
    background: var(--color-bg);
}
.chat-sidebar-header {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--color-border);
    background: #fff;
}
.chat-search-input {
    width: 100%;
    padding: .5rem .75rem .5rem 2.25rem;
    border: 1px solid var(--color-border);
    border-radius: var(--radius-full);
    font-size: var(--fs-sm);
    background: var(--color-bg-alt);
    outline: none;
    transition: border-color .15s;
}
.chat-search-input:focus { border-color: var(--color-primary); background: #fff; }
.chat-search-wrap { position: relative; }
.chat-search-wrap .bi-search {
    position: absolute; left: .75rem; top: 50%; transform: translateY(-50%);
    color: var(--color-muted); font-size: .8rem;
}

.conv-list { flex: 1; overflow-y: auto; }
.conv-item {
    display: flex; align-items: center; gap: .75rem;
    padding: .875rem 1.25rem;
    text-decoration: none; color: var(--color-text);
    border-bottom: 1px solid var(--color-border);
    transition: background .15s;
    cursor: pointer;
}
.conv-item:hover { background: var(--color-bg-alt); }
.conv-item.active {
    background: var(--color-primary-subtle);
    border-left: 3px solid var(--color-primary);
}
.conv-avatar-wrap { position: relative; flex-shrink: 0; }
.conv-avatar {
    width: 44px; height: 44px; border-radius: 50%; object-fit: cover;
    border: 2px solid var(--color-border);
}
.conv-item.active .conv-avatar { border-color: var(--color-primary); }
.online-dot {
    position: absolute; bottom: 1px; right: 1px;
    width: 11px; height: 11px; background: #22c55e;
    border: 2px solid #fff; border-radius: 50%;
}
.conv-meta { flex: 1; min-width: 0; }
.conv-name {
    font-size: var(--fs-sm); font-weight: var(--fw-semibold);
    color: var(--color-text); white-space: nowrap;
    overflow: hidden; text-overflow: ellipsis;
}
.conv-preview {
    font-size: var(--fs-xs); color: var(--color-text-secondary);
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    margin-top: 2px;
}
.conv-time { font-size: 10px; color: var(--color-muted); white-space: nowrap; }
.unread-badge {
    background: var(--color-primary); color: #fff;
    font-size: 10px; font-weight: 600;
    min-width: 18px; height: 18px; border-radius: 999px;
    display: inline-flex; align-items: center; justify-content: center;
    padding: 0 5px; flex-shrink: 0;
}

/* ─── New Chat Button ─────────────────────────────────────────── */
.new-chat-btn {
    margin: .75rem 1.25rem;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    padding: .5rem; border-radius: var(--radius-md);
    font-size: var(--fs-sm); font-weight: var(--fw-medium);
    color: var(--color-primary); background: var(--color-primary-subtle);
    border: 1.5px dashed var(--color-primary-light);
    cursor: pointer; text-decoration: none; transition: all .15s;
}
.new-chat-btn:hover { background: var(--color-primary-light); color: var(--color-primary); }

/* ─── Main Chat Area ──────────────────────────────────────────── */
.chat-main {
    flex: 1; display: flex; flex-direction: column;
    min-width: 0; background: var(--color-bg-alt);
    position: relative;
}
.chat-main-header {
    padding: .875rem 1.25rem;
    background: #fff; border-bottom: 1px solid var(--color-border);
    display: flex; align-items: center; justify-content: space-between;
    gap: 1rem;
}
.chat-partner-info { display: flex; align-items: center; gap: .75rem; }
.chat-partner-avatar {
    width: 42px; height: 42px; border-radius: 50%; object-fit: cover;
    border: 2px solid var(--color-primary);
}
.chat-partner-name { font-size: var(--fs-md); font-weight: var(--fw-bold); margin: 0; line-height: 1.2; }
.chat-partner-sub { font-size: var(--fs-xs); color: var(--color-text-secondary); }

/* ─── Messages Log ────────────────────────────────────────────── */
.chat-messages-log {
    flex: 1; overflow-y: auto; padding: 1.5rem 1.25rem;
    display: flex; flex-direction: column; gap: .25rem;
}
.msg-date-divider {
    text-align: center; margin: .75rem 0;
    font-size: 11px; color: var(--color-muted); position: relative;
}
.msg-date-divider::before, .msg-date-divider::after {
    content: ''; position: absolute; top: 50%; height: 1px;
    background: var(--color-border); width: calc(50% - 60px);
}
.msg-date-divider::before { left: 0; }
.msg-date-divider::after  { right: 0; }

.msg-row {
    display: flex; align-items: flex-end; gap: .5rem;
    margin-bottom: .375rem;
    width: 100%;
}
.msg-row.me  { justify-content: flex-end; }
.msg-row.them { justify-content: flex-start; }

.msg-avatar-sm {
    width: 28px; height: 28px; border-radius: 50%; object-fit: cover;
    flex-shrink: 0; opacity: 1;
}

.msg-bubble-wrap {
    max-width: 72%;
    min-width: 0;
    display: flex;
    flex-direction: column;
}
.msg-row.me .msg-bubble-wrap {
    align-items: flex-end;
}
.msg-row.them .msg-bubble-wrap {
    align-items: flex-start;
}

.msg-bubble {
    display: inline-block;
    width: fit-content;
    max-width: 100%;
    min-width: 32px;
    padding: .5rem .875rem;
    border-radius: 18px;
    font-size: var(--fs-sm);
    line-height: 1.45;
    word-break: normal;
    overflow-wrap: break-word;
    white-space: pre-wrap;
    box-shadow: 0 1px 2px rgba(0,0,0,.06);
    text-align: left;
}
.msg-row.me  .msg-bubble {
    background: var(--color-primary); color: #fff;
    border-bottom-right-radius: 4px;
}
.msg-row.them .msg-bubble {
    background: #fff; color: var(--color-text);
    border-bottom-left-radius: 4px; border: 1px solid var(--color-border);
}
.msg-time {
    font-size: 10px; margin-top: 3px;
    opacity: .65; white-space: nowrap;
}
.msg-row.me .msg-time { text-align: right; }
.msg-row.them .msg-time { text-align: left; }

/* ─── Chat Input ──────────────────────────────────────────────── */
.chat-input-area {
    background: #fff; border-top: 1px solid var(--color-border);
    padding: .875rem 1.25rem;
}
.chat-input-form {
    display: flex; align-items: center; gap: .5rem;
}
.chat-input-field {
    flex: 1; padding: .625rem 1rem;
    border: 1.5px solid var(--color-border); border-radius: var(--radius-full);
    font-size: var(--fs-sm); outline: none; transition: border-color .15s;
    background: var(--color-bg-alt);
    font-family: inherit;
    resize: none;
}
.chat-input-field:focus { border-color: var(--color-primary); background: #fff; }
.chat-send-btn {
    width: 42px; height: 42px; border-radius: 50%;
    background: var(--color-primary); color: #fff; border: none;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; transition: all .15s; flex-shrink: 0;
    font-size: 1rem;
}
.chat-send-btn:hover { background: var(--color-primary-dark); transform: scale(1.05); }
.chat-send-btn.sending { opacity: .6; pointer-events: none; }

/* ─── Right Profile Panel ─────────────────────────────────────── */
.chat-profile-panel {
    width: 260px; flex-shrink: 0;
    background: #fff; border-left: 1px solid var(--color-border);
    display: flex; flex-direction: column; overflow-y: auto;
}
.profile-panel-header {
    padding: 1.5rem 1.25rem 1rem;
    text-align: center; border-bottom: 1px solid var(--color-border);
}
.profile-panel-avatar {
    width: 72px; height: 72px; border-radius: 50%; object-fit: cover;
    border: 3px solid var(--color-primary); margin: 0 auto .75rem;
    display: block;
}
.profile-panel-name { font-size: var(--fs-md); font-weight: var(--fw-bold); margin: 0 0 2px; }
.profile-panel-sub  { font-size: var(--fs-xs); color: var(--color-text-secondary); }
.profile-panel-body { padding: 1rem 1.25rem; }
.profile-stat-row   { display: flex; flex-direction: column; gap: .75rem; }
.profile-stat-item  {
    display: flex; align-items: center; gap: .625rem;
    font-size: var(--fs-xs); color: var(--color-text-secondary);
}
.profile-stat-icon  {
    width: 28px; height: 28px; border-radius: var(--radius-sm);
    background: var(--color-primary-subtle); color: var(--color-primary);
    display: flex; align-items: center; justify-content: center;
    font-size: .85rem; flex-shrink: 0;
}
.skill-chip {
    display: inline-block; padding: 2px 8px;
    background: var(--color-primary-subtle); color: var(--color-primary);
    border-radius: var(--radius-full); font-size: 11px; font-weight: 500;
    margin: 2px;
}
.seller-badge-pill {
    display: inline-flex; align-items: center; gap: 4px;
    background: #dcfce7; color: #166534;
    font-size: 11px; font-weight: 600; padding: 3px 10px;
    border-radius: var(--radius-full); margin-top: 6px;
}

/* ─── Empty / No Selection State ─────────────────────────────── */
.chat-empty-state {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    gap: 1rem; color: var(--color-muted); text-align: center; padding: 2rem;
}
.chat-empty-icon { font-size: 3.5rem; opacity: .25; }

/* ─── Loading Overlay ─────────────────────────────────────────── */
.chat-loading-overlay {
    position: absolute; inset: 0; background: rgba(255,255,255,0.7);
    display: flex; align-items: center; justify-content: center;
    z-index: 10; backdrop-filter: blur(2px);
}

/* ─── New Chat Modal ──────────────────────────────────────────── */
.user-search-item {
    display: flex; align-items: center; gap: .75rem;
    padding: .625rem .75rem; border-radius: var(--radius-md);
    cursor: pointer; transition: background .12s;
}
.user-search-item:hover { background: var(--color-bg-alt); }

/* ─── Responsive ──────────────────────────────────────────────── */
@media (max-width: 900px) {
    .chat-profile-panel { display: none; }
}
@media (max-width: 640px) {
    .chat-sidebar { width: 72px; }
    .conv-meta, .conv-time, .unread-badge, .new-chat-btn span { display: none; }
    .conv-item { justify-content: center; padding: .75rem .5rem; }
    .chat-wrapper { height: calc(100vh - 120px); }
}

/* Typing dots animation */
.typing-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--color-muted); display: inline-block;
    animation: typingBounce 1s infinite ease-in-out;
}
@keyframes typingBounce {
    0%, 80%, 100% { transform: translateY(0); }
    40%           { transform: translateY(-5px); }
}
</style>
@endpush

@section('content')
<div class="container" style="padding-top: 1.5rem; padding-bottom: 1.5rem;">

    {{-- Page Title --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <div>
            <h1 style="font-size: var(--fs-xl); font-weight: var(--fw-bold); margin: 0;">
                <i class="bi bi-chat-dots-fill text-primary me-2"></i>Messages
            </h1>
            <p class="text-secondary mb-0" style="font-size: var(--fs-xs);">
                Chat directly with freelancers and clients in real time
            </p>
        </div>
        <button class="btn-sh btn-sh-primary btn-sh-sm" data-bs-toggle="modal" data-bs-target="#newChatModal">
            <i class="bi bi-pencil-square me-1"></i> New Message
        </button>
    </div>

    {{-- Chat Wrapper --}}
    <div class="chat-wrapper">

        {{-- ─── LEFT SIDEBAR: Conversations ─── --}}
        <aside class="chat-sidebar">
            <div class="chat-sidebar-header">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <strong style="font-size: var(--fs-sm);">Conversations</strong>
                    <span class="badge bg-primary rounded-pill" id="convTotalCount" style="font-size: 10px;">{{ count($conversations) }}</span>
                </div>
                <div class="chat-search-wrap">
                    <i class="bi bi-search"></i>
                    <input type="text" class="chat-search-input" id="convSearch" placeholder="Search chats…">
                </div>
            </div>

            <div class="conv-list" id="convList">
                @forelse($conversations as $conv)
                    <div class="conv-item {{ (isset($activeConv) && $activeConv['id'] == $conv['id']) ? 'active' : '' }}"
                         data-id="{{ $conv['id'] }}"
                         data-name="{{ strtolower($conv['name']) }}"
                         role="button"
                         tabindex="0"
                         onclick="openConversation({{ $conv['id'] }}, event)">
                        <div class="conv-avatar-wrap">
                            <img src="{{ $conv['avatar'] }}" alt="{{ $conv['name'] }}" class="conv-avatar">
                            @if($conv['online'])
                                <span class="online-dot"></span>
                            @endif
                        </div>
                        <div class="conv-meta">
                            <div class="d-flex align-items-center justify-content-between gap-1">
                                <span class="conv-name">{{ $conv['name'] }}</span>
                                <span class="conv-time">{{ $conv['last_time'] }}</span>
                            </div>
                            <div class="conv-preview">{{ $conv['last_message'] }}</div>
                        </div>
                        @if($conv['unread'] > 0)
                            <span class="unread-badge">{{ $conv['unread'] }}</span>
                        @endif
                    </div>
                @empty
                    <div class="p-4 text-center text-muted" id="convListEmpty" style="font-size: var(--fs-xs);">
                        <i class="bi bi-chat-square-dots d-block fs-3 mb-2 opacity-25"></i>
                        No conversations yet.<br>Start one below!
                    </div>
                @endforelse
            </div>

            <a href="#" class="new-chat-btn" data-bs-toggle="modal" data-bs-target="#newChatModal">
                <i class="bi bi-pencil-square"></i>
                <span>New Message</span>
            </a>
        </aside>

        {{-- ─── CENTRE: Message Thread ─── --}}
        <main class="chat-main" id="chatMain">

            {{-- Loading spinner overlay (hidden by default) --}}
            <div id="chatLoadingOverlay" class="chat-loading-overlay" style="display:none;">
                <div class="spinner-border text-primary" role="status" style="width: 2rem; height: 2rem;">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>

            {{-- Active conversation view --}}
            <div id="activeThreadWrap" style="{{ ($activeConv && $activeWithId) ? 'display:flex; flex-direction:column; height:100%; min-width:0;' : 'display:none; height:100%; min-width:0;' }}">
                {{-- Header --}}
                <div class="chat-main-header" id="chatHeader">
                    <div class="chat-partner-info">
                        <div class="position-relative">
                            <img src="{{ $activeConv['avatar'] ?? '' }}" alt="{{ $activeConv['name'] ?? '' }}" id="chatPartnerAvatar" class="chat-partner-avatar">
                            <span id="chatPartnerOnlineDot" class="position-absolute bottom-0 end-0" style="width:11px;height:11px;background:#22c55e;border:2px solid #fff;border-radius:50%; display:none;"></span>
                        </div>
                        <div>
                            <p class="chat-partner-name d-flex align-items-center gap-2 mb-0" id="chatPartnerName">
                                {{ $activeConv['name'] ?? '' }}
                                @if(($activeConv['role'] ?? '') === 'admin')
                                    <span class="badge bg-danger text-white" id="chatPartnerAdminBadge" style="font-size:10px;"><i class="bi bi-shield-fill-check me-1"></i>SkillHive Admin</span>
                                @endif
                            </p>
                            <span class="chat-partner-sub" id="chatPartnerSub">
                                {{ $activeConv['role_label'] ?? '' }}
                                @if(($activeConv['role'] ?? '') === 'seller')
                                    &nbsp;·&nbsp; <span style="color:#16a34a; font-weight:600;">Freelancer</span>
                                @elseif(($activeConv['role'] ?? '') === 'admin')
                                    &nbsp;·&nbsp; <span style="color:#ef4444; font-weight:600;">Platform Administrator</span>
                                @endif
                            </span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn-sh btn-sh-outline btn-sh-xs" id="startVideoCallBtn" onclick="launchVideoCall()" title="Start Instant Live Video Call">
                            <i class="bi bi-camera-video-fill text-primary me-1"></i> Video Call
                        </button>
                    </div>
                </div>

                {{-- Messages Log --}}
                <div class="chat-messages-log" id="chatMessagesLog">
                    @php
                        $prevDate = null;
                        $lastMsgId = 0;
                    @endphp
                    @forelse($activeMessages as $msg)
                        @php
                            $msgDate = \Carbon\Carbon::parse($msg['date']);
                            $dateLabel = $msgDate->isToday() ? 'Today' : ($msgDate->isYesterday() ? 'Yesterday' : $msg['date']);
                            if ($lastMsgId < $msg['id']) $lastMsgId = $msg['id'];
                        @endphp

                        @if($prevDate !== $dateLabel)
                            <div class="msg-date-divider">{{ $dateLabel }}</div>
                            @php $prevDate = $dateLabel; @endphp
                        @endif

                        <div class="msg-row {{ $msg['sender'] }}" data-msg-id="{{ $msg['id'] }}">
                            @if($msg['sender'] === 'them')
                                <img src="{{ $msg['avatar'] }}" alt="{{ $msg['name'] }}" class="msg-avatar-sm" title="{{ $msg['name'] }}">
                            @endif
                            <div class="msg-bubble-wrap">
                                @if(!empty($msg['is_admin']))
                                    <div class="mb-1 {{ $msg['sender'] === 'me' ? 'text-end' : '' }}">
                                        <span class="badge bg-danger text-white" style="font-size:9px; padding:2px 6px;">
                                            <i class="bi bi-shield-fill-check me-1"></i>SkillHive Admin
                                        </span>
                                    </div>
                                @endif
                                <div class="msg-bubble {{ !empty($msg['is_admin']) ? 'border border-danger' : '' }}">
                                    @php
                                        $safeText = e($msg['text']);
                                        $safeText = preg_replace('/(https:\/\/meet\.jit\.si\/[^\s]+)/', '<br><a href="$1" target="_blank" class="btn btn-sm btn-light py-1 px-2 my-1 d-inline-flex align-items-center gap-1 border shadow-sm fw-bold text-primary text-decoration-none" onclick="event.preventDefault(); openLiveMeeting(\'$1\');"><i class="bi bi-camera-video-fill"></i> Join Video Call</a>', $safeText);
                                        $safeText = preg_replace('/(https?:\/\/(?!meet\.jit\.si)[^\s]+)/', '<a href="$1" target="_blank" class="text-decoration-underline">$1</a>', $safeText);
                                    @endphp
                                    {!! nl2br($safeText) !!}
                                </div>
                                <div class="msg-time">{{ $msg['time'] }}</div>
                            </div>
                            @if($msg['sender'] === 'me')
                                <img src="{{ Auth::user()->avatar }}" alt="Me" class="msg-avatar-sm">
                            @endif
                        </div>
                    @empty
                        <div class="chat-empty-state" id="chatEmptyState">
                            <i class="bi bi-chat-heart chat-empty-icon"></i>
                            <p style="font-size: var(--fs-sm);">No messages yet. Say hello! 👋</p>
                        </div>
                    @endforelse

                    {{-- Typing indicator placeholder --}}
                    <div id="typingIndicator" class="msg-row them" style="display:none;">
                        <img src="{{ $activeConv['avatar'] ?? '' }}" id="typingIndicatorAvatar" class="msg-avatar-sm">
                        <div class="msg-bubble-wrap">
                            <div class="msg-bubble" style="padding: .5rem .875rem;">
                                <span style="display:inline-flex;gap:3px;align-items:center;">
                                    <span class="typing-dot"></span>
                                    <span class="typing-dot" style="animation-delay:.15s"></span>
                                    <span class="typing-dot" style="animation-delay:.3s"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Input Area --}}
                <div class="chat-input-area">
                    <form class="chat-input-form" id="chatForm" autocomplete="off">
                        @csrf
                        <input type="hidden" name="receiver_id" id="receiverIdInput" value="{{ $activeWithId }}">
                        <input type="hidden" id="lastMsgId" value="{{ $lastMsgId }}">
                        <input type="text"
                               name="message"
                               id="chatInput"
                               class="chat-input-field"
                               placeholder="Type a message…"
                               required
                               autocomplete="off"
                               maxlength="2000">
                        <button type="submit" class="chat-send-btn" id="chatSendBtn" title="Send">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </form>
                </div>
            </div>

            {{-- No conversation selected fallback --}}
            <div id="noConversationState" class="chat-empty-state" style="{{ ($activeConv && $activeWithId) ? 'display:none;' : 'display:flex;' }}">
                <i class="bi bi-chat-square-dots chat-empty-icon"></i>
                <h5 style="color: var(--color-text); font-weight: var(--fw-semibold);">Your Messages</h5>
                <p style="font-size: var(--fs-sm); max-width: 260px;">Select a conversation from the left, or start a new one to connect with a freelancer or client.</p>
                <button class="btn-sh btn-sh-primary btn-sh-sm" data-bs-toggle="modal" data-bs-target="#newChatModal">
                    <i class="bi bi-pencil-square me-1"></i> Start a Conversation
                </button>
            </div>

        </main>

        {{-- ─── RIGHT PANEL: Partner Profile ─── --}}
        <aside class="chat-profile-panel" id="chatProfilePanel" style="{{ ($activeConv && $activeWithId) ? 'display:flex;' : 'display:none;' }}">
            <div class="profile-panel-header">
                <img src="{{ $activeConv['avatar'] ?? '' }}" alt="{{ $activeConv['name'] ?? '' }}" id="profileAvatar" class="profile-panel-avatar">
                <p class="profile-panel-name" id="profileName">{{ $activeConv['name'] ?? '' }}</p>
                <span class="profile-panel-sub" id="profileSub">{{ $activeConv['role_label'] ?? '' }}</span>

                <div class="mt-1" id="profileBadgeWrap" style="{{ (($activeConv['role'] ?? '') === 'seller') ? '' : 'display:none;' }}">
                    <span class="seller-badge-pill"><i class="bi bi-patch-check-fill"></i> Verified Freelancer</span>
                </div>

                <div class="mt-3">
                    <a href="{{ isset($activeConv) ? route('freelancer.show', $activeConv['slug']) : '#' }}" id="profileViewBtn" class="btn-sh btn-sh-primary btn-sh-sm w-100">
                        <i class="bi bi-person-badge me-1"></i> View Full Profile
                    </a>
                </div>
            </div>

            <div class="profile-panel-body">
                @php
                    $partnerUser = isset($activeConv['id']) ? \App\Models\User::find($activeConv['id']) : null;
                    $partnerServices = $partnerUser ? \App\Models\Service::where('user_id', $partnerUser->id)->where('status', 'active')->take(3)->get() : collect();
                    $partnerSkills = $partnerUser && is_array($partnerUser->skills) ? $partnerUser->skills : [];
                @endphp

                <div class="profile-stat-row">
                    <div class="profile-stat-item">
                        <div class="profile-stat-icon"><i class="bi bi-mortarboard"></i></div>
                        <div>
                            <div style="font-weight: 600; color: var(--color-text); font-size: var(--fs-xs);" id="profileCollege">{{ $activeConv['college'] ?? 'SkillHive Member' }}</div>
                            <div>College</div>
                        </div>
                    </div>

                    <div class="profile-stat-item" id="profileServicesCountItem" style="{{ $partnerServices->count() ? '' : 'display:none;' }}">
                        <div class="profile-stat-icon"><i class="bi bi-journal-text"></i></div>
                        <div>
                            <div style="font-weight: 600; color: var(--color-text); font-size: var(--fs-xs);" id="profileServicesCountText">{{ $partnerServices->count() }} Active Service{{ $partnerServices->count() > 1 ? 's' : '' }}</div>
                            <div>On SkillHive</div>
                        </div>
                    </div>

                    <div class="profile-stat-item">
                        <div class="profile-stat-icon"><i class="bi bi-star-fill" style="color: #f59e0b;"></i></div>
                        <div>
                            <div style="font-weight: 600; color: var(--color-text); font-size: var(--fs-xs);">4.9 / 5.0 Rating</div>
                            <div>Seller Score</div>
                        </div>
                    </div>
                </div>

                <div class="mt-3" id="profileSkillsSection" style="{{ count($partnerSkills) ? '' : 'display:none;' }}">
                    <div style="font-size: var(--fs-xs); font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: .5px; margin-bottom: .5rem;">Skills</div>
                    <div id="profileSkillsList">
                        @foreach($partnerSkills as $skill)
                            <span class="skill-chip">{{ $skill }}</span>
                        @endforeach
                    </div>
                </div>

                <div class="mt-3" id="profileServicesSection" style="{{ $partnerServices->count() ? '' : 'display:none;' }}">
                    <div style="font-size: var(--fs-xs); font-weight: 600; color: var(--color-text-secondary); text-transform: uppercase; letter-spacing: .5px; margin-bottom: .5rem;">Services</div>
                    <div id="profileServicesList">
                        @foreach($partnerServices as $svc)
                        <a href="{{ route('service.show', $svc->slug) }}" class="d-flex align-items-center gap-2 mb-2 text-decoration-none p-2 rounded" style="background: var(--color-bg-alt); border-radius: var(--radius-sm);">
                            <img src="{{ $svc->image }}" style="width:36px;height:26px;object-fit:cover;border-radius:4px;flex-shrink:0;">
                            <div style="min-width:0;">
                                <div class="text-truncate" style="font-size:11px;font-weight:600;color:var(--color-text);">{{ Str::limit($svc->title, 32) }}</div>
                                <div style="font-size:10px;color:var(--color-primary);font-weight:600;">₹{{ number_format($svc->price) }}</div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

    </div>{{-- /.chat-wrapper --}}
</div>

{{-- ═══ New Chat Modal ═══ --}}
<div class="modal fade" id="newChatModal" tabindex="-1" aria-labelledby="newChatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-header border-bottom">
                <h5 class="modal-title" id="newChatModalLabel">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>New Message
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="p-3 border-bottom">
                    <div class="chat-search-wrap">
                        <i class="bi bi-search"></i>
                        <input type="text" id="userSearchInput" class="chat-search-input" placeholder="Search freelancers or users…">
                    </div>
                </div>
                <div id="userSearchResults" style="max-height: 320px; overflow-y: auto;">
                    @foreach($allUsers as $u)
                    <div class="user-search-item"
                         role="button"
                         tabindex="0"
                         data-id="{{ $u['id'] }}"
                         data-name="{{ strtolower($u['name']) }}"
                         onclick="selectUserChat({{ $u['id'] }})">
                        <img src="{{ $u['avatar'] }}" style="width:40px;height:40px;border-radius:50%;object-fit:cover;flex-shrink:0;">
                        <div style="min-width:0;">
                            <div style="font-size:var(--fs-sm);font-weight:600;">{{ $u['name'] }}</div>
                            <div style="font-size:var(--fs-xs);color:var(--color-text-secondary);">
                                {{ $u['role_label'] }} · {{ $u['college'] }}
                                @if($u['role'] === 'seller') <span style="color:#16a34a;font-weight:600;">· Freelancer</span> @endif
                            </div>
                        </div>
                        <i class="bi bi-arrow-right-circle text-primary ms-auto"></i>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
{{-- ═══ Video Call Modal ═══ --}}
<div class="modal fade" id="videoCallModal" tabindex="-1" aria-labelledby="videoCallModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: var(--radius-lg); overflow: hidden; background: #0f172a;">
            <div class="modal-header border-0 bg-dark text-white py-2 px-3">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-circle p-1"><i class="bi bi-camera-video-fill"></i></span>
                    <h6 class="modal-title mb-0" id="videoCallModalLabel">Live Video Room: <span id="callRoomTitle">SkillHive Live Meeting</span></h6>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="callExternalLink" target="_blank" class="btn btn-sm btn-outline-light" title="Open full screen in new tab">
                        <i class="bi bi-box-arrow-up-right me-1"></i> New Tab
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="closeVideoCall()"></button>
                </div>
            </div>
            <div class="modal-body p-0" style="height: 600px; background: #000;">
                <iframe id="videoCallFrame" src="" allow="camera; microphone; fullscreen; display-capture; autoplay" style="width: 100%; height: 100%; border: none;"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    /* ── State & Constants ── */
    const myUserId   = {{ Auth::id() }};
    const myAvatar   = '{{ Auth::user()->avatar }}';
    const csrfToken  = document.querySelector('meta[name="csrf-token"]')?.content;
    let currentPartnerAvatar = '{{ $activeConv["avatar"] ?? "" }}';
    let isPolling    = false;
    let pollIntervalMs = 2500;
    let pollTimer    = null;

    /* ── DOM References ── */
    const log             = document.getElementById('chatMessagesLog');
    const form            = document.getElementById('chatForm');
    const input           = document.getElementById('chatInput');
    const sendBtn         = document.getElementById('chatSendBtn');
    const lastMsgEl       = document.getElementById('lastMsgId');
    const receiverIdInput = document.getElementById('receiverIdInput');
    const loadingOverlay  = document.getElementById('chatLoadingOverlay');
    const activeThread    = document.getElementById('activeThreadWrap');
    const noConvState     = document.getElementById('noConversationState');
    const convListEl      = document.getElementById('convList');

    /* ── Scroll to bottom ── */
    function scrollToBottom(smooth = false) {
        if (!log) return;
        log.scrollTo({ top: log.scrollHeight, behavior: smooth ? 'smooth' : 'auto' });
    }
    scrollToBottom(false);

    /* ── HTML Escape & URL Formatting ── */
    function escapeHtml(s) {
        if (!s) return '';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function formatChatText(text) {
        if (!text) return '';
        const escaped = escapeHtml(text);
        let formatted = escaped.replace(/(https:\/\/meet\.jit\.si\/[^\s]+)/g, function(url) {
            return `<br><a href="${url}" target="_blank" class="btn btn-sm btn-light py-1 px-2 my-1 d-inline-flex align-items-center gap-1 border shadow-sm fw-bold text-primary text-decoration-none" onclick="event.preventDefault(); openLiveMeeting('${url}');"><i class="bi bi-camera-video-fill"></i> Join Video Call</a>`;
        });
        formatted = formatted.replace(/(https?:\/\/(?!meet\.jit\.si)[^\s]+)/g, '<a href="$1" target="_blank" class="text-decoration-underline">$1</a>');
        return formatted.replace(/\n/g, '<br>');
    }

    /* ── Video Call Modal Controls ── */
    window.launchVideoCall = function () {
        const receiverId = receiverIdInput?.value;
        if (!receiverId) return;

        const btn = document.getElementById('startVideoCallBtn');
        if (btn) btn.disabled = true;

        fetch('{{ route("chat.meeting.start") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ receiver_id: receiverId })
        })
        .then(res => res.json())
        .then(data => {
            if (btn) btn.disabled = false;
            if (data.status === 'success') {
                if (data.message) appendMessage(data.message);
                openLiveMeeting(data.room_url);
            } else {
                alert(data.message || 'Could not start meeting.');
            }
        })
        .catch(err => {
            if (btn) btn.disabled = false;
            console.error(err);
            alert('Unable to launch video meeting.');
        });
    };

    window.openLiveMeeting = function (url) {
        const iframe = document.getElementById('videoCallFrame');
        const extLink = document.getElementById('callExternalLink');
        const modalEl = document.getElementById('videoCallModal');
        if (iframe) iframe.src = url + '#config.prejoinPageEnabled=false';
        if (extLink) extLink.href = url;
        if (modalEl && typeof bootstrap !== 'undefined') {
            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }
    };

    window.closeVideoCall = function () {
        const iframe = document.getElementById('videoCallFrame');
        if (iframe) iframe.src = '';
    };

    /* ── Get / Update lastMsgId safely ── */
    function getLastMsgId() {
        return parseInt(lastMsgEl?.value || '0', 10) || 0;
    }
    function updateLastMsgId(id) {
        const num = parseInt(id, 10);
        if (!isNaN(num) && num > getLastMsgId()) {
            if (lastMsgEl) lastMsgEl.value = num;
        }
    }

    /* ── Append a new message bubble cleanly (prevents duplicates) ── */
    function appendMessage(msg) {
        if (!msg || !log) return;

        // Hide empty state if present
        const emptyState = document.getElementById('chatEmptyState');
        if (emptyState) emptyState.remove();

        const numId = parseInt(msg.id, 10);

        // Deduplication: if message with real ID already rendered, update lastMsgId and return
        if (numId && log.querySelector(`[data-msg-id="${numId}"]`)) {
            updateLastMsgId(numId);
            return;
        }

        // Reconcile optimistic temp bubble if incoming message is mine
        if (msg.sender === 'me' && numId) {
            const tempBubble = log.querySelector('[data-msg-id^="tmp-"]');
            if (tempBubble) {
                const bubbleText = tempBubble.querySelector('.msg-bubble')?.textContent?.trim();
                if (bubbleText === msg.text?.trim()) {
                    tempBubble.dataset.msgId = numId;
                    const timeEl = tempBubble.querySelector('.msg-time');
                    if (timeEl && msg.time) timeEl.textContent = msg.time;
                    updateLastMsgId(numId);
                    return;
                }
            }
        }

        const isMine = (msg.sender === 'me');
        const row = document.createElement('div');
        row.className = 'msg-row ' + msg.sender;
        row.dataset.msgId = msg.id || '';

        const themAvatar = currentPartnerAvatar || msg.avatar || '';
        const adminBadgeHtml = msg.is_admin ? `
            <div class="mb-1 ${isMine ? 'text-end' : ''}">
                <span class="badge bg-danger text-white" style="font-size:9px; padding:2px 6px;">
                    <i class="bi bi-shield-fill-check me-1"></i>SkillHive Admin
                </span>
            </div>` : '';

        row.innerHTML = `
            ${!isMine ? `<img src="${themAvatar}" class="msg-avatar-sm" title="${escapeHtml(msg.name || '')}">` : ''}
            <div class="msg-bubble-wrap">
                ${adminBadgeHtml}
                <div class="msg-bubble ${msg.is_admin ? 'border border-danger' : ''}">${formatChatText(msg.text)}</div>
                <div class="msg-time">${escapeHtml(msg.time || '')}</div>
            </div>
            ${isMine ? `<img src="${myAvatar}" class="msg-avatar-sm" alt="Me">` : ''}
        `;

        const typing = document.getElementById('typingIndicator');
        if (typing && typing.parentNode === log) {
            log.insertBefore(row, typing);
        } else {
            log.appendChild(row);
        }

        if (numId) updateLastMsgId(numId);
        scrollToBottom(true);
    }

    /* ── Update left sidebar conversations in real time ── */
    function updateSidebarConversations(conversations) {
        if (!Array.isArray(conversations) || !convListEl) return;

        const currentActiveId = parseInt(receiverIdInput?.value || '0', 10);

        conversations.forEach(c => {
            let item = convListEl.querySelector(`.conv-item[data-id="${c.id}"]`);

            if (item) {
                // Update preview & time
                const previewEl = item.querySelector('.conv-preview');
                const timeEl    = item.querySelector('.conv-time');
                if (previewEl && c.last_message) previewEl.textContent = c.last_message;
                if (timeEl && c.last_time) timeEl.textContent = c.last_time;

                // Update unread badge
                let badge = item.querySelector('.unread-badge');
                if (c.id === currentActiveId) {
                    if (badge) badge.remove();
                } else if (c.unread > 0) {
                    if (!badge) {
                        badge = document.createElement('span');
                        badge.className = 'unread-badge';
                        item.appendChild(badge);
                    }
                    badge.textContent = c.unread;
                } else if (badge) {
                    badge.remove();
                }

                // If active or has unread, move to top
                if (c.unread > 0 || c.id === currentActiveId) {
                    if (convListEl.firstElementChild !== item) {
                        convListEl.insertBefore(item, convListEl.firstElementChild);
                    }
                }
            }
        });
    }

    /* ── Poll for new messages every 2.5s ── */
    async function pollMessages() {
        if (isPolling) return;
        const receiverId = parseInt(receiverIdInput?.value || '0', 10);
        const since = getLastMsgId();

        isPolling = true;
        try {
            const url = `{{ route('chat.poll') }}?with=${receiverId}&since=${since}`;
            const resp = await fetch(url, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });

            if (resp.status === 401) {
                // Session expired or logged out
                clearInterval(pollTimer);
                return;
            }

            const data = await resp.json();

            // Guard against session change in other tab
            if (data.session_user_id && data.session_user_id !== myUserId) {
                return;
            }

            // Append new messages
            if (data.messages && data.messages.length) {
                data.messages.forEach(m => appendMessage(m));
            }

            // Sync sidebar
            if (data.conversations) {
                updateSidebarConversations(data.conversations);
            }

            if (data.last_msg_id) {
                updateLastMsgId(data.last_msg_id);
            }
        } catch (_) {
            // Network glitch; poller will retry automatically
        } finally {
            isPolling = false;
        }
    }

    function startPolling() {
        if (pollTimer) clearInterval(pollTimer);
        pollTimer = setInterval(pollMessages, pollIntervalMs);
    }
    startPolling();

    // Pause/slow polling when tab is hidden, resume immediately when visible
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            clearInterval(pollTimer);
            pollTimer = setInterval(pollMessages, 10000); // 10s backoff in background
        } else {
            clearInterval(pollTimer);
            pollMessages(); // Poll immediately on return
            startPolling();
        }
    });

    /* ── Send Message via AJAX without reloading ── */
    if (form) {
        form.addEventListener('submit', async function (e) {
            e.preventDefault();
            const text = input.value.trim();
            const receiverId = parseInt(receiverIdInput?.value || '0', 10);
            if (!text || !receiverId) return;

            sendBtn.classList.add('sending');
            const tempId = 'tmp-' + Date.now();

            // Optimistic bubble
            appendMessage({
                id: tempId,
                sender: 'me',
                text: text,
                time: new Date().toLocaleTimeString('en-IN', { hour: 'numeric', minute: '2-digit' })
            });

            input.value = '';
            input.focus();

            try {
                const resp = await fetch('{{ route("chat.send") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ receiver_id: receiverId, message: text }),
                });

                const data = await resp.json();
                if (data.success && data.message?.id) {
                    const realId = data.message.id;
                    const tmpEl = log.querySelector(`[data-msg-id="${tempId}"]`);
                    if (tmpEl) tmpEl.dataset.msgId = realId;
                    updateLastMsgId(realId);

                    // Update sidebar item preview
                    const activeItem = convListEl.querySelector(`.conv-item[data-id="${receiverId}"]`);
                    if (activeItem) {
                        const previewEl = activeItem.querySelector('.conv-preview');
                        const timeEl = activeItem.querySelector('.conv-time');
                        if (previewEl) previewEl.textContent = text;
                        if (timeEl) timeEl.textContent = 'Just now';
                        convListEl.insertBefore(activeItem, convListEl.firstElementChild);
                    }
                } else if (data.error) {
                    if (typeof showToast === 'function') showToast(data.error, 'error');
                }
            } catch (err) {
                console.warn('Chat send error:', err);
                if (typeof showToast === 'function') showToast('Message delivery failed. Please check your connection.', 'error');
            } finally {
                sendBtn.classList.remove('sending');
            }
        });

        /* Enter sends, Shift+Enter creates newline */
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.requestSubmit();
            }
        });
    }

    /* ── Asynchronously switch conversations (NO FULL PAGE RELOAD) ── */
    window.openConversation = async function(partnerId, event) {
        if (event) event.preventDefault();
        if (!partnerId) return;

        // Highlight active conversation in sidebar
        document.querySelectorAll('#convList .conv-item').forEach(el => {
            if (parseInt(el.dataset.id, 10) === partnerId) {
                el.classList.add('active');
                const badge = el.querySelector('.unread-badge');
                if (badge) badge.remove();
            } else {
                el.classList.remove('active');
            }
        });

        // Update URL cleanly via history.pushState without reloading
        window.history.pushState(null, '', `{{ route('chat.index') }}?with=${partnerId}`);

        // Show main thread, hide no-conv state
        if (noConvState) noConvState.style.display = 'none';
        if (activeThread) activeThread.style.display = 'flex';
        if (loadingOverlay) loadingOverlay.style.display = 'flex';

        if (receiverIdInput) receiverIdInput.value = partnerId;

        try {
            const resp = await fetch(`{{ route('chat.messages') }}?with=${partnerId}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
            });
            const data = await resp.json();

            if (!data.success) {
                if (typeof showToast === 'function') showToast(data.error || 'Failed to load conversation.', 'error');
                return;
            }

            const p = data.partner;
            currentPartnerAvatar = p.avatar;

            // 1. Update Header
            const partnerAvatarEl = document.getElementById('chatPartnerAvatar');
            const partnerNameEl   = document.getElementById('chatPartnerName');
            const partnerSubEl    = document.getElementById('chatPartnerSub');

            if (partnerAvatarEl) partnerAvatarEl.src = p.avatar;
            if (partnerNameEl) {
                partnerNameEl.innerHTML = `
                    ${escapeHtml(p.name)}
                    ${p.role === 'admin' ? '<span class="badge bg-danger text-white" style="font-size:10px;"><i class="bi bi-shield-fill-check me-1"></i>SkillHive Admin</span>' : ''}
                `;
            }
            if (partnerSubEl) {
                partnerSubEl.innerHTML = `
                    ${escapeHtml(p.role_label)}
                    ${p.role === 'seller' ? '&nbsp;·&nbsp; <span style="color:#16a34a; font-weight:600;">Freelancer</span>' : ''}
                    ${p.role === 'admin' ? '&nbsp;·&nbsp; <span style="color:#ef4444; font-weight:600;">Platform Administrator</span>' : ''}
                `;
            }

            // 2. Update Right Profile Panel
            const profilePanel = document.getElementById('chatProfilePanel');
            if (profilePanel) {
                profilePanel.style.display = 'flex';
                const pAvatar = document.getElementById('profileAvatar');
                const pName   = document.getElementById('profileName');
                const pSub    = document.getElementById('profileSub');
                const pBadge  = document.getElementById('profileBadgeWrap');
                const pBtn    = document.getElementById('profileViewBtn');
                const pCollege = document.getElementById('profileCollege');
                const pSkillsWrap = document.getElementById('profileSkillsSection');
                const pSkillsList = document.getElementById('profileSkillsList');
                const pServicesWrap = document.getElementById('profileServicesSection');
                const pServicesList = document.getElementById('profileServicesList');

                if (pAvatar) pAvatar.src = p.avatar;
                if (pName) pName.textContent = p.name;
                if (pSub) pSub.textContent = p.role_label;
                if (pBadge) pBadge.style.display = (p.role === 'seller') ? '' : 'none';
                if (pBtn && p.profile_url) pBtn.href = p.profile_url;
                if (pCollege) pCollege.textContent = p.college || 'SkillHive Member';

                if (pSkillsWrap && pSkillsList) {
                    if (p.skills && p.skills.length) {
                        pSkillsWrap.style.display = '';
                        pSkillsList.innerHTML = p.skills.map(s => `<span class="skill-chip">${escapeHtml(s)}</span>`).join('');
                    } else {
                        pSkillsWrap.style.display = 'none';
                    }
                }

                if (pServicesWrap && pServicesList) {
                    if (p.services && p.services.length) {
                        pServicesWrap.style.display = '';
                        pServicesList.innerHTML = p.services.map(s => `
                            <a href="${s.url}" class="d-flex align-items-center gap-2 mb-2 text-decoration-none p-2 rounded" style="background: var(--color-bg-alt); border-radius: var(--radius-sm);">
                                <img src="${s.image}" style="width:36px;height:26px;object-fit:cover;border-radius:4px;flex-shrink:0;">
                                <div style="min-width:0;">
                                    <div class="text-truncate" style="font-size:11px;font-weight:600;color:var(--color-text);">${escapeHtml(s.title)}</div>
                                    <div style="font-size:10px;color:var(--color-primary);font-weight:600;">₹${Number(s.price).toLocaleString('en-IN')}</div>
                                </div>
                            </a>
                        `).join('');
                    } else {
                        pServicesWrap.style.display = 'none';
                    }
                }
            }

            // 3. Clear Log & Render Messages
            if (log) {
                log.innerHTML = '';
                if (!data.messages || data.messages.length === 0) {
                    log.innerHTML = `
                        <div class="chat-empty-state" id="chatEmptyState">
                            <i class="bi bi-chat-heart chat-empty-icon"></i>
                            <p style="font-size: var(--fs-sm);">No messages yet. Say hello! 👋</p>
                        </div>
                    `;
                } else {
                    let prevDate = null;
                    data.messages.forEach(m => {
                        const dateLabel = m.date || 'Today';
                        if (prevDate !== dateLabel) {
                            const div = document.createElement('div');
                            div.className = 'msg-date-divider';
                            div.textContent = dateLabel;
                            log.appendChild(div);
                            prevDate = dateLabel;
                        }
                        appendMessage(m);
                    });
                }

                // Re-add typing indicator
                const typing = document.createElement('div');
                typing.id = 'typingIndicator';
                typing.className = 'msg-row them';
                typing.style.display = 'none';
                typing.innerHTML = `
                    <img src="${p.avatar}" class="msg-avatar-sm">
                    <div class="msg-bubble-wrap">
                        <div class="msg-bubble" style="padding: .5rem .875rem;">
                            <span style="display:inline-flex;gap:3px;align-items:center;">
                                <span class="typing-dot"></span>
                                <span class="typing-dot" style="animation-delay:.15s"></span>
                                <span class="typing-dot" style="animation-delay:.3s"></span>
                            </span>
                        </div>
                    </div>
                `;
                log.appendChild(typing);
            }

            if (lastMsgEl) lastMsgEl.value = data.last_msg_id || 0;
            scrollToBottom(false);
            if (input) { input.value = ''; input.focus(); }

        } catch (err) {
            console.error('Error switching conversation:', err);
        } finally {
            if (loadingOverlay) loadingOverlay.style.display = 'none';
        }
    };

    /* ── Select user from New Chat Modal ── */
    window.selectUserChat = function(userId) {
        const modalEl = document.getElementById('newChatModal');
        if (modalEl && typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
        }
        window.openConversation(userId);
    };

    /* ── Search Filters ── */
    const convSearch = document.getElementById('convSearch');
    if (convSearch) {
        convSearch.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#convList .conv-item').forEach(item => {
                const name = item.dataset.name || '';
                item.style.display = name.includes(q) ? '' : 'none';
            });
        });
    }

    const userSearch = document.getElementById('userSearchInput');
    if (userSearch) {
        userSearch.addEventListener('input', function () {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('#userSearchResults .user-search-item').forEach(item => {
                const name = item.dataset.name || '';
                item.style.display = name.includes(q) ? '' : 'none';
            });
        });
        document.getElementById('newChatModal')?.addEventListener('shown.bs.modal', () => userSearch.focus());
    }

})();
</script>
@endpush
