<?php

namespace App\Models;

class OlgaCalendarAppointment extends OlgaCalendarBaseModel
{
    protected $fillable = [
        'olga_calendar_day_id',
        'name',
        'status',
        'email',
        'phone',
        'start',
        'finish',
        'comment',
    ];

    public function day(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OlgaCalendarDay::class);
    }
}
