<h4>Ваша заявка перенаправлена в {{$operationApplication->division->name}}</h4>
<p><b>Перенаправил:</b> {{$operationApplication->last_editor->name}}</p>
<p><b>ТРК:</b> {{$operationApplication->trk->name}}</p>
<p><b>Проблема:</b> {{$operationApplication->trouble_description}}</p>
<hr>
<p><b>Что сделано:</b> {{$operationApplication->result_description}}</p>
<p><b>Процент выполнения:</b> {{$operationApplication->done_percents . '%'}}</p>
<p><b>Дата:</b> {{$operationApplication->updated_at}}</p>
<br>
<a href="{{route('operation_applications.show',$operationApplication->id)}}">Посмотреть заявку</a>



