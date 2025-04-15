<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<h1>Страница твоего сайта</h1>
<p>Календарь вставлен как виджет в ифрейме</p>

<iframe width="400" height="400" src="{{ route('olga.calendar.iframe') }}"></iframe>

</body>
</html>
