<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->admin()->create([
            'name' => 'Admin',
            'sobrenome' => 'Sistema',
            'email' => 'admin@proplacas.com.br',
        ]);

        User::factory()->operador()->create([
            'name' => 'Operador',
            'sobrenome' => 'Teste',
            'email' => 'operador@proplacas.com.br',
        ]);

        User::factory()->create([
            'name' => 'Cliente',
            'sobrenome' => 'Teste',
            'email' => 'cliente@proplacas.com.br',
        ]);

        $this->command->info('Usuários de teste criados com senha: password');
    }
}
