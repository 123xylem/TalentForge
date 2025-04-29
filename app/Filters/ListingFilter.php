<?php

namespace App\Filters;

class ListingFilter extends QueryFilter
{
    public function category($value)
    {
        return $this->builder->where('category_id', $value);
    }

    public function skills($value)
    {
        return $this->builder->whereHas('skills', function ($query) use ($value) {
            $query->whereIn('skill_id', $value);
        }, '>=', count($value));
    }

    public function salary($value)
    {
        return $this->builder->whereRaw(
            "CAST(REGEXP_REPLACE(salary, '[^0-9]', '') AS UNSIGNED) >= ?",
            [$value]
        );
    }

    public function location($value)
    {
        return $this->builder->where('location', 'LIKE', '%' . $value . '%');
    }

    public function title($value)
    {
        return $this->builder->where('title', 'LIKE', '%' . $value . '%')->orWhere('description', 'LIKE', '%' . $value . '%');
    }
}
