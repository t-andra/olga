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
        Schema::create('olga_calendar_semaphore', function (Blueprint $table) {
            $table->id();
            $table->datetime('updated_at');
            $table->engine = 'MEMORY'; // Данные будут храниться в оперативке
            $table->comment('Хранение даты последнего обновленяи календаря для скрипта обновления');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olga_calendar_semaphore');
    }
};
