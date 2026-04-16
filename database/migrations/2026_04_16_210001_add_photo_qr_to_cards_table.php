<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('photo')->nullable()->after('name');
            $table->string('qr_data')->nullable()->after('photo'); // what QR encodes
        });
        Schema::table('contacts', function (Blueprint $table) {
            if (!Schema::hasColumn('contacts', 'avatar')) {
                $table->string('avatar')->nullable()->after('notes');
            }
        });
    }
    public function down(): void {
        Schema::table('cards', function (Blueprint $t) {
            $t->dropColumn(['photo','qr_data']);
        });
    }
};
