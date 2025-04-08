<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $fillable = ['name'];

    public function listings()
    {
        return $this->belongsToMany(Listing::class, 'listing_category');
    }

    // Get the parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Get all child categories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Get all child categories and their children recursively
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    // Get the parent category and its parent recursively
    public function parentRecursive()
    {
        return $this->parent()->with('parentRecursive');
    }

    // Scope to get only top-level categories
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }
}
