<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
//model responsible for data structure of model.
// affects how data is stored in database.


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
}
