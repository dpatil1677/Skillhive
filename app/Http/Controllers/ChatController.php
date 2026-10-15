<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    /**
     * Display the chat interface.
     * Shows all people the current user has messaged or been messaged by.
     * Optionally opens a conversation with a specific user via ?with=userId
     * or starts a new one via ?start=userId.
     */
    public function index(Request $request)
    {
        $me = Auth::user();

        // Build list of unique conversation partners from DB
        $conversationUserIds = Message::where('sender_id', $me->id)
            ->orWhere('receiver_id', $me->id)
            ->get(['sender_id', 'receiver_id'])
            ->flatMap(fn($m) => [$m->sender_id, $m->receiver_id])
            ->unique()
            ->filter(fn($id) => $id !== $me->id)
            ->values();

        // Allow starting a new conversation with any valid user
        $startWith = (int) ($request->query('with') ?? $request->query('start') ?? 0);
        if ($startWith > 0 && $startWith !== $me->id) {
            $startUser = User::find($startWith);
            if ($startUser && !$conversationUserIds->contains($startWith)) {
                $conversationUserIds->prepend($startWith);
            }
        }

        // Build conversation list with last message preview & unread count
        $conversations = $this->buildConversationsList($me, $conversationUserIds);

        // Determine active partner
        $activeWithId = $startWith > 0 ? $startWith : ($conversations[0]['id'] ?? 0);
        $activeConv   = collect($conversations)->firstWhere('id', $activeWithId);

        // Load actual messages for the active conversation
        $activeMessages = [];
        if ($activeWithId && $activeConv) {
            // Mark incoming messages as read
            Message::where('sender_id', $activeWithId)
                ->where('receiver_id', $me->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            $rawMessages = Message::with(['sender', 'receiver'])
                ->where(function ($q) use ($me, $activeWithId) {
                    $q->where('sender_id', $me->id)->where('receiver_id', $activeWithId);
                })->orWhere(function ($q) use ($me, $activeWithId) {
                    $q->where('sender_id', $activeWithId)->where('receiver_id', $me->id);
                })->oldest()->get();

            $activeMessages = $rawMessages->map(fn($m) => $this->formatMessage($m, $me->id))->toArray();

            // Update the unread count in conversations list to 0 for active
            foreach ($conversations as &$c) {
                if ($c['id'] === $activeWithId) {
                    $c['unread'] = 0;
                }
            }
        }

        // All users list for starting a new chat
        $allUsers = User::where('id', '!=', $me->id)
            ->whereIn('role', ['user', 'seller'])
            ->get(['id', 'name', 'college', 'course', 'role', 'profile_image'])
            ->map(fn($u) => [
                'id'         => $u->id,
                'name'       => $u->name,
                'avatar'     => $u->avatar,
                'college'    => $u->college ?? 'SkillHive Member',
                'role_label' => $u->course ?? 'Student',
                'role'       => $u->role,
            ])->toArray();

        return view('chat.index', [
            'conversations'  => $conversations,
            'activeConv'     => $activeConv,
            'activeMessages' => $activeMessages,
            'activeWithId'   => $activeWithId,
            'allUsers'       => $allUsers,
            'me'             => $me,
        ]);
    }

    /**
     * Asynchronously fetch conversation messages and partner details (no page reload).
     */
    public function messages(Request $request)
    {
        $me = Auth::user();
        $withId = (int) $request->query('with');

        if (!$withId || $withId === $me->id) {
            return response()->json(['success' => false, 'error' => 'Invalid conversation partner.'], 422);
        }

        $partner = User::find($withId);
        if (!$partner) {
            return response()->json(['success' => false, 'error' => 'User not found.'], 404);
        }

        // Server-side Role Authorization Check
        if (!$this->canCommunicate($me, $partner)) {
            return response()->json(['success' => false, 'error' => 'Unauthorized conversation access.'], 403);
        }

        // Mark incoming messages as read
        Message::where('sender_id', $withId)
            ->where('receiver_id', $me->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        // Load conversation history
        $rawMessages = Message::with(['sender', 'receiver'])
            ->where(function ($q) use ($me, $withId) {
                $q->where('sender_id', $me->id)->where('receiver_id', $withId);
            })->orWhere(function ($q) use ($me, $withId) {
                $q->where('sender_id', $withId)->where('receiver_id', $me->id);
            })->oldest()->get();

        $activeMessages = $rawMessages->map(fn($m) => $this->formatMessage($m, $me->id))->toArray();
        $lastMsgId = $rawMessages->max('id') ?? 0;

        // Partner services & skills for right panel
        $partnerServices = Service::where('user_id', $partner->id)
            ->where('status', 'active')
            ->take(3)
            ->get(['id', 'title', 'slug', 'price', 'image'])
            ->map(fn($s) => [
                'id'    => $s->id,
                'title' => $s->title,
                'slug'  => $s->slug,
                'price' => $s->price,
                'image' => $s->image,
                'url'   => route('service.show', $s->slug),
            ]);

        $partnerSkills = is_array($partner->skills)
            ? $partner->skills
            : ($partner->skills ? json_decode($partner->skills, true) : []);

        return response()->json([
            'success'     => true,
            'partner'     => [
                'id'         => $partner->id,
                'name'       => $partner->name,
                'avatar'     => $partner->avatar,
                'college'    => $partner->college ?? 'SkillHive Member',
                'role_label' => ($partner->course ?? 'Student') . ' • ' . ($partner->college ?? 'SkillHive'),
                'role'       => $partner->role,
                'slug'       => Str::slug($partner->name),
                'profile_url'=> route('freelancer.show', Str::slug($partner->name)),
                'skills'     => $partnerSkills,
                'services'   => $partnerServices,
            ],
            'messages'    => $activeMessages,
            'last_msg_id' => $lastMsgId,
            'me'          => [
                'id'     => $me->id,
                'name'   => $me->name,
                'avatar' => $me->avatar,
                'role'   => $me->role,
            ],
        ]);
    }

    /**
     * Send a message — stores to DB, returns JSON for AJAX.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message'     => 'required|string|min:1|max:2000',
        ]);

        $me = Auth::user();
        $receiverId = (int) $request->input('receiver_id');

        if ($receiverId === $me->id) {
            return response()->json(['success' => false, 'error' => 'Cannot message yourself.'], 422);
        }

        $receiver = User::findOrFail($receiverId);

        // Server-side authorization: sender must be authorized to contact receiver
        if (!$this->canCommunicate($me, $receiver)) {
            return response()->json(['success' => false, 'error' => 'You are not authorized to send messages to this user.'], 403);
        }

        $text = trim($request->input('message'));

        $msg = Message::create([
            'sender_id'   => $me->id,
            'receiver_id' => $receiverId,
            'message'     => $text,
            'is_read'     => false,
        ]);

        $msg->load('sender', 'receiver');
        $formatted = $this->formatMessage($msg, $me->id);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $formatted,
                'last_msg_id' => $msg->id,
            ]);
        }

        return redirect()->route('chat.index', ['with' => $receiverId])
            ->with('toast_success', 'Message sent!');
    }

    /**
     * Poll for new messages since a given message ID and get live conversation updates.
     */
    public function poll(Request $request)
    {
        $me = Auth::user();
        if (!$me) {
            return response()->json(['error' => 'unauthenticated'], 401);
        }

        $withId  = (int) $request->query('with');
        $sinceId = (int) $request->query('since', 0);

        $newMessages = collect();

        if ($withId > 0 && $withId !== $me->id) {
            $partner = User::find($withId);
            if ($partner && $this->canCommunicate($me, $partner)) {
                $newMessages = Message::with(['sender', 'receiver'])
                    ->where(function ($q) use ($me, $withId) {
                        $q->where(function ($sub) use ($me, $withId) {
                            $sub->where('sender_id', $withId)->where('receiver_id', $me->id);
                        })->orWhere(function ($sub) use ($me, $withId) {
                            $sub->where('sender_id', $me->id)->where('receiver_id', $withId);
                        });
                    })
                    ->where('id', '>', $sinceId)
                    ->oldest()
                    ->get()
                    ->map(fn($m) => $this->formatMessage($m, $me->id));

                // Mark incoming from active partner as read
                if ($newMessages->count()) {
                    Message::where('sender_id', $withId)
                        ->where('receiver_id', $me->id)
                        ->where('is_read', false)
                        ->update(['is_read' => true]);
                }
            }
        }

        // Live conversation summaries for updating the left sidebar in real time
        $conversationUserIds = Message::where('sender_id', $me->id)
            ->orWhere('receiver_id', $me->id)
            ->get(['sender_id', 'receiver_id'])
            ->flatMap(fn($m) => [$m->sender_id, $m->receiver_id])
            ->unique()
            ->filter(fn($id) => $id !== $me->id)
            ->values();

        if ($withId > 0 && !$conversationUserIds->contains($withId)) {
            $conversationUserIds->prepend($withId);
        }

        $convList = $this->buildConversationsList($me, $conversationUserIds);

        // Find max message ID across new messages and current sinceId
        $maxMsgId = $newMessages->max('id') ?? $sinceId;

        return response()->json([
            'success'         => true,
            'messages'        => $newMessages->values(),
            'last_msg_id'     => $maxMsgId,
            'conversations'   => $convList,
            'active_with_id'  => $withId,
            'session_user_id' => $me->id,
        ]);
    }

    /**
     * Check if sender $me is authorized to communicate with $target.
     */
    private function canCommunicate(User $me, User $target): bool
    {
        // Admin can message any user or freelancer
        if ($me->role === 'admin' || $target->role === 'admin') {
            return true;
        }

        // Freelancers (sellers) and clients (users) can freely communicate
        if (($me->role === 'seller' && $target->role === 'user') ||
            ($me->role === 'user' && $target->role === 'seller') ||
            ($me->role === 'seller' && $target->role === 'seller')) {
            return true;
        }

        // Regular users with orders or existing chat history
        $hasOrder = Order::where(function ($q) use ($me, $target) {
            $q->where('client_id', $me->id)->where('freelancer_id', $target->id);
        })->orWhere(function ($q) use ($me, $target) {
            $q->where('client_id', $target->id)->where('freelancer_id', $me->id);
        })->exists();

        if ($hasOrder) {
            return true;
        }

        $hasHistory = Message::where(function ($q) use ($me, $target) {
            $q->where('sender_id', $me->id)->where('receiver_id', $target->id);
        })->orWhere(function ($q) use ($me, $target) {
            $q->where('sender_id', $target->id)->where('receiver_id', $me->id);
        })->exists();

        return $hasHistory;
    }

    /**
     * Helper to build conversation items with preview, unread count, and timestamp.
     */
    private function buildConversationsList(User $me, $partnerIds): array
    {
        return User::whereIn('id', $partnerIds)
            ->get()
            ->map(function (User $partner) use ($me) {
                $lastMsg = Message::where(function ($q) use ($me, $partner) {
                    $q->where('sender_id', $me->id)->where('receiver_id', $partner->id);
                })->orWhere(function ($q) use ($me, $partner) {
                    $q->where('sender_id', $partner->id)->where('receiver_id', $me->id);
                })->latest()->first();

                $unread = Message::where('sender_id', $partner->id)
                    ->where('receiver_id', $me->id)
                    ->where('is_read', false)
                    ->count();

                return [
                    'id'           => $partner->id,
                    'name'         => $partner->name,
                    'avatar'       => $partner->avatar,
                    'college'      => $partner->college ?? 'SkillHive Member',
                    'role_label'   => ($partner->course ?? 'Student') . ' • ' . ($partner->college ?? 'SkillHive'),
                    'online'       => false,
                    'unread'       => $unread,
                    'last_message' => $lastMsg?->message ?? 'Start a conversation…',
                    'last_time'    => $lastMsg ? $lastMsg->created_at->diffForHumans(null, true) : '',
                    'last_ts'      => $lastMsg?->created_at?->timestamp ?? 0,
                    'slug'         => Str::slug($partner->name),
                    'role'         => $partner->role,
                ];
            })
            ->sortByDesc('last_ts')
            ->values()
            ->toArray();
    }

    /**
     * Format message array for view and JSON.
     */
    private function formatMessage(Message $m, int $myId): array
    {
        $isMine = ($m->sender_id === $myId);
        $isAdmin = ($m->sender && $m->sender->role === 'admin');

        return [
            'id'       => $m->id,
            'sender'   => $isMine ? 'me' : 'them',
            'text'     => $m->message,
            'time'     => $m->created_at ? $m->created_at->format('g:i A') : date('g:i A'),
            'date'     => $m->created_at ? $m->created_at->format('M d') : date('M d'),
            'avatar'   => $m->sender ? $m->sender->avatar : 'https://ui-avatars.com/api/?name=User&background=6D28D9&color=fff',
            'name'     => $m->sender ? $m->sender->name : 'User',
            'is_admin' => $isAdmin,
            'role'     => $m->sender?->role ?? 'user',
        ];
    }
}
