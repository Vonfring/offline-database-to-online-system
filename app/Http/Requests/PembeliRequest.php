<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PembeliRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'kode_pembeli' => filled($this->kode_pembeli) ? strtoupper(trim($this->kode_pembeli)) : null,
        ]);
    }

    public function rules(): array
    {
        $pembeli = $this->route('pembeli');

        return [
            'kode_pembeli' => ['nullable', 'string', 'max:20', 'regex:/^[A-Z0-9\-]+$/', Rule::unique('pembeli')->ignore($pembeli)],
            'nama' => ['required', 'string', 'max:100'],
            'perusahaan' => ['nullable', 'string', 'max:100'],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_pembeli.regex' => 'Kode pembeli hanya boleh berisi huruf, angka, dan tanda hubung.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka, spasi, +, dan -.',
        ];
    }
}
