<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;
use Database\Seeders\RolePermissionSeeder;

/**
 * Test que verifica que cada rol ve y accede a lo que le corresponde.
 * 
 * Roles del sistema:
 * - Super Admin: Acceso total, incluye panel admin
 * - Manager: Acceso a panel admin, gestión de usuarios
 * - Programador: Acceso a rutas /programadores/*
 * - Operador: Acceso básico (legacy, actualmente redirige)
 * 
 * Rutas actuales después de auditoría (v2.2.0):
 * - Admin: /admin/dashboard, /admin/users
 * - Programador: /programadores/dashboard, /programadores/clients, /programadores/workflows/*
 */
class LoginByRoleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    // =========================================================
    // SUPER ADMIN TESTS
    // =========================================================

    public function test_super_admin_login_redirects_to_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $this->actingAs($user);
        $dashboardResponse = $this->get('/dashboard');
        
        $dashboardResponse->assertRedirect(route('admin.dashboard'));
    }

    public function test_super_admin_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
    }

    public function test_super_admin_can_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $response = $this->actingAs($user)->get(route('admin.users.index'));
        
        $response->assertStatus(200);
    }

    // =========================================================
    // MANAGER TESTS
    // =========================================================

    public function test_manager_login_redirects_to_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Manager');

        $this->actingAs($user);
        $response = $this->get('/dashboard');
        
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_manager_can_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Manager');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
    }

    public function test_manager_can_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Manager');

        $response = $this->actingAs($user)->get(route('admin.users.index'));
        
        $response->assertStatus(200);
    }

    public function test_manager_cannot_access_programmer_routes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Manager');

        $response = $this->actingAs($user)->get(route('programmer.dashboard'));
        
        $response->assertStatus(403);
    }

    // =========================================================
    // PROGRAMADOR TESTS
    // =========================================================

    public function test_programador_login_redirects_to_programmer_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $this->actingAs($user);
        $response = $this->get('/dashboard');
        
        $response->assertRedirect(route('programmer.dashboard'));
    }

    public function test_programador_can_access_programmer_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('programmer.dashboard'));
        
        $response->assertStatus(200);
    }

    public function test_programador_can_access_programmer_clients(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('programmer.clients.index'));
        
        $response->assertStatus(200);
    }

    public function test_programador_can_access_workflow_upload(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('programmer.workflows.upload'));
        
        $response->assertStatus(200);
    }

    public function test_programador_can_access_workflow_history(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('programmer.workflows.history'));
        
        $response->assertStatus(200);
    }

    public function test_programador_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        
        $response->assertStatus(403);
    }

    public function test_programador_cannot_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('admin.users.index'));
        
        $response->assertStatus(403);
    }

    // =========================================================
    // GUEST TESTS (Sin autenticación)
    // =========================================================

    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');
        
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_admin_routes(): void
    {
        $response = $this->get(route('admin.dashboard'));
        
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_programmer_routes(): void
    {
        $response = $this->get(route('programmer.dashboard'));
        
        $response->assertRedirect(route('login'));
    }

    public function test_guest_cannot_access_clients(): void
    {
        $response = $this->get(route('clients.index'));
        
        $response->assertRedirect(route('login'));
    }

    // =========================================================
    // PROFILE ACCESS TESTS
    // =========================================================

    public function test_super_admin_can_access_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $response = $this->actingAs($user)->get(route('profile.edit'));
        
        $response->assertStatus(200);
    }

    public function test_programador_can_access_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('profile.edit'));
        
        $response->assertStatus(200);
    }
}
