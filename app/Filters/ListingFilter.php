<?php

namespace App\Filters;

use Illuminate\Http\Request;

class ListingFilter extends QueryFilter
{
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function category($value)
    {
        return $this->builder->where('category_id', $value);
    }

    public function skills($value)
    {
        return $this->builder->whereHas('skills', function ($query) use ($value) {
            $query->whereIn('skill_id', $value);
        }, '=', count($value));
    }

    public function salary($value)
    {
        $salary = (int)$value * 1000;
        return $this->builder->where('salary', '>=', $salary);
    }

    public function locationSearch($value)
    {
        return $this->builder->where('location', 'LIKE', '%' . $value . '%');
    }

    public function textSearch($value)
    {
        return $this->builder->where(function ($query) use ($value) {
            $query->where('title', 'LIKE', '%' . $value . '%')
                ->orWhere('description', 'LIKE', '%' . $value . '%');
        });
    }
}
