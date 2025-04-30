<h4>Создано новое оборудование</h4>
<hr>
<p><b>ТРК:</b> {{$trk_equipment->trk_room->trk->name}}</p>
<p><b>Блок/Зона:</b> {{$trk_equipment->trk_room->building->name}}</p>
<p><b>Этаж:</b> {{$trk_equipment->trk_room->floor->name}}</p>
<p><b>Помещение:</b> {{$trk_equipment->trk_room->room->name}}</p>
<p><b>Оборудование:</b> {{$trk_equipment->equipment_name->name}}</p>
<hr>
<p>Автор: {{$trk_equipment->author->name}}</p>
<p>Создано: {{$trk_equipment->created_at}}</p>
<p>Тел: {{$trk_equipment->author->phone ?? 'не указан'}}</p>
<p>Почта: {{$trk_equipment->author->email}}</p>
<hr>
<a href="{{route('trk_equipments.show',$trk_equipment->id)}}">Посмотреть оборудование</a>



