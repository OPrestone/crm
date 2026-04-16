<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sales_quotas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id')->nullable(); // null = tenant-wide
            $table->string('period', 7); // YYYY-MM or YYYY for annual
            $table->decimal('amount', 15, 2)->default(0);
            $table->timestamps();
            $table->unique(['tenant_id','user_id','period']);
            $table->index('tenant_id');
        });
    }
    public function down(): void {
        Schema::dropIfExists('sales_quotas');
    }
};
