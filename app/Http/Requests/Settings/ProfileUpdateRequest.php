<?php

namespace App\Http\Requests\Settings;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'company' => ['nullable', 'string', 'max:255'],
            'type' => 'required|string|in:job_hunter,employer',
            'bio' => 'nullable|string|max:255|min:8',
            'website' => 'nullable|string|max:255|min:3',
            'location' => ['nullable', 'string', 'max:255'],
            'profile_image' => 'nullable|file|mimes:jpg,png,pdf,jpeg,webp|max:2048',
            'cv' => ['nullable', 'file', 'mimes:jpg,png,pdf,doc,docx|max:2048'],
            'phone' => ['nullable', 'string', 'max:255'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
        ];
    }
}
