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
 * - Operador: Acceso básico a clientes y dashboard operativo
 * 
 * Nota: Pest aplica LazilyRefreshDatabase automáticamente a tests Feature
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

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // Después del login, debe poder acceder al dashboard
        $this->actingAs($user);
        $dashboardResponse = $this->get('/dashboard');
        
        // Super Admin se redirige a admin.dashboard
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

    public function test_super_admin_can_access_api_services(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $response = $this->actingAs($user)->get(route('admin.api-services.index'));
        
        $response->assertStatus(200);
    }

    public function test_super_admin_can_access_email_settings(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $response = $this->actingAs($user)->get(route('admin.email.settings'));
        
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
        
        // Manager se redirige a admin.dashboard
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
        
        // Manager no tiene acceso a rutas de programador
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
        
        // Programador se redirige a programmer.dashboard
        $response->assertRedirect(route('programmer.dashboard'));
    }

    public function test_programador_can_access_programmer_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('programmer.dashboard'));
        
        $response->assertStatus(200);
    }

    public function test_programador_can_access_enterprise_module(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('programmer.enterprise.index'));
        
        $response->assertStatus(200);
    }

    public function test_programador_can_access_programmer_clients(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('programmer.clients.index'));
        
        $response->assertStatus(200);
    }

    public function test_programador_can_access_api_monitor(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('programmer.api-dashboard'));
        
        $response->assertStatus(200);
    }

    public function test_programador_can_access_business_rules(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('programmer.business-rules.index'));
        
        $response->assertStatus(200);
    }

    public function test_programador_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        
        // Programador no tiene acceso a rutas admin
        $response->assertStatus(403);
    }

    public function test_programador_cannot_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('admin.users.index'));
        
        // Programador no tiene acceso a gestión de usuarios
        $response->assertStatus(403);
    }

    // =========================================================
    // OPERADOR TESTS
    // =========================================================

    public function test_operador_login_goes_to_operador_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operador');

        $this->actingAs($user);
        $response = $this->get('/dashboard');
        
        // Operador NO se redirige, ve el dashboard estándar
        $response->assertStatus(200);
    }

    public function test_operador_can_access_clients(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operador');

        $response = $this->actingAs($user)->get(route('clients.index'));
        
        $response->assertStatus(200);
    }

    public function test_operador_can_access_api_monitor(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operador');

        $response = $this->actingAs($user)->get(route('api.dashboard'));
        
        $response->assertStatus(200);
    }

    public function test_operador_can_access_profile(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operador');

        $response = $this->actingAs($user)->get(route('profile.edit'));
        
        $response->assertStatus(200);
    }

    public function test_operador_can_access_settings(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operador');

        $response = $this->actingAs($user)->get(route('settings.index'));
        
        $response->assertStatus(200);
    }

    public function test_operador_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operador');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        
        // Operador no tiene acceso a rutas admin
        $response->assertStatus(403);
    }

    public function test_operador_cannot_access_programmer_routes(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operador');

        $response = $this->actingAs($user)->get(route('programmer.dashboard'));
        
        // Operador no tiene acceso a rutas de programador
        $response->assertStatus(403);
    }

    public function test_operador_cannot_access_user_management(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operador');

        $response = $this->actingAs($user)->get(route('admin.users.index'));
        
        // Operador no tiene acceso a gestión de usuarios
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
    // NAVIGATION VISIBILITY TESTS
    // =========================================================

    public function test_super_admin_sees_admin_menu_in_navigation(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Super Admin');

        $response = $this->actingAs($user)->get(route('admin.dashboard'));
        
        $response->assertStatus(200);
        // Verificar elementos del admin layout
        $response->assertSee('Panel de Control General'); // Dashboard header
        $response->assertSee('Usuarios Activos'); // Stats card
        $response->assertSee('Clientes Totales'); // Stats card
        $response->assertSee('APIs Disponibles'); // Stats card
    }

    public function test_programador_sees_programmer_menu_in_navigation(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('programmer.dashboard'));
        
        $response->assertStatus(200);
        $response->assertSee('Panel Programador');
        $response->assertSee('Enterprise');
        $response->assertSee('Clientes');
        $response->assertSee('Monitor APIs');
        $response->assertSee('Reglas de Negocio');
    }

    public function test_operador_sees_operador_menu_in_navigation(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operador');

        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Clientes');
        $response->assertSee('Monitor APIs');
        // No debe ver menú de Admin
        $response->assertDontSee('Dashboard Global');
        $response->assertDontSee('Gestión de Usuarios');
    }

    public function test_operador_does_not_see_admin_menu(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Operador');

        $response = $this->actingAs($user)->get('/dashboard');
        
        $response->assertStatus(200);
        // No debe ver el menú Admin dropdown
        $response->assertDontSee('fa-shield-alt');
    }

    public function test_programador_does_not_see_admin_menu(): void
    {
        $user = User::factory()->create();
        $user->assignRole('Programador');

        $response = $this->actingAs($user)->get(route('programmer.dashboard'));
        
        $response->assertStatus(200);
        // No debe ver el menú Admin dropdown (solo Super Admin lo ve según la navegación)
        $response->assertDontSee('Dashboard Global');
    }
}
