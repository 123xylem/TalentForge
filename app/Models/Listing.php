<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ListingApplication;
use App\Models\User;


// the controller interacts with this by using the model to query the database.
class Listing extends Model
{
    // 
    protected $fillable = [
        'title',
        'slug',
        'description',
        'location',
        'company',
        'salary',
        'image',
        'url',
        'user_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class);
    }

    // When querying this Listing we can access its many skills
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'listing_skill');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'listing_category');
    }

    public function applications()
    {
        return $this->hasMany(ListingApplication::class);
    }

    public function applicants()
    {
        return $this->hasManyThrough(
            User::class,
            ListingApplication::class,
            'listing_id',  // Foreign key on listing_applications table
            'id',          // Foreign key on users table
            'id',          // Local key on listings table
            'user_id'      // Local key on listing_applications table
        )->select('users.*');  // Explicitly select user fields
    }
}
