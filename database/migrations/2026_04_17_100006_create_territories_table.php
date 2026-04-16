<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('territories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('type', ['geographic','account','industry','custom'])->default('custom');
            $table->json('rules')->nullable(); // countries, states, industries, etc.
            $table->string('color', 7)->default('#6c757d');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index('tenant_id');
        });

        Schema::create('territory_user', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('territory_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->unique(['territory_id','user_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('territory_user');
        Schema::dropIfExists('territories');
    }
};
