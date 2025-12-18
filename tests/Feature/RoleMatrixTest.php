<?php

namespace Tests\Feature;

use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\RolePermissionSeeder;

class RoleMatrixTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Ejecutamos el seeder para poblar la base de datos de prueba (sqlite :memory:)
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_roles_exist()
    {
        $this->assertTrue(Role::where('name', 'Super Admin')->exists(), 'Role Super Admin should exist');
        $this->assertTrue(Role::where('name', 'Manager')->exists(), 'Role Manager should exist');
        $this->assertTrue(Role::where('name', 'Analista')->exists(), 'Role Analista should exist');
        // El rol User también se crea en el seeder ahora
        $this->assertTrue(Role::where('name', 'User')->exists(), 'Role User should exist');
    }

    public function test_super_admin_has_full_permissions()
    {
        $role = Role::findByName('Super Admin');
        
        // Super Admin debe tener acceso a todos los permisos existentes
        $allPermissions = Permission::all();
        foreach ($allPermissions as $permission) {
            $this->assertTrue($role->hasPermissionTo($permission), "Super Admin should have permission: {$permission->name}");
        }
    }

    public function test_manager_permissions_matrix()
    {
        $role = Role::findByName('Manager');

        // Matrix: Clientes FULL
        $this->assertTrue($role->hasPermissionTo('view clients'), 'Manager should view clients');
        $this->assertTrue($role->hasPermissionTo('create clients'), 'Manager should create clients');
        $this->assertTrue($role->hasPermissionTo('edit clients'), 'Manager should edit clients');
        $this->assertTrue($role->hasPermissionTo('delete clients'), 'Manager should delete clients');
        $this->assertTrue($role->hasPermissionTo('restore clients'), 'Manager should restore clients');
        $this->assertTrue($role->hasPermissionTo('reassign clients'), 'Manager should reassign clients');

        // Matrix: Catálogo APIs Ver
        $this->assertTrue($role->hasPermissionTo('view api catalog'), 'Manager should view api catalog');
        
        // Matrix: Catálogo APIs NO crear/editar (manage api catalog)
        $this->assertFalse($role->hasPermissionTo('manage api catalog'), 'Manager should NOT manage api catalog');

        // Matrix: Usuarios - No tiene el permiso 'manage users'
        $this->assertFalse($role->hasPermissionTo('manage users'), 'Manager should NOT manage users');
    }

    public function test_analista_permissions_matrix()
    {
        $role = Role::findByName('Analista');

        // Matrix: Clientes Create/Read/Update
        $this->assertTrue($role->hasPermissionTo('view clients'), 'Analista should view clients');
        $this->assertTrue($role->hasPermissionTo('create clients'), 'Analista should create clients');
        $this->assertTrue($role->hasPermissionTo('edit clients'), 'Analista should edit clients');

        // Matrix: Clientes NO Delete final
        $this->assertFalse($role->hasPermissionTo('delete clients'), 'Analista should NOT delete clients');
        $this->assertFalse($role->hasPermissionTo('restore clients'), 'Analista should NOT restore clients');
        $this->assertFalse($role->hasPermissionTo('reassign clients'), 'Analista should NOT reassign clients');

        // Matrix: Catálogo APIs Ver (Solo lista)
        $this->assertTrue($role->hasPermissionTo('view api catalog'), 'Analista should view api catalog');
        $this->assertFalse($role->hasPermissionTo('manage api catalog'), 'Analista should NOT manage api catalog');

        // Matrix: Credenciales Cargar y Editar
        $this->assertTrue($role->hasPermissionTo('manage credentials'), 'Analista should manage credentials');

        // Matrix: Usuarios X
        $this->assertFalse($role->hasPermissionTo('manage users'), 'Analista should NOT manage users');
    }
}
