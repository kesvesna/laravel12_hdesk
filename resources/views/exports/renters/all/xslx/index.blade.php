<table>
    <thead>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <td colspan="5"><b>{{'Арендаторы ' . $trk->name . ', ' . date('d-m-Y')}}</b></td>
    </tr>
    <tr>
        <td colspan="5"><b>{{'Этаж: '}}{{$floor->name ?? 'Все'}}</b></td>
    </tr>
    <tr>
        <td colspan="5"></td>
    </tr>
    <tr>
        <th><b>Блок</b></th>
        <th><b>Этаж</b></th>
        <th><b>Помещение</b></th>
        <th><b>Бренд</b></th>
        <th><b>Юр.лицо</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($renters as $renter)
        <tr>
            <td>{{$renter->trk_room->building->name}}</td>
            <td>{{$renter->trk_room->floor->name}}</td>
            <td>{{$renter->trk_room->room->name}}</td>
            <td>{{$renter->brand->name ?? 'отсутствует'}}</td>
            <td>{{$renter->organization->name ?? 'отсутствует'}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
