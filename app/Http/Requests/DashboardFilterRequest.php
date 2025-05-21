<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DashboardFilterRequest extends FormRequest
{
  public function rules(): array
  {
    return [
      'textSearch' => 'nullable|string|max:255',
      'locationSearch' => 'nullable|string|max:255',
      'salary' => 'nullable|numeric',
      'category' => 'nullable|exists:categories,id',
      'skills' => 'nullable|array',
      'skills.*' => 'exists:skills,id',
      'applicationStatus' => 'nullable|in:unread,read,applied,shortlisted,rejected'
    ];
  }
}
