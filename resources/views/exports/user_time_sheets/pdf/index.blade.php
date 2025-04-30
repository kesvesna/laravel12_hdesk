<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
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

<table>
    <thead>
    <tr>
        <th>{{$user['name']}}</th>
        <th colspan="2">{{$user_result_time_sheet['year'] . '-' . $user_result_time_sheet['month']}}</th>
        <th>{{$user_result_time_sheet['result'] . ' ч'}}</th>
        <th>{{$user_result_time_sheet['overtime'] . ' ч'}}</th>
    </tr>
    <tr>
        <th colspan="5">&nbsp;</th>
    </tr>
    <tr>
        <th><b>Дата</b></th>
        <th><b>Начало</b></th>
        <th><b>Конец</b></th>
        <th><b>Всего, ч</b></th>
        <th><b>Сверх, ч</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($user_time_sheets as $user_time_sheet)
        <tr>
            <td>{{$user_time_sheet['date']}}</td>
            <td>{{date('H:i', strtotime($user_time_sheet['start']))}}</td>
            <td>{{date('H:i', strtotime($user_time_sheet['finish']))}}</td>
            <td>{{$user_time_sheet['result'] != '00:00:00' ?date('H:i', strtotime($user_time_sheet['result'])) : null}}</td>
            <td>{{$user_time_sheet['overtime'] != '00:00:00' ? date('H:i', strtotime($user_time_sheet['overtime'])) : null}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>

