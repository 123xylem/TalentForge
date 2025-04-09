<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ListingRequest extends FormRequest
{
  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'title' => 'required|string|max:255',
      'description' => 'required|string',
      'location' => 'required|string|max:255',
      'salary' => 'required|string|max:255',
      'skills' => 'required|array',
      'categories' => 'required|array',
      'company' => 'nullable|string|max:255',

    ];
  }
}
