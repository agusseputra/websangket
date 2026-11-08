<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_ditolak(): void
    {
        $this->getJson('/api/user')->assertUnauthorized();
        $this->getJson('/api/admin/ringkasan')->assertUnauthorized();
    }

    public function test_petugas_tidak_bisa_membuka_data_admin(): void
    {
        $petugas = User::factory()->create();

        $this->actingAs($petugas)->getJson('/api/admin/ringkasan')->assertForbidden();
        $this->actingAs($petugas)->getJson('/api/petugas/ringkasan')->assertOk();
    }

    public function test_admin_merangkap_akses_petugas(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->getJson('/api/admin/ringkasan')->assertOk();
        $this->actingAs($admin)->getJson('/api/petugas/ringkasan')->assertOk();
    }

    public function test_pengguna_yang_dinonaktifkan_ditolak(): void
    {
        $user = User::factory()->admin()->create(['aktif' => false]);

        $this->actingAs($user)->getJson('/api/admin/ringkasan')->assertForbidden();
    }
}
