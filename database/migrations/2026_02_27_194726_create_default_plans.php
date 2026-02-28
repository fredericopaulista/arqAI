<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('plans')->insert([
            ['name' => 'Free', 'monthly_price' => 0, 'image_limit' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pro', 'monthly_price' => 29.90, 'image_limit' => 50, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Enterprise', 'monthly_price' => 99.90, 'image_limit' => 500, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('plans')->truncate();
    }
};
