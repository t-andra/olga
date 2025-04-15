<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as OlgaCalendarBaseModel;

class OlgaCalendarBlock extends OlgaCalendarBaseModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'start',
    ];

    public function day(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(OlgaCalendarDay::class);
    }
}
