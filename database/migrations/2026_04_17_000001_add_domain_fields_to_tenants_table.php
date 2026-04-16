<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('subdomain')->nullable()->unique()->after('slug');
            $table->string('custom_domain')->nullable()->unique()->after('subdomain');
            $table->enum('domain_status', ['inactive', 'pending', 'active', 'failed'])->default('inactive')->after('custom_domain');
            $table->string('domain_txt_record')->nullable()->after('domain_status');
            $table->timestamp('domain_verified_at')->nullable()->after('domain_txt_record');
            $table->string('primary_color', 7)->nullable()->after('dark_mode');
            $table->string('accent_color', 7)->nullable()->after('primary_color');
            $table->boolean('email_notifications')->default(true)->after('accent_color');
            $table->string('smtp_host')->nullable()->after('email_notifications');
            $table->integer('smtp_port')->nullable()->after('smtp_host');
            $table->string('smtp_user')->nullable()->after('smtp_port');
            $table->string('smtp_pass')->nullable()->after('smtp_user');
            $table->string('smtp_from_name')->nullable()->after('smtp_pass');
            $table->string('smtp_from_email')->nullable()->after('smtp_from_name');
            $table->enum('smtp_encryption', ['tls', 'ssl', 'none'])->default('tls')->after('smtp_from_email');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'subdomain','custom_domain','domain_status','domain_txt_record',
                'domain_verified_at','primary_color','accent_color','email_notifications',
                'smtp_host','smtp_port','smtp_user','smtp_pass','smtp_from_name',
                'smtp_from_email','smtp_encryption',
            ]);
        });
    }
};
