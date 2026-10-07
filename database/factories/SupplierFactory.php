<?php

namespace Database\Factories;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Supplier>
 */
class SupplierFactory extends Factory
{
    protected $model = Supplier::class;

    public function definition(): array
    {
        return [
            'kode_supplier' => 'SUP-'.fake()->unique()->numerify('9###'),
            'nama' => 'Pengepul '.fake()->unique()->firstName(),
            'kategori' => fake()->randomElement(Supplier::DAFTAR_KATEGORI),
            'no_hp' => '08'.fake()->numerify('##########'),
            'alamat' => fake()->streetAddress().', '.fake()->city(),
        ];
    }
}
