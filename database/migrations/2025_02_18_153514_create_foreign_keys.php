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
        // Добавление внешних ключей для таблицы blocks
        Schema::table('blocks', function (Blueprint $table) {
         //  $table->foreignId('anketa_id')->constrained()->onDelete('cascade');
            //   $table->foreignId('question_id')->constrained('questions');
               $table->foreignId('anketa_id')->constrained('anketas');
        });

        // Добавление внешних ключей для таблицы questions
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('block_id')->constrained('blocks');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foreign_keys');
    }
};
