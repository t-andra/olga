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
        Schema::create('olga_calendar_appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('olga_calendar_day_id');
            $table->foreign('olga_calendar_day_id')
                ->references('id')
                ->on('olga_calendar_days')
                ->onDelete('cascade');
            $table->unsignedTinyInteger('status')
                ->default(1)
                ->comment('0 - подтвержден, 1 - не подтвержден');

            $table->string('name', 80)
                ->comment('Имя клиента');
            $table->string('email', 80)
                ->nullable();
            $table->string('phone', 20)
                ->nullable();

            $table->time('start');
            $table->time('finish');
            $table->text('comment');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olga_calendar_appointments');
    }
};
