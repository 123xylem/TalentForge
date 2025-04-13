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
    protected $appUrl;

    public function __construct()
    {
        $this->appUrl = config('app.url');
    }

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
            'profile_image' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (is_string($value)) {
                        // If it's a string, ensure it's a valid path or URL
                        if (!filter_var($this->appUrl . $value, FILTER_VALIDATE_URL) && !file_exists($value)) {
                            $fail($attribute . ' is not a valid file path or URL.');
                        }
                    } elseif (is_file($value)) {
                        // If it's a file, apply file validation rules
                        $this->validateFile($value, $attribute, $fail);
                    }
                },
            ],
            'cv' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if (is_string($value)) {
                        if (!filter_var($this->appUrl . $value, FILTER_VALIDATE_URL) && !file_exists($value)) {
                            $fail($attribute . ' is not a valid file path or URL.');
                        }
                    } elseif (is_file($value)) {
                        $this->validateFile($value, $attribute, $fail);
                    }
                },
            ],
            'phone' => ['nullable', 'string', 'max:255'],
            'skills' => ['nullable', 'array'],
            'skills.*' => ['exists:skills,id'],
        ];
    }
    protected function validateFile($value, $attribute, $fail)
    {
        $rules = ['file', 'mimes:jpg,png,pdf,jpeg,webp', 'max:4048'];
        $validator = \Validator::make([$attribute => $value], [$attribute => $rules]);

        if ($validator->fails()) {
            $fail($validator->errors()->first($attribute));
        }
    }
}
