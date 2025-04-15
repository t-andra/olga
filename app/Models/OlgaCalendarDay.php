<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as OlgaCalendarBaseModel;
use Illuminate\Support\Facades\DB;

class OlgaCalendarDay extends OlgaCalendarBaseModel
{
    const STATUS_OPENED = 1;

    const STATUS_CLOSED = 0;

    const STATUS_BUSY = 3;

    const STATUS_BLOCKED = 4;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'date_at',
        'status',
    ];

    public function appointments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OlgaCalendarAppointment::class)->orderBy('start');
    }

    public function blocks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(OlgaCalendarBlock::class)
            ->where('start', '>', DB::raw('DATE_ADD(NOW(), INTERVAL -'.config('olga_calendar.block_seconds').' SECOND)'));
    }
}
