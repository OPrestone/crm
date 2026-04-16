<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('id_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('full_name');
            $table->string('id_type'); // passport, national_id, driver_license, residence_permit
            $table->string('id_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('issuing_country')->nullable();
            $table->string('nationality')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            $table->string('document_front')->nullable(); // file path
            $table->string('document_back')->nullable();
            $table->string('selfie')->nullable();
            $table->string('status')->default('pending'); // pending, under_review, verified, rejected, expired
            $table->string('risk_level')->default('low'); // low, medium, high
            $table->integer('confidence_score')->default(0); // 0-100
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('id_verifications'); }
};
