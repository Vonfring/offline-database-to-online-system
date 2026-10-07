<?php

namespace App\Http\Requests;

use App\Models\Supplier;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'kode_supplier' => filled($this->kode_supplier) ? strtoupper(trim($this->kode_supplier)) : null,
        ]);
    }

    public function rules(): array
    {
        $supplier = $this->route('supplier');

        return [
            'kode_supplier' => ['nullable', 'string', 'max:20', 'regex:/^[A-Z0-9\-]+$/', Rule::unique('supplier')->ignore($supplier)],
            'nama' => ['required', 'string', 'max:100'],
            'kategori' => ['nullable', Rule::in(Supplier::DAFTAR_KATEGORI)],
            'no_hp' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]+$/'],
            'alamat' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'kode_supplier.regex' => 'Kode supplier hanya boleh berisi huruf, angka, dan tanda hubung.',
            'no_hp.regex' => 'Nomor HP hanya boleh berisi angka, spasi, +, dan -.',
        ];
    }
}
