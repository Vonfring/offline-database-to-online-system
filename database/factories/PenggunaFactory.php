<?php

namespace Database\Factories;

use App\Models\Pengguna;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Pengguna>
 */
class PenggunaFactory extends Factory
{
    protected $model = Pengguna::class;

    protected static ?string $password;

    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'peran' => Pengguna::PERAN_STAF,
            'aktif' => true,
        ];
    }

    public function admin(): static
    {
        return $this->state(['peran' => Pengguna::PERAN_ADMIN]);
    }

    public function nonaktif(): static
    {
        return $this->state(['aktif' => false]);
    }
}
