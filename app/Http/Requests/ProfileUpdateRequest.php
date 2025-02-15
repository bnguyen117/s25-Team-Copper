<?php

namespace App\Http\Requests;

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
            'name' => ['required', 'string', 'max:255'],
            'display_name' => ['required', 'string', 'max:255'], // New: Display Name Validation
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'birthdate' => ['required', 'date', 'before_or_equal:' . now()->subYears(13)->format('Y-m-d')], // New: Birthdate (Must be at least 13 years old)
            'avatar' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'], // New: Avatar File Validation
        ];
    }
}