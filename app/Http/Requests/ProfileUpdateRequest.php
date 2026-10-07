<?php

namespace App\Http\Requests;

use App\Models\Pengguna;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'nama' => ['required', 'string', 'max:100'],
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:100',
                Rule::unique(Pengguna::class)->ignore($this->user()->id),
            ],
        ];
    }
}
