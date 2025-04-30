<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Акты выполненных работ</title>
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

<h4>{{'Оборудование ' . $trk->name}}</h4>
@if(isset($building->id))
    <p>{{'Блок: ' . $building->name}}</p>
@endif
@if(isset($floor->id))
    <p>{{'Этаж: ' . $floor->name}}</p>
@endif
@if(isset($room->id))
    <p>{{'Помещение: ' . $room->name}}</p>
@endif
<p>{{'Система: '}}{{isset($system->id) ? $system->name : 'Все'}}</p>

<?php $counter = 1; ?>
<table>
    <thead>
    <tr>
        <th>№</th>
        @if(!isset($building->id))
            <th>Блок</th>
        @endif
        @if(!isset($floor->id))
            <th>Этаж</th>
        @endif
        @if(!isset($room->id))
            <th>Помещение</th>
        @endif
        <th>Оборудование</th>
    </tr>
    </thead>
    <tbody>
    @foreach($equipments as $equipment)
        <tr>
            <td>{{$counter++}}</td>
            @if(!isset($building->id))
                <td>{{$equipment->trk_room->building->name}}</td>
            @endif
            @if(!isset($floor->id))
                <td>{{$equipment->trk_room->floor->name}}</td>
            @endif
            @if(!isset($room->id))
                <td>{{$equipment->trk_room->room->name}}</td>
            @endif
            <td>{{$equipment->equipment_name->name}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>

