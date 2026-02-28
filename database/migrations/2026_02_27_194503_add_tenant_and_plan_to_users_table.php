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
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('tenant_id')->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->after('tenant_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('credits')->default(0)->after('password');
            $table->string('status')->default('active')->after('credits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['tenant_id']);
            $table->dropColumn('tenant_id');
            $table->dropForeign(['plan_id']);
            $table->dropColumn('plan_id');
            $table->dropColumn(['credits', 'status']);
        });
    }
};
