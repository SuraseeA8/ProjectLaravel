<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('shop_detail', function (Blueprint $table) {
            // เพิ่มคอลัมน์ deleted_at
            $table->softDeletes()->after('description');
            // (ถ้าตารางนี้ไม่มี created_at/updated_at และอยากมีด้วย)
            // $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('shop_detail', function (Blueprint $table) {
            $table->dropSoftDeletes();
            // ถ้าเพิ่ม timestamps ไว้ด้านบน ก็ drop ด้วย:
            // $table->dropTimestamps();
        });
    }
};


