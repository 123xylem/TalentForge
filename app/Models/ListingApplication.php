<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListingApplication extends Model
{
    protected $fillable = [
        'user_id',
        'cv',
        'cover_letter',
        'status',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class);
    }


    //Get ID of all applicants
    public function applicant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeFilter($query, $filters)
    {
        return $filters->apply($query);
    }
}
