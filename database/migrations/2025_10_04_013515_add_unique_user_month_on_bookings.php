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
        Schema::table('bookings', function (Blueprint $table) {
            // ป้องกันชื่อซ้ำ ถ้าเคยมี index อื่นตรงกันให้ลบก่อน (ไม่บังคับ)
            // $table->dropIndex('...');

            // ✅ บังคับ 1 คน/เดือน ได้แค่ 1 แถว
            $table->unique(['user_id', 'year', 'month'], 'uniq_user_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('uniq_user_month');
        });
    }
};
