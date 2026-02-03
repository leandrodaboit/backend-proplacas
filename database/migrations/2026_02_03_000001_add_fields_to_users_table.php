<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('sobrenome')->nullable()->after('name');
            $table->string('telefone', 20)->nullable()->after('email');
            $table->enum('tipo', ['admin', 'operador', 'cliente'])->default('cliente')->after('telefone');
            $table->enum('status', ['active', 'inactive', 'suspended', 'pending'])->default('pending')->after('tipo');
            $table->boolean('ativo')->default(true)->after('status');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');
            $table->string('last_login_ip', 45)->nullable()->after('last_login_at');

            $table->index('status');
            $table->index('tipo');
            $table->index('ativo');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['tipo']);
            $table->dropIndex(['ativo']);

            $table->dropColumn([
                'sobrenome',
                'telefone',
                'tipo',
                'status',
                'ativo',
                'last_login_at',
                'last_login_ip',
            ]);
        });
    }
};
