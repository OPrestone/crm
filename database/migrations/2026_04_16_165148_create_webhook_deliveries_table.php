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
        Schema::create('webhook_deliveries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('developer_app_id');
            $table->unsignedBigInteger('tenant_id');
            $table->string('event', 100);
            $table->json('payload');
            $table->string('endpoint_url', 500);
            $table->string('status', 20)->default('pending');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedSmallInteger('response_code')->nullable();
            $table->text('response_body')->nullable();
            $table->string('error_message')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('next_retry_at')->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'created_at']);
            $table->index('developer_app_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('webhook_deliveries');
    }
};
