<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            PlanSeeder::class,
            CompanySeeder::class,
        ]);

        // Usuários adicionais para teste (opcional, vinculados a empresas fictícias se necessário)
        // User::factory()->create([...]);

        $this->command->info('Database seeded successfully!');
    }
}
