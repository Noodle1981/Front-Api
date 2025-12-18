<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed roles and permissions
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

    public function test_analista_cannot_access_admin_dashboard()
    {
        $analista = User::factory()->create();
        $analista->assignRole('Analista');

        $response = $this->actingAs($analista)
            ->get('/admin/dashboard');

        if ($response->status() === 302) {
            dump("Redirecting to: " . $response->headers->get('Location'));
        }

        // Spatie middleware returns 403 Forbidden
        $response->assertStatus(403);
    }

    public function test_analista_role_does_not_have_delete_permission()
    {
        $analista = User::factory()->create();
        $analista->assignRole('Analista');

        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $this->assertFalse($analista->can('delete clients'), 'Analista should NOT have [delete clients] permission');
        $this->assertTrue($analista->can('view clients'), 'Analista SHOULD have [view clients] permission');
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

    public function test_analista_cannot_delete_clients()
    {
        $analista = User::factory()->create();
        $analista->assignRole('Analista');

        $client = Client::factory()->create(['user_id' => $analista->id]);

        $response = $this->actingAs($analista)
            ->delete(route('clients.destroy', $client));

        if ($response->status() === 302) {
            // If redirecting, ensure it's NOT to the index page (which implies success)
            // Typically redirects to home or login or dashboard on error
            $this->assertNotEquals(route('clients.index'), $response->headers->get('Location'), "Redirected to Index (Success) -> Security Fail");
        } else {
            $response->assertStatus(403);
        }

        $this->assertNotSoftDeleted($client);
    }
}
