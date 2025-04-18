<?php

namespace App\Services;



use App\Models\OlgaCalendarBlock;
use App\Models\OlgaCalendarDay;
use App\Models\OlgaCalendarSemaphore;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use stdClass;

class OlgaCalendarService
{
    private static $monthNamesRu = [
        '',
        'январь',
        'февраль',
        'март',
        'апрель',
        'май',
        'июнь',
        'июль',
        'август',
        'сентябрь',
        'октябрь',
        'ноябрь',
        'декабрь',
    ];

    public static function getDaysArray(Carbon $start, Carbon $finish): array
    {
        self::pruneBlocks();

        $days = [];

        $dbArray = self::getDaysFromDb($start, $finish);

        for ($date = clone $start; $date <= $finish; $date->addDay()) {
            $oneDay = self::getOneDay($date, $dbArray);
            $days[] = $oneDay;
        }

        return $days;
    }

    /**
     * Установка семафора в текущее время
     */
    public static function setSemaphore(): void
    {
        OlgaCalendarSemaphore::query()->delete();
        OlgaCalendarSemaphore::query()
            ->insert(['updated_at' => DB::Raw('Now()')]);
    }

    /**
     * Удаление устаревших блокировок
     */
    public static function pruneBlocks(): void
    {
        OlgaCalendarBlock::where('start', '<=', DB::raw('DATE_ADD(NOW(), INTERVAL -'.config('olga_calendar.block_seconds').' SECOND)'))
            ->delete();
    }

    private static function getOneDay(Carbon $date, array $dbArray): \stdClass
    {
        $oneDay = new \stdClass;

        $oneDay->date = clone $date;
        $oneDay->weekday = $date->dayOfWeek;
        $oneDay->row = $oneDay->weekday == 0 ? 6 : $oneDay->weekday - 1;

        $endOfMonth = clone $date;
        $endOfMonth->endOfMonth();

        $oneDay->monthRu = self::$monthNamesRu[$date->month];

        $oneDay->endOfMonth = $date->dayOfMonth == $endOfMonth->dayOfMonth;

        $fromDb = null;

        foreach ($dbArray as $dbDay) {
            if ($dbDay->date_at == $date->format('Y-m-d')) {
                $fromDb = $dbDay;
                break;
            }
        }

        // Если день не помечен в базе как неприёмный - значит, приемный
        $oneDay->status = $fromDb ? $fromDb->status : OlgaCalendarDay::STATUS_OPENED;

        $oneDay->name = $fromDb ? $fromDb->name : null;

        $appointments = [];
        if ($oneDay->status == OlgaCalendarDay::STATUS_OPENED && $fromDb) {
            $hours = 0;

            foreach ($fromDb->appointments as $appointment) {
                $ts = explode(':', $appointment->start);
                $start = Carbon::createFromTime($ts[0], $ts[1], $ts[2]);
                $hourStart = $ts[0];

                $ts = explode(':', $appointment->finish);
                $finish = Carbon::createFromTime($ts[0], $ts[1], $ts[2]);
                $hourFinish = $ts[0];
                $timeMinutes = ($finish->getTimestamp() - $start->getTimestamp()) / 60;
                $timeHours = ($finish->getTimestamp() - $start->getTimestamp()) / 3600;

                $appointments[] = [
                    'start' => $start,
                    'finish' => $finish,
                    'hourStart' => $hourStart,
                    'hourFinish' => $hourFinish,
                    'minutes' => $timeMinutes,
                    'hours' => $timeHours,
                ];
                $hours += $timeHours;
            }
            if ($hours >= config('olga_calendar.hours_per_day')) {
                $oneDay->status = OlgaCalendarDay::STATUS_BUSY;
            }
        }
        $oneDay->statusClass = match ($oneDay->status) {
            OlgaCalendarDay::STATUS_CLOSED => 'closed',
            OlgaCalendarDay::STATUS_OPENED => 'opened',
            OlgaCalendarDay::STATUS_BUSY => 'busy',
            OlgaCalendarDay::STATUS_BLOCKED => 'blocked',
        };
        $oneDay->appointments = json_encode($appointments);

        return $oneDay;

    }

    private static function getDaysFromDb(Carbon $start, Carbon $finish): array
    {
        $days = [];

        $records = OlgaCalendarDay::query()
            ->with('appointments')
            ->with('blocks')
            ->where('date_at', '>=', $start->format('Y-m-d'))
            ->where('date_at', '<=', $finish->format('Y-m-d'))
            ->orderBy('date_at', 'asc')
            ->get();

        foreach ($records as $record) {
            $one = new stdClass;
            $one->date_at = $record->date_at;

            $status = count($record->blocks) > 0 ? OlgaCalendarDay::STATUS_BLOCKED : $record->status;

            $one->status = $status;
            $one->name = $record->name;

            $appointments = [];

            foreach ($record->appointments as $appointment) {
                $two = new stdClass;
                $two->start = $appointment->start;
                $two->finish = $appointment->finish;
                $appointments[] = $two;
            }
            $one->appointments = $appointments;

            $days[] = $one;

        }

        return $days;
    }
}
