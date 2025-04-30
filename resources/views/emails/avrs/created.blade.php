<h4>Создано новый АВР</h4>
<hr>
<p><b>ТРК:</b> {{$avr->trk_room->trk->name}}</p>
<p><b>Блок/Зона:</b> {{$avr->trk_room->building->name}}</p>
<p><b>Этаж:</b> {{$avr->trk_room->floor->name}}</p>
<p><b>Помещение:</b> {{$avr->trk_room->room->name}}</p>
<p><b>Тип оборудования:</b> {{$avr->system->name}}</p>
<p><b>Оборудование:</b>
@foreach($avr->avr_equipments as $avr_equipment)
     {{$avr_equipment->trk_equipment->equipment_name->name . ', '}}
@endforeach
</p>
<hr>
<p>Автор: {{$avr->author->name}}</p>
<p>Создано: {{$avr->created_at}}</p>
<p>Тел: {{$avr->author->phone ?? 'не указан'}}</p>
<p>Почта: {{$avr->author->email}}</p>
<hr>
<a href="{{route('avrs.show',$avr->id)}}">Посмотреть акт</a>



