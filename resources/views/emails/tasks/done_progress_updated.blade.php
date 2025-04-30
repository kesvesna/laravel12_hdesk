<h4>Задача, которую Вы поставили {{$task->responsible->name}} выполняется</h4>
<hr>
<p><b>Задача:</b> {{$task->description}}</p>
<p><b>Создана:</b> {{$task->created_at}}</p>
<p><b>Выполнить до:</b> {{$task->deadline_at}}</p>
<p><b>Приоритет:</b> {{$task->priority->name}}</p>
<hr>
<p><b>Процент выполнения:</b> {{$task->done_progress . '%'}}
<p><b>Что сделано:</b> {{$task->executed_result}}
<p><b>Дата выполнения:</b> {{$task->updated_at}}




