<?php

namespace App\Http\Controllers\OlgaCalendar;

use App\Events\ReservedEvent;
use App\Http\Controllers\Controller;
use App\Models\OlgaCalendarAppointment;
use App\Models\OlgaCalendarBlock;
use App\Models\OlgaCalendarDay;
use App\Models\OlgaCalendarSemaphore;
use App\Services\OlgaCalendarService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class OlgaCalendarController extends Controller
{
    public function iframe(): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory
    {
        $today = Carbon::today();

        $start = $today->addDay(config('olga_calendar.first_after'));
        $finish = clone $start;
        $finish->addDay(config('olga_calendar.days'));

        $days = OlgaCalendarService::getDaysArray($start, $finish);

        return view('olga_calendar.iframe', compact('days'));
    }

    /**
     * Возвращает количество секунд после последнего изменения календаря
     *
     * @return int
     */
    public function getSecondsPassed(): JsonResponse
    {
        $delta = OlgaCalendarSemaphore::query()
            ->select(DB::Raw('Now()-updated_at as delta'))
            ->first()?->delta;

        if (($delta === null) || ($delta > config('olga_calendar.force_timeout_seconds'))) {
            OlgaCalendarService::setSemaphore();
            $delta = 0;
        }

        return new JsonResponse(data: ['delta' => $delta], status: ResponseAlias::HTTP_OK, headers: [], json: false);
    }

    public function storeAppointment(Request $request): RedirectResponse|Response
    {
        try {
            // Проверим входящие данные
            $rules = [
                'name' => 'required|string',
                'email' => 'email',
                'phone' => 'required|string',
                'date_at' => 'required|string|date_format:Y-m-d',
                'start' => 'required|string|date_format:H:i:s',
                'finish' => 'required|string|date_format:H:i:s',
            ];

            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $dayId = OlgaCalendarDay::query()
                ->where('date_at', $request->get('date_at'))
                ->first()?->id;
            if (! $dayId) {
                $dayId = OlgaCalendarDay::query()
                    ->insertGetId(['date_at' => $request->get('date_at')]);
            }

            $appointment = new OlgaCalendarAppointment;
            $appointment->olga_calendar_day_id = $dayId;
            $appointment->name = $request->get('name');
            $appointment->email = $request->get('email');
            $appointment->phone = $request->get('phone');
            $appointment->comment = $request->get('comment');
            $appointment->start = $request->get('start');
            $appointment->finish = $request->get('finish');

            $appointment->save();

            // Инициируем событие - чтобы "передаать пас" обработчику события.
            // Это эффективное разделение задачи между разработчиками.
            // Здесь применено в образовательніх целях
            event(new ReservedEvent($appointment));

            OlgaCalendarBlock::query()
                ->where('olga_calendar_day_id', $dayId)
                ->delete();
            OlgaCalendarService::pruneBlocks();

            OlgaCalendarService::setSemaphore();

            return response()->redirectToRoute('olga.calendar.iframe');
        } catch (ValidationException $e) {
            return new Response($e->errors(), ResponseAlias::HTTP_BAD_REQUEST);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return new Response($e->getMessage(), ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

    public function setBlocked(Request $request): void
    {
        OlgaCalendarService::pruneBlocks();
        OlgaCalendarService::setSemaphore();

        $dayId = OlgaCalendarDay::query()
            ->where('date_at', $request->get('start'))
            ->first()?->id;
        if (! $dayId) {
            $dayId = OlgaCalendarDay::query()
                ->insertGetId(['date_at' => $request->get('start')]);
        }

        OlgaCalendarBlock::query()
            ->insert(['olga_calendar_day_id' => $dayId, 'start' => DB::Raw('Now()')]);

    }
}
