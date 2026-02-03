<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/auth/register', [
            'name' => 'João',
            'sobrenome' => 'Silva',
            'email' => 'joao@example.com',
            'telefone' => '(11) 99999-9999',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'device_name' => 'iPhone 15',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'user' => ['id', 'name', 'email'],
                    'token',
                    'token_type',
                ],
            ])
            ->assertJson(['success' => true, 'data' => ['token_type' => 'Bearer']]);

        $this->assertDatabaseHas('users', [
            'email' => 'joao@example.com',
            'name' => 'João',
            'tipo' => 'cliente',
            'status' => 'active',
        ]);
    }

    public function test_user_cannot_register_with_existing_email(): void
    {
        User::factory()->create(['email' => 'existing@example.com']);

        $response = $this->postJson('/api/auth/register', [
            'name' => 'Test',
            'email' => 'existing@example.com',
            'password' => 'Password@123',
            'password_confirmation' => 'Password@123',
            'device_name' => 'Test Device',
        ]);

        $response->assertStatus(422)->assertJson(['success' => false]);
    }

    public function test_user_can_login(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => 'password',
            'status' => 'active',
            'ativo' => true,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password',
            'device_name' => 'Test Device',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data' => ['user', 'token', 'token_type']])
            ->assertJson(['success' => true, 'message' => 'Login realizado com sucesso']);
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create(['email' => 'test@example.com', 'password' => 'password']);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
            'device_name' => 'Test Device',
        ]);

        $response->assertStatus(401)->assertJson(['success' => false, 'message' => 'Credenciais inválidas']);
    }

    public function test_inactive_user_cannot_login(): void
    {
        User::factory()->create([
            'email' => 'inactive@example.com',
            'password' => 'password',
            'ativo' => false,
        ]);

        $response = $this->postJson('/api/auth/login', [
            'email' => 'inactive@example.com',
            'password' => 'password',
            'device_name' => 'Test Device',
        ]);

        $response->assertStatus(401)->assertJson(['success' => false, 'message' => 'Usuário desativado']);
    }

    public function test_authenticated_user_can_get_profile(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/auth/me');

        $response->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data' => ['id', 'name', 'email']])
            ->assertJson(['success' => true, 'data' => ['id' => $user->id, 'email' => $user->email]]);
    }

    public function test_unauthenticated_user_cannot_get_profile(): void
    {
        $response = $this->getJson('/api/auth/me');

        $response->assertStatus(401)->assertJson(['success' => false, 'message' => 'Não autenticado']);
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-device')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->postJson('/api/auth/logout');

        $response->assertStatus(200)->assertJson(['success' => true, 'message' => 'Logout realizado com sucesso']);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_can_logout_from_all_devices(): void
    {
        $user = User::factory()->create();
        $user->createToken('device-1');
        $user->createToken('device-2');
        $currentToken = $user->createToken('current-device')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$currentToken}")->postJson('/api/auth/logout-all');

        $response->assertStatus(200)->assertJson(['success' => true]);
        $this->assertDatabaseCount('personal_access_tokens', 0);
    }

    public function test_user_can_list_tokens(): void
    {
        $user = User::factory()->create();
        $user->createToken('device-1');
        $user->createToken('device-2');
        $token = $user->createToken('current-device')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$token}")->getJson('/api/auth/tokens');

        $response->assertStatus(200)->assertJson(['success' => true])->assertJsonCount(3, 'data');
    }

    public function test_user_can_revoke_specific_token(): void
    {
        $user = User::factory()->create();
        $tokenToRevoke = $user->createToken('device-to-revoke');
        $currentToken = $user->createToken('current-device')->plainTextToken;

        $response = $this->withHeader('Authorization', "Bearer {$currentToken}")
            ->deleteJson("/api/auth/tokens/{$tokenToRevoke->accessToken->id}");

        $response->assertStatus(200)->assertJson(['success' => true, 'message' => 'Token revogado com sucesso']);
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $tokenToRevoke->accessToken->id]);
    }

    public function test_api_health_check(): void
    {
        $response = $this->getJson('/api/health');

        $response->assertStatus(200)->assertJson(['success' => true, 'data' => ['status' => 'healthy']]);
    }
}
