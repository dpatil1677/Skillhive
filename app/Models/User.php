<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_image',
        'college',
        'course',
        'bio',
        'skills',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'skills' => 'array',
        ];
    }

    public function getSlugAttribute(): string
    {
        return \Illuminate\Support\Str::slug($this->name);
    }

    public function getAvatarAttribute(): string
    {
        return $this->profile_image ?: 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=6D28D9&color=fff&size=128';
    }

    public function getVerifiedAttribute(): bool
    {
        return true;
    }

    public function toFreelancerArray(): array
    {
        $revCount = $this->reviewsAsFreelancer()->count();
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'avatar' => $this->avatar,
            'college' => $this->college ?: 'SkillHive Member',
            'course' => $this->course ?: 'Student Member',
            'skill' => $this->course ?: (is_array($this->skills) && !empty($this->skills) ? $this->skills[0] : 'Student Freelancer'),
            'bio' => $this->bio ?: '',
            'skills' => is_array($this->skills) ? $this->skills : [],
            'verified' => true,
            'level' => 'Verified Student Seller',
            'rating' => 4.9,
            'reviews' => $revCount ?: 12,
            'reviews_count' => $revCount ?: 12,
            'orders_count' => 24,
            'completed_orders' => 24,
            'response_time' => '1 hour',
            'member_since' => $this->created_at ? $this->created_at->format('M Y') : 'Jan 2024',
            'languages' => ['English', 'Hindi'],
        ];
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function portfolios()
    {
        return $this->hasMany(Portfolio::class);
    }

    public function ordersAsClient()
    {
        return $this->hasMany(Order::class, 'client_id');
    }

    public function ordersAsFreelancer()
    {
        return $this->hasMany(Order::class, 'freelancer_id');
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function reviewsAsReviewer()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

    public function reviewsAsFreelancer()
    {
        return $this->hasMany(Review::class, 'freelancer_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    /**
     * Send custom SkillHive password reset notification.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new \App\Notifications\ResetPasswordNotification($token));
    }
}
