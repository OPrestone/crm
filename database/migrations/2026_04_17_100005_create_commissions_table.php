<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('commission_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('name');
            $table->enum('type', ['flat','percentage','tiered'])->default('percentage');
            $table->decimal('rate', 8, 4)->default(0); // % or flat amount
            $table->decimal('min_deal_value', 15, 2)->default(0);
            $table->json('tiers')->nullable(); // for tiered plans
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            $table->index('tenant_id');
        });

        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('deal_id');
            $table->unsignedBigInteger('plan_id')->nullable();
            $table->decimal('deal_value', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->enum('status', ['pending','approved','paid'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index(['tenant_id','user_id']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('commissions');
        Schema::dropIfExists('commission_plans');
    }
};
