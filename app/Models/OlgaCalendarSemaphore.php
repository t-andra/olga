<?php

namespace App\Models;

class OlgaCalendarSemaphore extends OlgaCalendarBaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'updated_at',
    ];

    /**
     * Нарушили "конвенцию" по именам таблицы и модели, поэтому указали имя таблицы явно
     *
     * @var string
     */
    protected $table = 'olga_calendar_semaphore';
}
