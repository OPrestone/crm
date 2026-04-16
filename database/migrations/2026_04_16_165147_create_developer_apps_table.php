<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('developer_apps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('created_by');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('client_id', 40)->unique();
            $table->string('client_secret', 80);
            $table->string('webhook_url')->nullable();
            $table->json('webhook_events')->nullable();
            $table->json('allowed_ips')->nullable();
            $table->unsignedInteger('rate_limit')->default(1000);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_used_at')->nullable();
            $table->unsignedBigInteger('total_requests')->default(0);
            $table->timestamps();
            $table->index('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_apps');
    }
};
