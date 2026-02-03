<?php

namespace Tests\Feature\Permission;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolePermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Criar permissões básicas
        Permission::create(['name' => 'roles.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'roles.create', 'guard_name' => 'web']);
        Permission::create(['name' => 'roles.update', 'guard_name' => 'web']);
        Permission::create(['name' => 'roles.delete', 'guard_name' => 'web']);
        Permission::create(['name' => 'permissions.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'permissions.manage', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.view', 'guard_name' => 'web']);
        Permission::create(['name' => 'users.manage-roles', 'guard_name' => 'web']);

        // Criar role super-admin
        $superAdmin = Role::create(['name' => RoleEnum::SUPER_ADMIN->value, 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());
    }

    private function createSuperAdmin(): User
    {
        $user = User::factory()->create(['tipo' => 'admin', 'status' => 'active', 'ativo' => true]);
        $user->assignRole(RoleEnum::SUPER_ADMIN->value);
        return $user;
    }

    private function createUserWithPermission(string $permission): User
    {
        $user = User::factory()->create(['tipo' => 'admin', 'status' => 'active', 'ativo' => true]);
        $user->givePermissionTo($permission);
        return $user;
    }

    public function test_super_admin_can_list_roles(): void
    {
        $user = $this->createSuperAdmin();

        $response = $this->actingAs($user)->getJson('/api/roles');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_user_without_permission_cannot_list_roles(): void
    {
        $user = User::factory()->create(['tipo' => 'cliente', 'status' => 'active', 'ativo' => true]);

        $response = $this->actingAs($user)->getJson('/api/roles');

        $response->assertStatus(403);
    }

    public function test_user_with_permission_can_list_roles(): void
    {
        $user = $this->createUserWithPermission('roles.view');

        $response = $this->actingAs($user)->getJson('/api/roles');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_super_admin_can_create_role(): void
    {
        $user = $this->createSuperAdmin();

        $response = $this->actingAs($user)->postJson('/api/roles', [
            'name' => 'teste-role',
            'description' => 'Role de teste',
            'permissions' => ['roles.view'],
        ]);

        $response->assertStatus(201)->assertJson([
            'success' => true,
            'data' => ['name' => 'teste-role'],
        ]);

        $this->assertDatabaseHas('roles', ['name' => 'teste-role']);
    }

    public function test_super_admin_can_update_role(): void
    {
        $user = $this->createSuperAdmin();
        $role = Role::create(['name' => 'role-to-update', 'guard_name' => 'web']);

        $response = $this->actingAs($user)->putJson("/api/roles/{$role->id}", [
            'name' => 'role-updated',
            'description' => 'Descrição atualizada',
        ]);

        $response->assertStatus(200)->assertJson([
            'success' => true,
            'data' => ['name' => 'role-updated'],
        ]);
    }

    public function test_super_admin_cannot_delete_protected_role(): void
    {
        $user = $this->createSuperAdmin();
        $superAdminRole = Role::findByName(RoleEnum::SUPER_ADMIN->value);

        $response = $this->actingAs($user)->deleteJson("/api/roles/{$superAdminRole->id}");

        $response->assertStatus(403)->assertJson([
            'success' => false,
            'message' => 'Não é permitido editar esta role',
        ]);
    }

    public function test_super_admin_can_delete_custom_role(): void
    {
        $user = $this->createSuperAdmin();
        $role = Role::create(['name' => 'role-to-delete', 'guard_name' => 'web']);

        $response = $this->actingAs($user)->deleteJson("/api/roles/{$role->id}");

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseMissing('roles', ['name' => 'role-to-delete']);
    }

    public function test_user_can_get_own_permissions(): void
    {
        $user = $this->createUserWithPermission('roles.view');

        $response = $this->actingAs($user)->getJson('/api/my-permissions');

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonPath('data.permissions', ['roles.view']);
    }

    public function test_user_can_check_permission(): void
    {
        $user = $this->createUserWithPermission('roles.view');

        $response = $this->actingAs($user)->postJson('/api/check-permission', [
            'permission' => 'roles.view',
        ]);

        $response->assertStatus(200)->assertJson([
            'success' => true,
            'data' => ['permission' => 'roles.view', 'has_permission' => true],
        ]);
    }

    public function test_user_can_check_role(): void
    {
        $user = $this->createSuperAdmin();

        $response = $this->actingAs($user)->postJson('/api/check-permission', [
            'role' => RoleEnum::SUPER_ADMIN->value,
        ]);

        $response->assertStatus(200)->assertJson([
            'success' => true,
            'data' => ['role' => RoleEnum::SUPER_ADMIN->value, 'has_role' => true],
        ]);
    }

    public function test_super_admin_can_assign_roles_to_user(): void
    {
        $admin = $this->createSuperAdmin();
        $targetUser = User::factory()->create(['status' => 'active', 'ativo' => true]);

        Role::create(['name' => 'operador', 'guard_name' => 'web']);

        $response = $this->actingAs($admin)->postJson("/api/users/{$targetUser->id}/permissions/roles", [
            'roles' => ['operador'],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertTrue($targetUser->fresh()->hasRole('operador'));
    }

    public function test_super_admin_can_revoke_roles_from_user(): void
    {
        $admin = $this->createSuperAdmin();
        $targetUser = User::factory()->create(['status' => 'active', 'ativo' => true]);
        $role = Role::create(['name' => 'operador', 'guard_name' => 'web']);
        $targetUser->assignRole($role);

        $response = $this->actingAs($admin)->deleteJson("/api/users/{$targetUser->id}/permissions/roles");

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertFalse($targetUser->fresh()->hasRole('operador'));
    }

    public function test_list_permissions_grouped(): void
    {
        $user = $this->createSuperAdmin();

        $response = $this->actingAs($user)->getJson('/api/permissions/grouped');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    public function test_list_available_modules(): void
    {
        $user = $this->createSuperAdmin();

        $response = $this->actingAs($user)->getJson('/api/permissions/modules');

        $response->assertStatus(200)->assertJson(['success' => true]);
    }
}
