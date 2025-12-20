<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * Tests de Acceso por Rol a Rutas del Sistema.
 * 
 * Verifica que cada rol puede o no acceder a rutas específicas
 * y realizar operaciones permitidas/restringidas.
 */
class RoleAccessTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_super_admin_can_access_admin_dashboard()
    {
        $admin = User::factory()->create();
        $admin->assignRole('Super Admin');

        $response = $this->actingAs($admin)
            ->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    public function test_programador_cannot_access_admin_dashboard()
    {
        $programador = User::factory()->create();
        $programador->assignRole('Programador');

        $response = $this->actingAs($programador)
            ->get('/admin/dashboard');

        // Spatie middleware returns 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_programador_role_does_not_have_delete_permission()
    {
        $programador = User::factory()->create();
        $programador->assignRole('Programador');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->assertFalse($programador->can('delete clients'), 'Programador should NOT have [delete clients] permission');
        $this->assertTrue($programador->can('view clients'), 'Programador SHOULD have [view clients] permission');
    }

    public function test_operador_role_does_not_have_delete_permission()
    {
        $operador = User::factory()->create();
        $operador->assignRole('Operador');

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->assertFalse($operador->can('delete clients'), 'Operador should NOT have [delete clients] permission');
        $this->assertTrue($operador->can('view clients'), 'Operador SHOULD have [view clients] permission');
    }

    public function test_manager_role_has_delete_permission()
    {
        $manager = User::factory()->create();
        $manager->assignRole('Manager');

        $this->assertTrue($manager->can('delete clients'));
    }

    public function test_manager_can_soft_delete_clients()
    {
        $manager = User::factory()->create();
        $manager->assignRole('Manager');

        $client = Client::factory()->create(['user_id' => $manager->id]);

        $response = $this->actingAs($manager)
            ->delete(route('clients.destroy', $client));

        $response->assertRedirect(route('clients.index'));
        $this->assertSoftDeleted($client);
    }

    public function test_programador_cannot_delete_clients()
    {
        $programador = User::factory()->create();
        $programador->assignRole('Programador');

        $client = Client::factory()->create(['user_id' => $programador->id]);

        $response = $this->actingAs($programador)
            ->delete(route('clients.destroy', $client));

        if ($response->status() === 302) {
            // If redirecting, ensure it's NOT to the index page (which implies success)
            $this->assertNotEquals(route('clients.index'), $response->headers->get('Location'), "Redirected to Index (Success) -> Security Fail");
        } else {
            $response->assertStatus(403);
        }

        $this->assertNotSoftDeleted($client);
    }

    public function test_operador_cannot_delete_clients()
    {
        $operador = User::factory()->create();
        $operador->assignRole('Operador');

        $client = Client::factory()->create(['user_id' => $operador->id]);

        $response = $this->actingAs($operador)
            ->delete(route('clients.destroy', $client));

        if ($response->status() === 302) {
            $this->assertNotEquals(route('clients.index'), $response->headers->get('Location'), "Redirected to Index (Success) -> Security Fail");
        } else {
            $response->assertStatus(403);
        }

        $this->assertNotSoftDeleted($client);
    }
}
