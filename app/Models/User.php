<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ListingApplicationUpdate;

class User extends Authenticatable implements MustVerifyEmail
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type', // job_hunter or employer
        'company',
        'bio',
        'website',
        'profile_image',
        'location',
        'cv',
        'phone',
    ];


    // The type of user
    public function isEmployer()
    {
        return $this->type === 'employer' ? true : false;
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'user_skill');
    }

    public function listings()
    {
        if ($this->isEmployer()) {
            return $this->hasMany(Listing::class);
        }
        return [];
    }

    public function applications()
    {
        if ($this->isEmployer()) {
            return [];
        }
        return $this->hasMany(ListingApplication::class);
    }

    public function alerts()
    {
        return $this->hasMany(Alert::class);
    }

    // Check if the user has completed their profile (for making applications)
    public function hasCompletedProfile()
    {
        return $this->skills()->count() > 0 && $this->cv !== null;
    }

    public function isInConversation($conversationId)
    {
        return $this->conversations()->where('conversation_id', $conversationId)->exists();
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function connections()
    {
        return $this->belongsToMany(User::class, 'connections', 'user_id', 'connected_user_id')
            ->withPivot('is_accepted')
            ->withTimestamps();
    }


    public function markNotificationAsRead($notificationId)
    {
        return $this->notifications()
            ->where('id', $notificationId)
            ->markAsRead();
    }

    public function canMessage($userId)
    {
        return $this->connections()
            ->where('connected_user_id', $userId)
            ->where('is_accepted', true)
            ->exists();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
        ];
    }
}
