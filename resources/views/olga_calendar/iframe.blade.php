<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Calendar</title>
    <link rel="stylesheet" type="text/css" href="/css/olga_calendar.css" />
</head>
<body>

<div class="olga-calendar">

    <div class="day-name" style="left:0px;top:40px">пн</div>
    <div class="day-name" style="left:0px;top:80px">вт</div>
    <div class="day-name" style="left:0px;top:120px">ср</div>
    <div class="day-name" style="left:0px;top:160px">чт</div>
    <div class="day-name" style="left:0px;top:200px">пт</div>
    <div class="day-name" style="left:0px;top:240px">сб</div>
    <div class="day-name" style="left:0px;top:280px">вс</div>


    @php
        $column=1;
        $row=$days[0]->row;
        $size = 40;
    @endphp

    @foreach($days as $index=>$date)
        @if($index == 0 || $date->date->dayOfMonth == 1)
            <div class="month" style="left:{{ $column*$size }}px; top:1px">
                {{ $date->monthRu }}
            </div>
        @endif

       <div class="{{ $date->statusClass }}" data-date="{{ $date->date->format('Y-m-d') }}" data-appointments="{{ $date->appointments }}" style="left:{{ $column*$size }}px; top:{{ $row*$size + $size }}px"@if($date->statusClass=='blocked') title="Блокирован на оформление заказа"    @elseif($date->name) title="{{ $date->name }}"@endif>
            {{ $date->date->dayOfMonth }}
       </div>

        @if($date->endOfMonth)
            @php
                $column += 2;
            @endphp

            @elseif($row == 6) {{-- воскресенье --}}
            @php
                $column++;
                $row = 0;
            @endphp

        @else
            @php
                $row++;
            @endphp
        @endif
    @endforeach

    <div id="form1" class="form">
        <div class="close-button">&times;</div>
        <p id="form1date1" class="formdate"></p>
        <p>Выберите свободное время приема</p>
        <div id="grid">
            @for($line=0;$line<2;$line++)
                @for($column=0;$column<4;$column++)
                 <div id="hour{{ 10+$line*4+$column }}" style="left:{{ $column*60+50 }}px;top:{{ $line*60+20 }}px">{{ 10+$line*4+$column }}:00</div>
                @endfor
            @endfor
        </div>
    </div>

    <div id="form2" class="form">
        <div class="close-button">&times;</div>
        <p id="form1date2" class="formdate"></p>
        <p id="times"></p>
        <form method="post" id="form2form" action="{{ route('olga.calendar.store.appointment') }}">
        {{ csrf_field() }} {{-- Защита от убогих хакеров --}}
            <input type="hidden" name="start" value="" />
            <input type="hidden" name="finish" value="" />
            <input type="hidden" name="date_at" value="" />
        <div id="table">

            <div> <input name="name" type="text" value="{{ Auth::user()?->name ?? '' }}" placeholder="Ваше имя"/> </div>

            <div> <input name="phone" type="text" value="{{ Auth::user()?->phone ?? '' }}" placeholder="Телефон"/></div>

            <div> <input name="email"  type="text" value="{{ Auth::user()?->phone ?? '' }}" placeholder="e-mail"/></div>

            <div><textarea name="comment" rows="3" cols="40" placeholder="Пожелания"></textarea></div>

            <div>
                <p>Отправляя личные данные, Вы даете согласие на их обработку</p>
            </div>

            <div><input type="submit" value="Записаться" /></div>


        </div>
        </form>
    </div>


</div>

<script type="text/javascript">
    const timeOut = {{ config('olga_calendar.timeout_seconds') }}*1000; {{-- Таймаут задается в миллисекундах --}}
    let href = self.location.href;
    href = href.replace(/\?.+/,'');
    let domain = href.replace(/\/calendar\/iframe/,'');
    const url = domain + '{{ route('olga.calendar.iframe.passed-after-last',[],false) }}';
    let pause = false; {{-- Для возможности остановки обновления при оформлении заявки --}}
    const blockUrl = '{{ route('olga.calendar.blocked',[],false) }}';
    const _token='{{ csrf_token() }}';
</script>

<script type="text/javascript" src="/js/olga_calendar.js"></script>
<script type="text/javascript">
    self.onload = onloadCalendar(); {{-- После загрузки страницы привязываем обработчики к кнопкам.
                                         Привязать их сразу, как делали всегда, почему-то теперь дурной тон --}}
</script>
</body>
</html>
