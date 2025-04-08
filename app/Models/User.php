<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{

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
        return $this->hasMany(Application::class);
    }



    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'type', // job_hunter or employer
        'company',
        'bio',
        'website',
        'profile_image',
        'location',
        'cv',
        'phone',
    ];
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
