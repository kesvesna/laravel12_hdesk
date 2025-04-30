<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>Бланк чеклиста вытяжки</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <tr style="font-size: 10px;">
        <th style="background: #FAFAFA;"><b>&nbsp;&nbsp;Вытяжка&nbsp;&nbsp;</b></th>
        <th><b>I факт</b></th>
        <th style="background: #FAFAFA;"><b>Т выт</b></th>
        <th><b>Т двиг</b></th>
        <th style="background: #FAFAFA;"><b>Т подш</b></th>
        <th><b>Т к.двиг</b></th>
        <th style="background: #FAFAFA;"><b>Т к.серв</b></th>
        <th><b>I пасп</b></th>
        <th style="background: #FAFAFA;"><b>Hz факт</b></th>
        <th><b>Hz пасп</b></th>
        <th style="background: #FAFAFA;"><b>Q факт</b></th>
        <th><b>Q пасп</b></th>
    </tr>
    </thead>
    <tbody>
        {{$counter = 0}}
    @while($counter < 23)
        {{$counter++}}
        @if($counter == 13)
            <tr style="font-size: 10px;">
                <th style="background: #FAFAFA;"><b>&nbsp;&nbsp;Вытяжка&nbsp;&nbsp;</b></th>
                <th><b>I факт</b></th>
                <th style="background: #FAFAFA;"><b>Т выт</b></th>
                <th><b>Т двиг</b></th>
                <th style="background: #FAFAFA;"><b>Т подш</b></th>
                <th><b>Т к.двиг</b></th>
                <th style="background: #FAFAFA;"><b>Т к.серв</b></th>
                <th><b>I пасп</b></th>
                <th style="background: #FAFAFA;"><b>Hz факт</b></th>
                <th><b>Hz пасп</b></th>
                <th style="background: #FAFAFA;"><b>Q факт</b></th>
                <th><b>Q пасп</b></th>
            </tr>
        @else
            <tr>
                <td style="background: #FAFAFA;">&nbsp;</td>
                <td></td>
                <td style="background: #FAFAFA;"></td>
                <td></td>
                <td style="background: #FAFAFA;"></td>
                <td></td>
                <td style="background: #FAFAFA;"></td>
                <td></td>
                <td style="background: #FAFAFA;"></td>
                <td></td>
                <td style="background: #FAFAFA;"></td>
                <td></td>
            </tr>
        @endif
    @endwhile
    </tbody>
</table>
</body>

</html>

