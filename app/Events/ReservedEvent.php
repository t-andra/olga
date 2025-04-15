<?php

namespace App\Events;

use App\Models\OlgaCalendarAppointment;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Cобытие бронирования (резервирования) приема
 */
class ReservedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public OlgaCalendarAppointment $appointment) {}
}
