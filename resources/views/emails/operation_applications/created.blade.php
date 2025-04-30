<h4>Новая заявка от {{$operationApplication->trk->name}}</h4>
<h5>Для {{$operationApplication->division->name}}</h5>
<hr>
<p><b>Проблема:</b> {{$operationApplication->trouble_description}}</p>
<p>Автор заявки: {{$operationApplication->author->name}}</p>
<p>Создана: {{$operationApplication->created_at}}</p>
<p>Тел: {{$operationApplication->author->phone ?? 'не указан'}}</p>
<p>Почта: {{$operationApplication->author->email}}</p>
<br>
<a href="{{route('operation_applications.show',$operationApplication->id)}}">Посмотреть заявку</a>



