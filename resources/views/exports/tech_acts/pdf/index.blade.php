<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Технический акт</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $monthes = array(
        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
        5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
    ); ?>
</head>
<body>
<div style="padding-left: 2em;">
    <p style="text-align: right; margin-bottom: -1em; padding-bottom: -1em; font-size: 12px; font-family: 'Times New Roman';"><b>Утверждаю:</b></p>
    <p></p>
    <p style="text-align: right;  font-size: 10px; font-family: 'Times New Roman';"><u>______________/Главный инженер ДЭТК/</u>
    </p>
    <p style="text-align: right; font-size: 12px; font-family: 'Times New Roman';"><u>"____"________________</u>{{date('Y')}}г.</p>
    <br>
    <p style="text-align: center; margin-bottom: -1em; padding-bottom: -1em; font-size: 11px; font-family: 'Arial';"><b>Технический акт</b></p>
    <p style="text-align: center; font-size: 11px; font-family: 'Arial';"><b>о поломке, выходе из строя, бое, утере</b></p>
    <br>
    <style>
        body {
            font-size: 11px;
            font-family: 'Arial';
        }
    </style>
    <p style="margin-bottom: -1em; padding-bottom: -1em;">Дата
        составления {{date('"j" ', strtotime($tech_act->write_at))}}{{ $monthes[(date('n', strtotime($tech_act->write_at)))]}}{{ date(" Y", strtotime($tech_act->write_at)) . ' г.'}}</p>
    <p>Объект: ТРК {{ '"' . $tech_act->trk->name . '"'}}</p>
    <br>
    <p style="margin-bottom: 0em;"><b>Комиссия в составе:</b></p>
    <table>
        <tbody>
        @foreach($tech_act->executors as $executor)
            <tr>
                <td>{{$executor->function->name}}</td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{$executor->name}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
    <br>
    <p style="margin-bottom: -1em; padding-bottom: -1em; padding-top: 0.5em;"><b>Установила:</b></p>
    <ol>
        <li>Дата: {{date('d.m.Y', strtotime($tech_act->inspection_at)) . ' г.'}}</li>
        <li>Месторасположение: {{$tech_act->room_name}}</li>
        <li>Оборудование в котором поломка: {{$tech_act->equipment_name}}</li>
        <li>Что сломалось: {{$tech_act->trouble_description}}</li>
        <li>Причина: {{$tech_act->reason_description}}</li>
        <li>Способ восстановления: {{$tech_act->recovery_method_description}}</li>
        <li style="margin-bottom: -1em; padding-bottom: -1em;">Ориентировочная стоимость восстановления:</li>
        @foreach($tech_act->spare_parts as $spare_part)
            <p style="margin-bottom: -1em; padding-bottom: -1em;">{{$spare_part->spare_part_name}}{{' ' . $spare_part->price . 'руб.'}}</p>
        @endforeach
    </ol>
    <br>
    <p style="margin-bottom: -1em; padding-bottom: -1em; padding-top: 0.5em;"><b>Комиссия решила:</b></p>
    <ol>
        @foreach($tech_act->resumes as $resume)
            <li>{{$resume->resume_name->name}}</li>
        @endforeach
    </ol>
    <br>
    <p style="margin-bottom: 0; padding-bottom: -1em;"><b>Акт составлен:</b></p>
    <table>
        <tbody>
        @foreach($tech_act->executors as $executor)
            <tr>
                <td>{{$executor->function->name}}</td>
                <td>&nbsp;_______________&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td>/________________________/</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
</body>
</html>
