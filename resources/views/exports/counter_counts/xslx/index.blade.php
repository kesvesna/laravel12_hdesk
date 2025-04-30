<table>
    <thead>
    <tr>
        <th><b>Дата</b></th>
        <th><b>ТРК</b></th>
        <th><b>Этаж</b></th>
        <th><b>Бренд</b></th>
        <th><b>Юр. лицо</b></th>
        <th><b>Номер счетчика</b></th>
        <th><b>Тип счетчика</b></th>
        <th><b>Тариф</b></th>
        <th><b>Предыдущие</b></th>
        <th><b>Текущие</b></th>
        <th><b>Коэффициент</b></th>
        <th><b>Итого</b></th>
        <th><b>Комментарии</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($counter_counts as $counter_count)
        <tr>
            <td>{{$counter_count->date}}</td>
            <td>{{$counter_count->trk_room_counter->trk->name}}</td>
            <td>{{$counter_count->trk_room_counter->floor->name}}</td>
            <td>{{$counter_count->trk_room_counter->brand->name ?? 'отсутствует'}}</td>
            <td>{{$counter_count->trk_room_counter->organization->name}}</td>
            <td>{{$counter_count->trk_room_counter->number}}</td>
            <td>{{$counter_count->trk_room_counter->counter_type->name}}</td>
            <td>{{$counter_count->tariff ? 'день' : 'ночь'}}</td>
            <td>{{$counter_count->prev_count}}</td>
            <td>{{$counter_count->count}}</td>
            <td>{{$counter_count->trk_room_counter->coefficient}}</td>
            <td>{{$counter_count->result}}</td>
            <td>{{$counter_count->comment}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
