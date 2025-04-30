<?php $counter = 1; ?>
<table>
    <thead>
    <tr>
        <th colspan="6"></th>
    </tr>
    <tr>
        <th colspan="6"><b>{{'Акты выполненных работ с ' . $data['start_date'] . ' по ' . $data['finish_date']}}</b></th>
    </tr>
    <tr>
        <th colspan="6"><b>{{$trk->name . ', Блок: '}}{{isset($building->id) ? $building->name : 'Все' . ', Система: '}}{{isset($system->id) ? $system->name : 'Все'}}</b></th>
    </tr>
    <tr>
        <th colspan="6"><b>{{ 'Подразделение: '}}{{isset($division->id) ? $division->name : 'Все'}}</b></th>
    </tr>
    <tr><th colspan="6"></th></tr>
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
