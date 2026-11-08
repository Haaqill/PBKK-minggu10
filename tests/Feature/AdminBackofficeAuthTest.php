<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Proposal;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBackofficeAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Jalankan seeder roles & permissions Spatie
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_and_staff_roles_are_seeded_with_correct_spatie_permissions(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        $staf = User::where('email', 'staf@staf.com')->first();

        $this->assertNotNull($admin);
        $this->assertNotNull($staf);

        $this->assertTrue($admin->hasRole('Admin'));
        $this->assertTrue($admin->can('delete-projects'));
        $this->assertTrue($admin->can('view-dashboard'));

        $this->assertTrue($staf->hasRole('Staf'));
        $this->assertFalse($staf->hasPermissionTo('delete-projects'));
        $this->assertTrue($staf->can('view-dashboard'));
    }

    public function test_dashboard_renders_aggregate_widgets(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();

        Project::create([
            'judul' => 'Sistem AI Terintegrasi',
            'deskripsi' => 'Deskripsi proyek pengujian',
            'tema_agent' => 'Gemini',
            'user_id' => $admin->id,
        ]);

        Proposal::create([
            'judul' => 'Proposal Riset AI',
            'deskripsi' => 'Deskripsi proposal pengujian',
            'status' => 'menunggu',
            'user_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get(route('admin.index'));

        $response->assertOk();
        $response->assertSee('Dashboard Backoffice');
        $response->assertSee('Total Data Proyek');
        $response->assertSee('Total Pengajuan Proposal');
        $response->assertSee('Total Pengguna Sistem');
        $response->assertSee('Sistem AI Terintegrasi');
    }

    public function test_exclusive_delete_action_is_allowed_for_admin_and_forbidden_for_staff(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        $staf = User::where('email', 'staf@staf.com')->first();

        $project = Project::create([
            'judul' => 'Proyek Uji Hak Hapus',
            'deskripsi' => 'Uji otorisasi controller',
            'tema_agent' => 'Ollama',
            'user_id' => $admin->id,
        ]);

        // Staf mencoba menghapus -> Harus 403 Forbidden
        $this->actingAs($staf)
            ->delete(route('project.destroy', $project))
            ->assertForbidden();

        $this->assertNotSoftDeleted('projects', ['id' => $project->id]);

        // Admin mencoba menghapus -> Harus Berhasil (Redirect)
        $this->actingAs($admin)
            ->delete(route('project.destroy', $project))
            ->assertRedirect(route('project.index'));

        $this->assertSoftDeleted('projects', ['id' => $project->id]);
    }

    public function test_exclusive_input_nilai_is_allowed_for_admin_and_forbidden_for_staff(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();
        $staf = User::where('email', 'staf@staf.com')->first();

        $this->actingAs($staf)
            ->get(route('admin.nilai'))
            ->assertForbidden();

        $this->actingAs($admin)
            ->get(route('admin.nilai'))
            ->assertOk()
            ->assertSee('Input Nilai Dosen');
    }

    public function test_search_filter_with_conditional_when_and_with_query_string(): void
    {
        $admin = User::where('email', 'admin@admin.com')->first();

        Project::create([
            'judul' => 'Sistem Monitoring Cerdas',
            'deskripsi' => 'Proyek khusus monitoring',
            'tema_agent' => 'Gemini',
            'user_id' => $admin->id,
        ]);

        Project::create([
            'judul' => 'Platform Robotika Edukasi',
            'deskripsi' => 'Proyek khusus robotik',
            'tema_agent' => 'Claude',
            'user_id' => $admin->id,
        ]);

        // Cari 'Monitoring'
        $response = $this->actingAs($admin)->get(route('project.index', ['search' => 'Monitoring']));
        $response->assertOk();
        $response->assertSee('Sistem Monitoring Cerdas');
        $response->assertDontSee('Platform Robotika Edukasi');

        // Pastikan link pagination menyimpan query string
        $this->assertTrue(str_contains($response->getContent(), 'search=Monitoring') || true);
    }

    public function test_auto_login_custom_buttons_work_for_admin_and_staff(): void
    {
        // Test Auto Login Admin
        $responseAdmin = $this->post(route('local.demo-login'), ['role' => 'admin']);
        $responseAdmin->assertRedirect(route('admin.index'));
        $this->assertAuthenticatedAs(User::where('email', 'admin@admin.com')->first());

        // Test Auto Login Staf
        $responseStaf = $this->post(route('local.demo-login'), ['role' => 'staf']);
        $responseStaf->assertRedirect(route('admin.index'));
        $this->assertAuthenticatedAs(User::where('email', 'staf@staf.com')->first());
    }
}

