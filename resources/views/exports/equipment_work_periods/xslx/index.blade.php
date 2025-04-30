<?php $counter = 1; ?>
<table>
    <thead>
    <tr>
        <th colspan="6"></th>
    </tr>
    <tr>
        <th colspan="6"><b>{{$trk->name . ', ' . $system->name}}</b></th>
    </tr>
    <tr>
        <th colspan="6"><b>{{'Технические мероприятия с ' . $start_date . ' по ' . $finish_date}}</b></th>
    </tr>
    <tr>
        <th colspan="6"></th>
    </tr>
    <tr>
        <th><b>№</b></th>
        <th><b>Помещение</b></th>
        <th><b>Оборудование</b></th>
        <th><b>Мероприятие</b></th>
        <th><b>Последнее</b></th>
        <th><b>Следующее</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($equipment_work_periods as $equipment_work_period)
        <tr>
            <td align="center">{{$counter++}}</td>
            <td align="center">{{$equipment_work_period->trk_equipment->trk_room->room->name}}</td>
            <td align="center">{{$equipment_work_period->trk_equipment->equipment_name->name}}</td>
            <td align="center">{{$equipment_work_period->work_name->name}}</td>
            <td align="center">{{$equipment_work_period->last_was_at}}</td>
            <td align="center">{{$equipment_work_period->next_to_be_at}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
