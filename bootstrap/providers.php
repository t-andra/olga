<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\EventServiceProvider::class, // Забыл! А без этого событие не получит слушателя
];
