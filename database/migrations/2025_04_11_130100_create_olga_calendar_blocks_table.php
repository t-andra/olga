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
        Schema::create('olga_calendar_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('olga_calendar_day_id');
            $table->foreign('olga_calendar_day_id')
                ->references('id')
                ->on('olga_calendar_days')
                ->onDelete('cascade');

            $table->datetime('start');

            $table->timestamps();
            $table->comment('Чтоб заблокировать дату на кол-во секунд, указанное в конфиге');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olga_calendar_blocks');
    }
};
