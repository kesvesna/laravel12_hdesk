<table>
    <thead>
    <tr>
        <th></th>
    </tr>
    <tr>
        <th>{{$counter->trk->name}}</th>
        <th>{{$counter->floor->name}}</th>
        <th>{{$counter->brand->name ?? 'бренд отсутствует'}}</th>
        <th>{{$counter->organization->name}}</th>
    </tr>
    <tr>
        <th></th>
    </tr>
    <tr>
        <th>{{'№ ' . $counter->number}}</th>
        <th>{{$counter->counter_type->name}}</th>
    </tr>
    <tr>
        <th></th>
    </tr>
    <tr>
        <th><b>Дата</b></th>
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
