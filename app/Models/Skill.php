<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Skill extends Model
{
    //
    protected $fillable = ['name'];

    public function listings()
    {
        return $this->belongsToMany(Listing::class, 'listing_skill');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_skill');
    }
}
