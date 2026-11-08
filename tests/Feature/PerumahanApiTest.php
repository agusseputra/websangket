<?php

namespace Tests\Feature;

use App\Models\Krama;
use App\Models\Perumahan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PerumahanApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_mengelola_perumahan_dan_nama_harus_unik(): void
    {
        $admin = User::factory()->admin()->create();

        $id = $this->actingAs($admin)->postJson('/api/perumahan', ['nama' => 'RT 7'])->assertCreated()->json('id');
        $this->actingAs($admin)->postJson('/api/perumahan', ['nama' => 'RT 7'])->assertUnprocessable();
        $this->actingAs($admin)->putJson("/api/perumahan/{$id}", ['nama' => 'RT 8'])->assertOk()->assertJsonPath('nama', 'RT 8');
        $this->actingAs($admin)->putJson("/api/perumahan/{$id}", ['nama' => 'RT 8'])->assertOk();
        $this->actingAs($admin)->deleteJson("/api/perumahan/{$id}")->assertNoContent();
    }

    public function test_admin_menyimpan_mengubah_dan_mengosongkan_titik_koordinat(): void
    {
        $admin = User::factory()->admin()->create();

        $id = $this->actingAs($admin)->postJson('/api/perumahan', ['nama' => 'Kanaya', 'latitude' => -8.1405, 'longitude' => 115.09])
            ->assertCreated()->json('id');
        $this->assertSame('-8.1405000', Perumahan::find($id)->latitude);
        $this->assertSame('115.0900000', Perumahan::find($id)->longitude);

        $this->actingAs($admin)->getJson('/api/perumahan')
            ->assertJsonPath('0.latitude', '-8.1405000')
            ->assertJsonPath('0.longitude', '115.0900000');

        $this->actingAs($admin)->putJson("/api/perumahan/{$id}", ['nama' => 'Kanaya', 'latitude' => -8.2, 'longitude' => 115.2])->assertOk();
        $this->assertSame('-8.2000000', Perumahan::find($id)->latitude);

        $this->actingAs($admin)->putJson("/api/perumahan/{$id}", ['nama' => 'Kanaya', 'latitude' => null, 'longitude' => null])->assertOk();
        $this->assertNull(Perumahan::find($id)->latitude);
        $this->assertNull(Perumahan::find($id)->longitude);

        $tanpaTitik = $this->actingAs($admin)->postJson('/api/perumahan', ['nama' => 'Baruna'])->assertCreated()->json('id');
        $this->assertNull(Perumahan::find($tanpaTitik)->latitude);
    }

    public function test_titik_koordinat_harus_lengkap_dan_dalam_rentang(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->postJson('/api/perumahan', ['nama' => 'A', 'latitude' => -8.1])
            ->assertUnprocessable()->assertJsonValidationErrors('longitude');
        $this->actingAs($admin)->postJson('/api/perumahan', ['nama' => 'A', 'longitude' => 115.1])
            ->assertUnprocessable()->assertJsonValidationErrors('latitude');
        $this->actingAs($admin)->postJson('/api/perumahan', ['nama' => 'A', 'latitude' => 91, 'longitude' => 181])
            ->assertUnprocessable()->assertJsonValidationErrors(['latitude', 'longitude']);
        $this->actingAs($admin)->postJson('/api/perumahan', ['nama' => 'A', 'latitude' => 'x', 'longitude' => 'y'])
            ->assertUnprocessable()->assertJsonValidationErrors(['latitude', 'longitude']);

        $this->assertSame(0, Perumahan::count());
    }

    public function test_perumahan_yang_dipakai_tidak_bisa_dihapus_walau_kramanya_terhapus(): void
    {
        $perumahan = Perumahan::create(['nama' => 'RT 1']);
        $krama = Krama::create(['nama' => 'A', 'perumahan_id' => $perumahan->id]);
        $krama->delete();

        $this->actingAs(User::factory()->admin()->create())->deleteJson("/api/perumahan/{$perumahan->id}")
            ->assertUnprocessable()->assertJsonValidationErrors('perumahan');
    }

    public function test_petugas_hanya_bisa_membaca(): void
    {
        $perumahan = Perumahan::create(['nama' => 'RT 1']);
        $petugas = User::factory()->create();

        $this->actingAs($petugas)->getJson('/api/perumahan')->assertOk()->assertJsonPath('0.krama_count', 0);
        $this->actingAs($petugas)->postJson('/api/perumahan', ['nama' => 'X'])->assertForbidden();
        $this->actingAs($petugas)->deleteJson("/api/perumahan/{$perumahan->id}")->assertForbidden();
    }
}
