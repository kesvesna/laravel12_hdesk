<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>Показания счетчиков</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
</head>
<body>

<style>

    table {
        width: 100%;
    }

    table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
        border-style: groove;
        border-color: lightgrey;
    }

</style>
<h4>{{$trk->name . ', ' . $floor}}</h4>
<h4>{{'С ' . $start_date . ' по ' . $finish_date}}</h4>
<table>
    <thead>
    <tr>
        <th><b>Бренд</b></th>
        <th><b>Юр. лицо</b></th>
        <th><b>Счетчик №</b></th>
        <th><b>Тариф</b></th>
        <th><b>Пред.</b></th>
        <th><b>Тек.</b></th>
        <th><b>Коэфф.</b></th>
        <th><b>Итого</b></th>
        <th><b>Комментарии</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($counter_counts as $counter_count)
        <tr>
            <td>{{$counter_count->trk_room_counter->brand->name ?? 'отсутствует'}}</td>
            <td>{{$counter_count->trk_room_counter->organization->name}}</td>
            <td>{{$counter_count->trk_room_counter->number}}</td>
            <td>{{$counter_count->tariff ? 'день' : 'ночь'}}</td>
            <td>{{$counter_count->prev_count}}</td>
            <td>{{$counter_count->count}}</td>
            <td>{{$counter_count->trk_room_counter->coefficient}}</td>
            <td>{{$counter_count->result}}</td>
            <td>{{$counter_count->comment}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>

