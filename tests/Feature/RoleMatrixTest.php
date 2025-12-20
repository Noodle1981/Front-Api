<?php

namespace Tests\Feature;

use Tests\TestCase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Database\Seeders\RolePermissionSeeder;

/**
 * Tests de la Matriz de Permisos del Sistema.
 * 
 * Verifica que cada rol tiene exactamente los permisos correspondientes
 * según la matriz definida en RolePermissionSeeder.
 */
class RoleMatrixTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_roles_exist()
    {
        $this->assertTrue(Role::where('name', 'Super Admin')->exists(), 'Role Super Admin should exist');
        $this->assertTrue(Role::where('name', 'Manager')->exists(), 'Role Manager should exist');
        $this->assertTrue(Role::where('name', 'Programador')->exists(), 'Role Programador should exist');
        $this->assertTrue(Role::where('name', 'Operador')->exists(), 'Role Operador should exist');
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

    public function test_programador_permissions_matrix()
    {
        $role = Role::findByName('Programador');

        // Matrix: Clientes Create/Read/Update + Reassign
        $this->assertTrue($role->hasPermissionTo('view clients'), 'Programador should view clients');
        $this->assertTrue($role->hasPermissionTo('create clients'), 'Programador should create clients');
        $this->assertTrue($role->hasPermissionTo('edit clients'), 'Programador should edit clients');
        $this->assertTrue($role->hasPermissionTo('reassign clients'), 'Programador should reassign clients');

        // Matrix: Clientes NO Delete/Restore
        $this->assertFalse($role->hasPermissionTo('delete clients'), 'Programador should NOT delete clients');
        $this->assertFalse($role->hasPermissionTo('restore clients'), 'Programador should NOT restore clients');

        // Matrix: Catálogo APIs Ver (Solo lista)
        $this->assertTrue($role->hasPermissionTo('view api catalog'), 'Programador should view api catalog');
        $this->assertFalse($role->hasPermissionTo('manage api catalog'), 'Programador should NOT manage api catalog');

        // Matrix: Credenciales Cargar y Editar
        $this->assertTrue($role->hasPermissionTo('manage credentials'), 'Programador should manage credentials');

        // Matrix: Usuarios X
        $this->assertFalse($role->hasPermissionTo('manage users'), 'Programador should NOT manage users');
    }

    public function test_operador_permissions_matrix()
    {
        $role = Role::findByName('Operador');

        // Matrix: Clientes Create/Read/Update
        $this->assertTrue($role->hasPermissionTo('view clients'), 'Operador should view clients');
        $this->assertTrue($role->hasPermissionTo('create clients'), 'Operador should create clients');
        $this->assertTrue($role->hasPermissionTo('edit clients'), 'Operador should edit clients');

        // Matrix: Clientes NO Delete/Restore/Reassign
        $this->assertFalse($role->hasPermissionTo('delete clients'), 'Operador should NOT delete clients');
        $this->assertFalse($role->hasPermissionTo('restore clients'), 'Operador should NOT restore clients');
        $this->assertFalse($role->hasPermissionTo('reassign clients'), 'Operador should NOT reassign clients');

        // Matrix: Catálogo APIs Ver (Solo lista)
        $this->assertTrue($role->hasPermissionTo('view api catalog'), 'Operador should view api catalog');
        $this->assertFalse($role->hasPermissionTo('manage api catalog'), 'Operador should NOT manage api catalog');

        // Matrix: Credenciales
        $this->assertTrue($role->hasPermissionTo('manage credentials'), 'Operador should manage credentials');

        // Matrix: Usuarios X
        $this->assertFalse($role->hasPermissionTo('manage users'), 'Operador should NOT manage users');
    }
}
