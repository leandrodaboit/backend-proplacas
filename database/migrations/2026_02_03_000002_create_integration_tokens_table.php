<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('integration_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('token', 64)->unique();
            $table->enum('type', ['whatsapp', 'ai', 'webhook', 'external'])->default('external');
            $table->text('description')->nullable();
            $table->json('abilities')->nullable();
            $table->json('allowed_ips')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('rate_limit_per_minute')->default(60);
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('is_active');
            $table->index(['token', 'is_active']);
        });

        Schema::create('integration_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('integration_token_id')->constrained()->onDelete('cascade');
            $table->string('endpoint');
            $table->string('method', 10);
            $table->string('ip_address', 45)->nullable();
            $table->integer('response_status')->nullable();
            $table->integer('response_time_ms')->nullable();
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->json('response_body')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('integration_token_id');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('integration_logs');
        Schema::dropIfExists('integration_tokens');
    }
};
