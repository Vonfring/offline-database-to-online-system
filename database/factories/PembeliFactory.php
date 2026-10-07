<?php

namespace Database\Factories;

use App\Models\Pembeli;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pembeli>
 */
class PembeliFactory extends Factory
{
    protected $model = Pembeli::class;

    public function definition(): array
    {
        return [
            'kode_pembeli' => 'PBL-'.fake()->unique()->numerify('9###'),
            'nama' => fake()->unique()->name(),
            'perusahaan' => 'PT '.fake()->company(),
            'no_hp' => '08'.fake()->numerify('##########'),
            'alamat' => fake()->streetAddress().', '.fake()->city(),
        ];
    }
}
