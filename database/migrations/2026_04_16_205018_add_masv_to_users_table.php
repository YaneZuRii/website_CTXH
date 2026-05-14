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
        // Thêm cột maSV vào sau cột name, cho phép để trống (nullable) và không được trùng (unique)
        $table->string('maSV')->nullable()->unique()->after('name');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('maSV'); // Lệnh để xóa cột nếu muốn quay lại
    });
}
};
