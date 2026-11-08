<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('sangket.admin.email');
        $password = config('sangket.admin.password');

        if (blank($email) || blank($password)) {
            $this->command?->warn('ADMIN_EMAIL atau ADMIN_PASSWORD kosong, akun admin tidak dibuat.');

            return;
        }

        // Hanya dibuat sekali agar seeding ulang tidak menimpa kata sandi yang sudah diganti.
        User::firstOrCreate(['email' => $email], [
            'name' => 'Admin',
            'password' => $password,
            'role' => Role::Admin,
            'aktif' => true,
        ]);
    }
}
