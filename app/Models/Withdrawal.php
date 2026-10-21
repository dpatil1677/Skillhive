<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'reference_id',
        'amount',
        'method',
        'payout_details',
        'status',
        'admin_notes',
        'processed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payout_details' => 'array',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match(strtolower($this->status)) {
            'completed' => 'badge-sh-success',
            'processing' => 'badge-sh-primary',
            'rejected' => 'badge-sh-danger',
            default => 'badge-sh-warning',
        };
    }
}
