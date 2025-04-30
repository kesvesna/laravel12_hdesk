<h4>Новая задача от {{$task->author->name}}</h4>
<hr>
<p><b>Задача:</b> {{$task->description}}</p>
<p><b>Выполнить до:</b> {{$task->deadline_at}}</p>
<p><b>Приоритет:</b> {{$task->priority->name}}</p>
<hr>
<p>Создана: {{$task->created_at}}</p>
<p>Телефон автора: {{$task->author->phone ?? 'не указан'}}</p>
<p>Почта автора: {{$task->author->email}}</p>
<br>
<a href="{{route('tasks.show',$task->id)}}">Посмотреть задачу</a>



