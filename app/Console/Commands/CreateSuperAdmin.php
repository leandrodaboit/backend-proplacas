<?php

namespace App\Console\Commands;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Console\Command;

class CreateSuperAdmin extends Command
{
    protected $signature = 'make:super-admin
                            {email : E-mail do usuário}
                            {--name= : Nome do usuário}
                            {--password= : Senha (será solicitada se não informada)}';

    protected $description = 'Cria ou promove um usuário para Super Admin';

    public function handle(): int
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if ($user) {
            return $this->promoteExisting($user);
        }

        return $this->createNew($email);
    }

    private function promoteExisting(User $user): int
    {
        if ($user->hasRole(RoleEnum::SUPER_ADMIN->value)) {
            $this->warn("O usuário {$user->email} já é Super Admin.");
            return self::SUCCESS;
        }

        if (!$this->confirm("Usuário {$user->email} encontrado. Deseja promover para Super Admin?")) {
            $this->info('Operação cancelada.');
            return self::SUCCESS;
        }

        $user->assignRole(RoleEnum::SUPER_ADMIN->value);

        $this->info("Usuário {$user->email} promovido para Super Admin!");

        return self::SUCCESS;
    }

    private function createNew(string $email): int
    {
        $name = $this->option('name') ?? $this->ask('Nome do usuário');
        $password = $this->option('password') ?? $this->secret('Senha');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'tipo' => 'admin',
            'status' => 'active',
            'ativo' => true,
        ]);

        $user->assignRole(RoleEnum::SUPER_ADMIN->value);

        $this->info("Super Admin criado com sucesso!");
        $this->newLine();

        $this->table(['Campo', 'Valor'], [
            ['ID', $user->id],
            ['Nome', $user->name],
            ['E-mail', $user->email],
            ['Role', RoleEnum::SUPER_ADMIN->value],
        ]);

        return self::SUCCESS;
    }
}
