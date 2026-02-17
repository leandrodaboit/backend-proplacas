<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Database\Seeder;

class CompanySeeder extends Seeder
{
    public function run(): void
    {
        // Garante que o plano Enterprise existe
        $enterprisePlan = Plan::where('code', 'enterprise')->first();

        if (!$enterprisePlan) {
            $this->command->error('Plano Enterprise não encontrado. Rode o PlanSeeder primeiro.');
            return;
        }

        // Cria a empresa padrão Proletec
        $company = Company::firstOrCreate(
            ['slug' => 'proletec'],
            [
                'name' => 'Proletec',
                'status' => 'active',
                'plan_id' => $enterprisePlan->id,
                'settings' => json_encode(['theme' => 'dark', 'locale' => 'pt-BR']),
            ]
        );

        // Busca ou cria o usuário Admin principal
        $adminUser = User::firstOrCreate(
            ['email' => 'admin@proplacas.com.br'],
            [
                'name' => 'Admin',
                'sobrenome' => 'Sistema',
                'empresa' => 'Proletec',
                'password' => bcrypt('password'),
                'tipo' => 'admin',
                'status' => 'active',
                'ativo' => true,
            ]
        );

        // Vincula o Admin à empresa Proletec como Owner
        if (!$company->users()->where('user_id', $adminUser->id)->exists()) {
            $company->users()->attach($adminUser->id, [
                'is_owner' => true,
                'is_active' => true,
            ]);
        }

        $this->command->info('Empresa Proletec e usuário Admin configurados com sucesso.');
    }
}
