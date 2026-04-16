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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('developer_app_id')->nullable();
            $table->unsignedBigInteger('tenant_id');
            $table->string('method', 10);
            $table->string('endpoint', 500);
            $table->unsignedSmallInteger('status_code')->default(200);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->unsignedInteger('response_time_ms')->default(0);
            $table->json('request_headers')->nullable();
            $table->text('request_body')->nullable();
            $table->text('response_body')->nullable();
            $table->string('error_message')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->index(['tenant_id', 'created_at']);
            $table->index('developer_app_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
