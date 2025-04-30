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


<h4>{{'Акты выполненных работ с ' . $data['start_date'] . ' по ' . $data['finish_date']}}</h4>
<h4>{{$trk->name . ', Блок: '}}{{isset($building->id) ? $building->name : 'Все' . ', Система: '}}{{isset($system->id) ? $system->name : 'Все'}}</h4>
<h4>{{ 'Подразделение: '}}{{isset($division->id) ? $division->name : 'Все'}}</h4>

<?php $counter = 1; ?>
<table>
    <thead>
    <tr>
        <th>№</th>
        <th>Дата</th>
        <th>Помещение</th>
        <th>Оборудование</th>
        <th>Мероприятия</th>
        <th>Исполнители</th>
    </tr>
    </thead>
    <tbody>
    @foreach($avrs as $avr)
        @foreach($avr->avr_equipments as $equipment)
            <tr>
                <td align="center">{{$counter++}}</td>
                <td align="center">{{$avr->date}}</td>
                <td align="center">{{$avr->trk_room->room->name}}</td>
                <td align="center">
                    {{$equipment->trk_equipment->equipment_name->name}}<br>
                </td>
                <td align="center">
                    @foreach($avr->avr_works as $work)
                        @if($equipment->trk_equipment_id == $work->trk_equipment_id)
                            {{$work->work_name->name}}<br>
                        @endif
                    @endforeach
                </td>
                <td align="center">
                    @foreach($avr->avr_executors as $executor)
                        {{$executor->executor_name->name}}<br>
                    @endforeach
                </td>
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>

