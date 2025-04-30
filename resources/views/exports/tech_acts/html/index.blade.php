<table>
    <thead>
    <tr>
        <th><b>Дата</b></th>
        <th><b>ТРК</b></th>
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
            <td>{{$counter_count->created_at}}</td>
            <td>{{$counter_count->trk_room->trk->name}}</td>
            <td>{{$counter_count->trk_room->renter->brand->name}}</td>
            <td>{{$counter_count->trk_room->renter->organization->name}}</td>
            <td>{{$counter_count->counter->number}}</td>
            <td>{{$counter_count->counter->type->name}}</td>
            <td>{{$counter_count->tariff ? 'день' : 'ночь'}}</td>
            <td>{{$counter_count->current_count}}</td>
            <td>{{$counter_count->current_count}}</td>
            <td>{{$counter_count->counter->coefficient}}</td>
            <td>0</td>
            <td>{{$counter_count->comment}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
