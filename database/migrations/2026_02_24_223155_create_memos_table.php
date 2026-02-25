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
        Schema::create('memos', function (Blueprint $table) {
            $table->id();
            // 大分類と紐付け（大分類が消えたらメモも消える設定）
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            $table->text('content'); // メモの内容
            $table->boolean('is_checked')->default(false); // チェック状態
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memos');
    }
};
