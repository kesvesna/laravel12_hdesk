<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Арендаторы</title>
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


<h4>{{'Арендаторы ' . $trk->name . ', ' . date('d-m-Y')}}</h4>
<h4>{{'Этаж: '}}{{$floor->name ?? 'Все'}}</h4>

<table>
    <thead>
    <tr>
        <th><b>Блок</b></th>
        <th><b>Этаж</b></th>
        <th><b>Помещение</b></th>
        <th><b>Бренд</b></th>
        <th><b>Юр.лицо</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($renters as $renter)
        <tr>
            <td>{{$renter->trk_room->building->name}}</td>
            <td>{{$renter->trk_room->floor->name}}</td>
            <td>{{$renter->trk_room->room->name}}</td>
            <td>{{$renter->brand->name ?? 'отсутствует'}}</td>
            <td>{{$renter->organization->name ?? 'отсутствует'}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>

