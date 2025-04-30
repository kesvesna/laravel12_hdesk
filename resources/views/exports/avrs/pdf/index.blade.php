<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>Акт выполненных работ</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $monthes = array(
        1 => 'января', 2 => 'февраля', 3 => 'марта', 4 => 'апреля',
        5 => 'мая', 6 => 'июня', 7 => 'июля', 8 => 'августа',
        9 => 'сентября', 10 => 'октября', 11 => 'ноября', 12 => 'декабря'
    ); ?>
</head>
<body>
<div style="padding-left: 2em;">
    <p style="text-align: center; margin-bottom: -1em; padding-bottom: -1em;"><b>Акт выполненных работ от {{date('"j" ', strtotime($avr->date))}}{{ $monthes[(date('n', strtotime($avr->date)))]}}{{ date(" Y", strtotime($avr->date)) . ' г.'}}</b></p>
    <br>
    <p>{{ $avr->trk_room->trk->name . ', ' . $avr->trk_room->building->name . ', ' . $avr->trk_room->floor->name . ', пом. ' . $avr->trk_room->room->name . ', ' . $avr->system->name}}</p>
    <p></p>
        @forelse($avr->avr_equipments as $avr_equipment)
        <span style="font-weight: 500;">{{$avr_equipment->trk_equipment->equipment_name->name}}</span><br>
            <span>
                @forelse($avr->avr_works as $avr_work)
                    @if($avr_equipment->trk_equipment->id == $avr_work->trk_equipment_id)
                        <span>{{$avr_work->work_name->name}}</span>
                        @if(!empty($avr_work->description))
                            <span>{{' (' . $avr_work->description . ')'}}</span><br>
                        @else
                            <br>
                        @endif
                    @endif
                @empty
                    <span>не заполнено ...</span>
                @endforelse
            </span><br>
        @empty
        @endforelse
    </p>
{{--        <p style="margin-bottom: 0em;"><b>Комментарии:</b> <span>{{$avr->description ?? 'нет'}}</span></p>--}}


        <p style="margin-bottom: 0em;"><b>Использованные запчасти:</b></p>
                @forelse($avr->avr_spare_parts as $avr_spare_part)
                        <span>{{$avr_spare_part->spare_part_name->name . ' (' . $avr_spare_part->spare_part_model . ', ' . $avr_spare_part->value . ')'}}</span><br>
                    @empty
                        <span>нет</span><br>
                    @endforelse

        <br><span><b>Исполнители:</b></span><br>
        @foreach($avr->avr_executors as $avr_executor)
            @if(!empty($avr_executor->executor_name->organization->name))
                <span>{{$avr_executor->executor_name->organization->name . ', ' }}</span>
            @endif
            @if(!empty($avr_executor->executor_name->division->name))
                    <span>{{$avr_executor->executor_name->division->name . ', ' }}</span>
                @endif
                <span>{{$avr_executor->executor_name->name . ' /_____________________'}}</span><br>
                @endforeach
        <br><p><b>Заказчик: </b>
        <span> ____________________/____________________</span>
        <p>Замечания заказчика:</p>
        <span></span>
        <hr>
        <p></p>
        <hr>
</div>
</body>
</html>
