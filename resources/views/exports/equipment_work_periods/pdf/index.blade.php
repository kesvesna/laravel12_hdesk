<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Табель</title>
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

<h4>{{$trk->name . ', ' . $system->name}}</h4>
<h4>{{'Технические мероприятия с ' . $start_date . ' по ' . $finish_date}}</h4>
<?php $counter = 1; ?>
<table>
    <thead>
    <tr>
        <th>№</th>
        <th>Помещение</th>
        <th>Оборудование</th>
        <th>Мероприятие</th>
        <th>Последнее</th>
        <th>Следующее</th>
    </tr>
    </thead>
    <tbody>
    @foreach($equipment_work_periods as $equipment_work_period)
        <tr>
            <td align="center">{{$counter++}}</td>
            <td align="center">{{$equipment_work_period->trk_equipment->trk_room->room->name}}</td>
            <td align="center">{{$equipment_work_period->trk_equipment->equipment_name->name}}</td>
            <td align="center">{{$equipment_work_period->work_name->name}}</td>
            <td align="center">{{$equipment_work_period->last_was_at}}</td>
            <td align="center">{{$equipment_work_period->next_to_be_at}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>

