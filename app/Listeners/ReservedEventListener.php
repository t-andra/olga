<?php

namespace App\Listeners;

use App\Events\ReservedEvent;
use Illuminate\Support\Facades\Log;

readonly class ReservedEventListener
{
    /**
     * Слушатель события ReservedEvent
     */
    public function handle(ReservedEvent $event): void
    {
        // Тут можно уведомления послать

        // Модель приёма: $event->appointment

        Log::info('Listener!'); // Тест, что слушатель работает
    }
}
