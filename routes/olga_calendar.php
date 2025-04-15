<?php

use Illuminate\Support\Facades\Route;

Route::get('/calendar', function () {
    return view('olga_calendar.calendar');
})->name('olga.calendar');

Route::get('/calendar/iframe', 'App\Http\Controllers\OlgaCalendar\OlgaCalendarController@iframe')
    ->name('olga.calendar.iframe');

// Количество секунд после последнего изменения в календаре. Чтоб не обновлять весь календарь без нужды
Route::get('/calendar/iframe/passed-after-last', 'App\Http\Controllers\OlgaCalendar\OlgaCalendarController@getSecondsPassed')
    ->name('olga.calendar.iframe.passed-after-last');

Route::post('/calendar/appointments', 'App\Http\Controllers\OlgaCalendar\OlgaCalendarController@storeAppointment')
    ->name('olga.calendar.store.appointment');

Route::post('/calendar/blocked', 'App\Http\Controllers\OlgaCalendar\OlgaCalendarController@setBlocked')
    ->name('olga.calendar.blocked');
