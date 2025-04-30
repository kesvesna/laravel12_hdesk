
<table>
    <thead>
    <tr>
        <th colspan="4"></th>
    </tr>
    <tr>
        <th colspan="4"><b>{{'Заявки с ' . $data['start_date'] . ' по ' . $data['finish_date'] . ', ' . $status}}</b></th>
    </tr>
    <tr>
        <th colspan="4"><b>{{$trk->name . ', ' . $division->name}}</b></th>
    </tr>
    <tr><th colspan="4"></th></tr>
    <tr>
        <th>Дата</th>
        <th>Проблема</th>
        @if('Новые' != $status)
            <th>Что сделано</th>
            @if('Выполняются' === $status)
                <th>%</th>
            @endif
            @if('Выполнены' === $status)
                <th>Когда</th>
            @endif
        @endif
    </tr>
    </thead>
    <tbody>
    @foreach($applications as $application)
        <tr>
            <td align="center">{{$application->created_at}}</td>
            <td align="center">{{$application->trouble_description}}</td>
            @if($application->done_percents > 0)
                <td>{{$application->result_description}}</td>
                @if($application->done_percents < 100)
                    <td>{{$application->done_percents}}</td>
                @endif
                @if($application->done_percents === 100)
                    <td>{{$application->updated_at}}</td>
                @endif
            @endif
        </tr>
    @endforeach
    </tbody>
</table>
