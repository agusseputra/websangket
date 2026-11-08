<?php

namespace Database\Factories;

use App\Models\LaporanWarga;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LaporanWarga>
 */
class LaporanWargaFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'nik' => fake()->numerify('################'),
            'asal' => fake()->city(),
            'jenis' => 'tinggal',
            'alamat' => fake()->streetAddress(),
            'tanggal_mulai' => '2026-11-01',
            'menetap' => true,
        ];
    }

    public function usaha(): static
    {
        return $this->state(fn (array $attributes) => [
            'jenis' => 'usaha',
            'nama_usaha' => fake()->company(),
            'jenis_usaha' => 'Warung',
        ]);
    }
}
