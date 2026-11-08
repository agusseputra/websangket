<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    private const SPA = ['Referer' => 'http://localhost:8000/login', 'Accept' => 'application/json'];

    public function test_pengguna_aktif_bisa_login_dan_logout(): void
    {
        $user = User::factory()->admin()->create(['password' => 'rahasia123']);

        $this->withHeaders(self::SPA)
            ->postJson('/api/login', ['email' => $user->email, 'password' => 'rahasia123'])
            ->assertOk()
            ->assertJson(['email' => $user->email, 'role' => 'admin'])
            ->assertJsonMissingPath('password');

        $this->assertAuthenticatedAs($user, 'web');

        $this->withHeaders(self::SPA)->postJson('/api/logout')->assertNoContent();
        $this->assertGuest('web');
    }

    public function test_kata_sandi_salah_ditolak(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123']);

        $this->withHeaders(self::SPA)
            ->postJson('/api/login', ['email' => $user->email, 'password' => 'salah'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('email');
    }

    public function test_pengguna_nonaktif_tidak_bisa_login(): void
    {
        $user = User::factory()->create(['password' => 'rahasia123', 'aktif' => false]);

        $this->withHeaders(self::SPA)
            ->postJson('/api/login', ['email' => $user->email, 'password' => 'rahasia123'])
            ->assertUnprocessable();
    }

    public function test_login_dibatasi_setelah_lima_percobaan(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->withHeaders(self::SPA)->postJson('/api/login', ['email' => 'a@b.c', 'password' => 'x']);
        }

        $this->withHeaders(self::SPA)
            ->postJson('/api/login', ['email' => 'a@b.c', 'password' => 'x'])
            ->assertTooManyRequests();
    }
}
