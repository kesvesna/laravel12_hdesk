<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <title>Бланк чеклиста притока</title>
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
        <th style="background: #FAFAFA;"><b>&nbsp;&nbsp;Приток&nbsp;&nbsp;</b></th>
        <th><b>Т ул</b></th>
        <th style="background: #FAFAFA;"><b>Т уст</b></th>
        <th><b>Т прит</b></th>
        <th style="background: #FAFAFA;"><b>Т двиг</b></th>
        <th><b>Т подш</b></th>
        <th style="background: #FAFAFA;"><b>Т к.двиг</b></th>
        <th><b>Т к.серв</b></th>
        <th style="background: #FAFAFA;"><b>I факт</b></th>
        <th><b>I пасп</b></th>
        <th style="background: #FAFAFA;"><b>Hz факт</b></th>
        <th><b>Hz пасп</b></th>
        <th style="background: #FAFAFA;"><b>Q факт</b></th>
        <th><b>Q пасп</b></th>
    </tr>
    </thead>
    <tbody>
        {{$counter = 0}}
    @while($counter < 24)
        {{$counter++}}
        @if($counter == 13)
            <tr style="font-size: 10px;">
                <th style="background: #FAFAFA;"><b>&nbsp;&nbsp;Приток&nbsp;&nbsp;</b></th>
                <th><b>Т ул</b></th>
                <th style="background: #FAFAFA;"><b>Т уст</b></th>
                <th><b>Т прит</b></th>
                <th style="background: #FAFAFA;"><b>Т двиг</b></th>
                <th><b>Т подш</b></th>
                <th style="background: #FAFAFA;"><b>Т к.двиг</b></th>
                <th><b>Т к.серв</b></th>
                <th style="background: #FAFAFA;"><b>I факт</b></th>
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
                <td style="background: #FAFAFA;"></td>
                <td></td>
            </tr>
        @endif
    @endwhile
    </tbody>
</table>
<p>Узел гидравлической обвязки, заслонки</p>
<table>
    <thead>
    <tr style="font-size: 10px;">
        <th style="background: #FAFAFA;"><b>&nbsp;&nbsp;Приток&nbsp;&nbsp;</b></th>
        <th><b>ГВС %</b></th>
        <th style="background: #FAFAFA;"><b>ГВС вх</b></th>
        <th><b>ГВС вых</b></th>
        <th style="background: #FAFAFA;"><b>ХВС %</b></th>
        <th><b>ХВС вх</b></th>
        <th style="background: #FAFAFA;"><b>ХВС вых</b></th>
        <th><b>Прит %</b></th>
        <th style="background: #FAFAFA;"><b>Рецирк %</b></th>
        <th><b>Рекуп %</b></th>
        <th style="background: #FAFAFA;"><b>I гвс ф</b></th>
        <th><b>I гвс пасп</b></th>
        <th style="background: #FAFAFA;"><b>I гл ф</b></th>
        <th><b>I гл пасп</b></th>
    </tr>
    </thead>
    <tbody>
    {{$counter = 0}}
    @while($counter < 23)
        {{$counter++}}
        @if($counter == 13)
            <tr style="font-size: 10px;">
                <th style="background: #FAFAFA;"><b>&nbsp;&nbsp;Приток&nbsp;&nbsp;</b></th>
                <th><b>ГВС %</b></th>
                <th style="background: #FAFAFA;"><b>ГВС вх</b></th>
                <th><b>ГВС вых</b></th>
                <th style="background: #FAFAFA;"><b>ХВС %</b></th>
                <th><b>ХВС вх</b></th>
                <th style="background: #FAFAFA;"><b>ХВС вых</b></th>
                <th><b>Прит %</b></th>
                <th style="background: #FAFAFA;"><b>Рецирк %</b></th>
                <th><b>Рекуп %</b></th>
                <th style="background: #FAFAFA;"><b>I гвс ф</b></th>
                <th><b>I гвс пасп</b></th>
                <th style="background: #FAFAFA;"><b>I гл ф</b></th>
                <th><b>I гл пасп</b></th>
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
                <td style="background: #FAFAFA;"></td>
                <td></td>
            </tr>
        @endif
    @endwhile
    </tbody>
</table>
</body>

</html>

