<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Заявки в эксплуатацию</title>
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


<h4>{{'Заявки с ' . $data['start_date'] . ' по ' . $data['finish_date']}}</h4>
<h4>{{$trk->name . ', ' . $division->name . ', ' . $status}}</h4>

<table>
    <thead>
    <tr>
        <th>Заявка создана</th>
        <th>Задача</th>
        @if('Новые' != $status)
            <th>Что сделано</th>
            @if('Выполняются' === $status)
                <th> % </th>
            @endif
            @if('Выполнены' === $status)
                <th>Когда</th>
            @endif
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($applications as $application)
            <tr>
                <td align="center">{{$application->created_at}}</td>
                <td align="center">{{$application->trouble_description}}</td>
                @if($application->done_percents > 0)
                    <td>{{$application->result_description}}</td>
                    @if($application->done_percents < 100)
                        <td>{{$application->done_percents}}</td>
                    @endif
                    @if($application->done_percents == 100)
                        <td>{{$application->updated_at}}</td>
                    @endif
                @endif
            </tr>
    @endforeach
    </tbody>
</table>
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>

