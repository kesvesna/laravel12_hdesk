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
<?php $brand = $trk_room_counter->brand->name ?? 'бренд отсутствует'; ?>
<h4>{{$trk_room_counter->trk->name . ', ' . $trk_room_counter->floor->name . ', ' . $brand . ', ' . $trk_room_counter->organization->name}}</h4>
<h4>{{'№ ' . $trk_room_counter->number . ', ' . $trk_room_counter->counter_type->name}}</h4>
<table>
    <thead>
    <tr>
        <th><b>Дата</b></th>
        <th><b>Тариф</b></th>
        <th><b>Предыдущие</b></th>
        <th><b>Текущие</b></th>
        <th><b>Коэффициент</b></th>
        <th><b>Итого</b></th>
        <th><b>Комментарии</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($counter_counts as $counter_count)
        <tr>
            <td>{{$counter_count->date}}</td>
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

