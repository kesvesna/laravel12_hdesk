<table>
    <thead>
    <tr>
        <th colspan="6"></th>
    </tr>
    <tr>
        <th colspan="6"><b>{{'Арендаторы ' . $trk->name . ' - ' . now()}}</b></th>
    </tr>
    <tr><th colspan="6"></th></tr>
    <tr>
        <th>Блок</th>
        <th>Этаж</th>
        <th>Помещение</th>
        <th>Бренд</th>
        <th>Юр.лицо</th>
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
