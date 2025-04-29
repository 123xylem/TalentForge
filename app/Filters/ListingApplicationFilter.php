<?php

namespace App\Filters;

class ListingApplicationFilter extends ListingFilter
{
  public function applicationStatus($value)
  {
    // return $this->builder->whereHas('listingApplications', function ($query) use ($value) {
    //   $query->where('status', $value);
    // });
    return $this->builder->where('status', $value);
  }
}
