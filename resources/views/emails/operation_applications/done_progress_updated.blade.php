<h4>Ваша заявка для {{$operationApplication->division->name}} {{$operationApplication->done_percents < 100 ? ' выполняется' : ' выполнена'}}</h4>
<p><b>ТРК:</b> {{$operationApplication->trk->name}}</p>
<p><b>Проблема:</b> {{$operationApplication->trouble_description}}</p>
<hr>
<p><b>Что сделано:</b> {{$operationApplication->result_description}}</p>
<p><b>Процент выполнения:</b> {{$operationApplication->done_percents . '%'}}</p>
<p><b>Дата:</b> {{$operationApplication->updated_at}}</p>
<br>
<a href="{{route('operation_applications.show',$operationApplication->id)}}">Посмотреть заявку</a>



