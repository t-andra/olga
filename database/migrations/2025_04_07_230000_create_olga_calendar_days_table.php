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
        Schema::create('olga_calendar_days', function (Blueprint $table) {
            $table->id();
            $table->string('name')
                ->nullable()
                ->comment('Название дня, если есть. Например, Пасха');
            $table->date('date_at');
            $table->unsignedTinyInteger('status')
                ->default(1)
                ->comment('0 - неприемный, 1 - есть свободные часы 2 - нет свободных часов');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('olga_calendar_days');
    }
};
