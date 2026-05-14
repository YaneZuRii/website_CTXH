<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('maQR')->nullable()->unique()->after('maSV');
        });

        DB::statement("UPDATE users SET maQR = CONCAT('CTXH-', UPPER(maSV)) WHERE maSV IS NOT NULL AND maSV <> '' AND maQR IS NULL");
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('maQR');
        });
    }
};
