<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'client_id',
        'freelancer_id',
        'service_id',
        'package',
        'price',
        'platform_fee',
        'total_amount',
        'requirements',
        'deliverables_note',
        'deliverables_link',
        'deliverables_file',
        'submitted_at',
        'status',
        'delivery_date',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'platform_fee' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'delivery_date' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    protected $appends = [
        'formatted_status',
        'timeline',
        'due_date',
        'client_name',
        'client_avatar',
        'package_name',
        'reviewed',
    ];

    public function getStatusAttribute($value): string
    {
        $v = strtolower($value ?? 'pending');
        return match ($v) {
            'accepted' => 'Accepted',
            'in_progress' => 'In Progress',
            'submitted' => 'Submitted',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            default => 'Pending',
        };
    }

    public function getRawStatusAttribute(): string
    {
        return strtolower($this->getRawOriginal('status') ?? 'pending');
    }

    public function getFormattedStatusAttribute(): string
    {
        return $this->status;
    }

    public function getTimelineAttribute(): array
    {
        $st = $this->raw_status;
        $isCancelled = ($st === 'cancelled');

        return [
            [
                'title' => 'Order Placed',
                'completed' => !$isCancelled,
                'time' => $this->created_at ? $this->created_at->format('M d, Y h:i A') : 'Just now',
            ],
            [
                'title' => 'Accepted by Freelancer',
                'completed' => in_array($st, ['accepted', 'in_progress', 'submitted', 'completed']),
                'time' => in_array($st, ['accepted', 'in_progress', 'submitted', 'completed']) ? 'Confirmed' : 'Awaiting confirmation',
            ],
            [
                'title' => 'In Progress',
                'completed' => in_array($st, ['in_progress', 'submitted', 'completed']),
                'time' => in_array($st, ['in_progress', 'submitted', 'completed']) ? 'Active' : 'Pending',
            ],
            [
                'title' => 'Work Submitted',
                'completed' => in_array($st, ['submitted', 'completed']),
                'time' => in_array($st, ['submitted', 'completed']) ? 'Deliverables uploaded' : 'Pending',
            ],
            [
                'title' => 'Completed & Approved',
                'completed' => $st === 'completed',
                'time' => $st === 'completed' ? 'Approved & Funds Released' : 'Pending',
            ],
        ];
    }

    public function getDueDateAttribute(): string
    {
        return $this->delivery_date ? $this->delivery_date->format('M d, Y') : 'In 5 days';
    }

    public function getClientNameAttribute(): string
    {
        if ($this->relationLoaded('client') && $this->client) {
            return $this->client->name;
        }
        return $this->client ? $this->client->name : 'Verified Buyer';
    }

    public function getClientAvatarAttribute(): string
    {
        if ($this->relationLoaded('client') && $this->client) {
            return $this->client->avatar;
        }
        return $this->client ? $this->client->avatar : 'https://ui-avatars.com/api/?name=Buyer&background=6D28D9&color=fff&size=64';
    }

    public function getPackageNameAttribute(): string
    {
        return $this->package ?: 'Standard';
    }

    public function getReviewedAttribute(): bool
    {
        return Review::where('order_id', $this->id)->exists();
    }

    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
