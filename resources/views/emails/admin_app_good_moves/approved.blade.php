<h4>Ваша заявка на ввоз/вывоз согласована</h4>
<hr>
<ul class="list-group mb-4">
    <li class="list-group-item">
        <b>Заявка на:</b> {{$admin_app_good_move->operation_type == 'import' ? 'ввоз' : 'вывоз'}}
    </li>
    <li class="list-group-item"><b>Начало:</b> {{$admin_app_good_move->start_at}}</li>
    <li class="list-group-item"><b>Конец:</b> {{$admin_app_good_move->finish_at}}</li>
    <li class="list-group-item">
        <b>Статус:</b> {{$admin_app_good_move->admin_app_status->name}}
    </li>
    @if($admin_app_good_move->comment)
        <li class="list-group-item"><b>Комментарий:</b> {{$admin_app_good_move->comment}}</li>
    @endif
    <li class="list-group-item"><b>ТРК:</b> {{$admin_app_good_move->trk_room->trk->name}}</li>
    <li class="list-group-item"><b>Помещение: </b><a href="{{route('trk_room.show', $admin_app_good_move->trk_room->id)}}">{{$admin_app_good_move->trk_room->room->name}}</a></li>
    <li class="list-group-item"><b>Арендатор:</b> {{$admin_app_good_move->organization->name}}</li>
    <li class="list-group-item"><b>Торговая марка:</b> {{$admin_app_good_move->brand->name}}</li>
    <li class="list-group-item"><b>Загрузочная зона:</b> {{$admin_app_good_move->gate_number}}</li>
    <li class="list-group-item"><b>Материально ответственный:</b> {{$admin_app_good_move->responsible_user}}</li>
</ul>
<hr>
<p>Согласована: {{$admin_app_good_move->updated_at}}</p>
<p>Телефон админа: {{$admin_app_good_move->last_editor->phone ?? 'не указан'}}</p>
<p>Почта админа: {{$admin_app_good_move->last_editor->email}}</p>
<br>
<a href="{{route('admin_app_good_moves.show',$admin_app_good_move->id)}}">Посмотреть заявку</a>



