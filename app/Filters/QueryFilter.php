<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class QueryFilter
{
  protected $request;
  protected $builder;

  public function __construct($request)
  {
    $this->request = $request;
  }
  //Makes a QUERY builder by getting the request and applying the filters
  public function apply(Builder $builder)
  {
    $this->builder = $builder;

    foreach ($this->filters() as $filter => $value) {
      if (method_exists($this, $filter)) {
        $this->$filter($value);
      }
    }

    return $this->builder;
  }

  public function filters()
  {
    return $this->request->all();
  }
}
