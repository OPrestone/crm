<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('web_forms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->json('fields')->nullable(); // array of field definitions
            $table->enum('submit_action', ['contact','lead','both'])->default('contact');
            $table->text('success_message')->nullable();
            $table->string('redirect_url')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index('tenant_id');
        });

        Schema::create('web_form_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('form_id');
            $table->unsignedBigInteger('tenant_id');
            $table->json('data');
            $table->string('ip_address', 45)->nullable();
            $table->boolean('processed')->default(false);
            $table->timestamps();
            $table->index(['form_id','tenant_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('web_form_submissions');
        Schema::dropIfExists('web_forms');
    }
};
