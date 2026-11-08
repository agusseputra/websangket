<?php

namespace Database\Seeders;

use App\Models\Perumahan;
use Illuminate\Database\Seeder;

class PerumahanSeeder extends Seeder
{
    public function run(): void
    {
        $daftar = [
            'RT 1', 'RT 2', 'RT 3', 'RT 4 Pugpug', 'RT 4', 'RT 5', 'RT 6',
            'Krisna Graha 1', 'Krisna Graha 2', 'Griya Adi Jaya 1', 'Griya Adi Jaya 2',
            'Kanaya', 'Padi Permai', 'Griya Jro', 'Bukit Awangga', 'Baruna',
        ];

        foreach ($daftar as $nama) {
            Perumahan::firstOrCreate(['nama' => $nama]);
        }
    }
}
