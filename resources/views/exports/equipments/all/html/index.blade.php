<?php $counter = 1; ?>
<table>
    <thead>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td colspan="5"><b>{{'Оборудование ' . $trk->name}}</b></td>
    </tr>
    @if(isset($building->id))
        <tr>
            <td colspan="5"><b>{{'Блок: ' . $building->name}}</b></td>
        </tr>
    @endif
    @if(isset($floor->id))
        <tr>
            <td colspan="5"><b>{{'Этаж: ' . $floor->name}}</b></td>
        </tr>
    @endif
    @if(isset($room->id))
        <tr>
            <td colspan="5"><b>{{'Помещение: ' . $room->name}}</b></td>
        </tr>
    @endif
    @if(isset($system->id))
        <tr>
            <td colspan="5"><b>{{'Система: ' . $system->name}}</b></td>
        </tr>
    @endif
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <th><b>№</b></th>
        @if(!isset($building->id))
            <th><b>Блок</b></th>
        @endif
        @if(!isset($floor->id))
            <th><b>Этаж</b></th>
        @endif
        @if(!isset($room->id))
            <th><b>Помещение</b></th>
        @endif
        <th><b>Оборудование</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($equipments as $equipment)
        <tr>
            <td>{{$counter++}}</td>
            @if(!isset($building->id))
                <td>{{$equipment->trk_room->building->name}}</td>
            @endif
            @if(!isset($floor->id))
                <td>{{$equipment->trk_room->floor->name}}</td>
            @endif
            @if(!isset($room->id))
                <td>{{$equipment->trk_room->room->name}}</td>
            @endif
            <td>{{$equipment->equipment_name->name}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
