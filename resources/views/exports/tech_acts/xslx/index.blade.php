<table>
    <thead>
    <tr>
        <th colspan="5">&nbsp;</th>
    </tr>
    <tr>
        <th>{{$user_result_time_sheet->user->name}}</th>
        <th colspan="2">{{$user_result_time_sheet->year . '-' . $user_result_time_sheet->month}}</th>
        <th>{{$user_result_time_sheet->result . ' ч'}}</th>
        <th>{{$user_result_time_sheet->overtime . ' ч'}}</th>
    </tr>
    <tr>
        <th colspan="5">&nbsp;</th>
    </tr>
    <tr>
        <th><b>Дата</b></th>
        <th><b>Начало</b></th>
        <th><b>Конец</b></th>
        <th><b>Всего, ч</b></th>
        <th><b>Сверх, ч</b></th>
    </tr>
    </thead>
    <tbody>
    @foreach($user_time_sheets as $user_time_sheet)
        <tr>
            <td>{{$user_time_sheet->date}}</td>
            <td>{{date('H:i', strtotime($user_time_sheet->start))}}</td>
            <td>{{date('H:i', strtotime($user_time_sheet->finish))}}</td>
            <td>{{$user_time_sheet->result != '00:00:00' ? date('H:i', strtotime($user_time_sheet->result)) : null}}</td>
            <td>{{$user_time_sheet->overtime != '00:00:00' && $user_time_sheet->overtime != null ? date('H:i', strtotime($user_time_sheet->overtime)) : null}}</td>
        </tr>
    @endforeach
    </tbody>
</table>
