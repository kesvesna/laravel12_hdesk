@extends('layouts.backend.main')

@section('title', 'Просмотр | Задача')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Задача</h4>
                    </div>
                </div>
            </div>
            <div class="profile-foreground position-relative"
                 style="
                        margin-top: -1.5rem !important;
                        margin-right: -1.5rem !important;
                        margin-left: -1.5rem !important;
                     ">
                <div class="profile-wid-bg">
                    {{--                        <img src="{{asset('assets/images/profile-bg.jpg')}}" alt="" class="profile-wid-img" />--}}
                </div>

                <div class="pt-4 mb-lg-3 pb-lg-4 px-4">
                    <div class="row">
                        <div class="col">
                            <div class="card shadow p-3">
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <ul class="list-group mb-3">
                                    @if(isset($task->parent_id))
                                        <li class="list-group-item"><a href="{{route('tasks.show', $task->parent_id)}}">Задача
                                                родитель</a></li>
                                    @endif
                                    <li class="list-group-item"><b>Задача: </b>{{$task->description ?? 'не выбрано'}}</li>
                                        <li class="list-group-item"><b>Выполнить
                                                до: </b>{{$task->deadline_at ?? 'не выбрано'}}</li>
                                        <li class="list-group-item">
                                            <b>Приоритет: </b>{{$task->priority->name ?? 'не выбрано'}}</li>
                                        <li class="list-group-item"><b>Выполнено: </b>{{$task->done_progress . '%'}}</li>
                                        <li class="list-group-item">
                                        <b>Сделано: </b>{{$task->executed_result ?? 'пока ничего'}}</li>
                                    <li class="list-group-item">
                                        <b>Ответственный: </b>{{$task->responsible->name ?? 'не выбрано'}}</li>
                                    @if(count($task->executors) > 0)
                                        <li class="list-group-item"><b>Исполнители: </b>
                                            <ul class="list-group mt-1">
                                                @forelse($task->executors as $executor)
                                                    <li class="list-group-item">{{$executor->name}}</li>
                                                @empty
                                                    <li class="list-group-item">нет данных ...</li>
                                                @endforelse
                                            </ul>
                                        </li>
                                        <li class="list-group-item"><b>Дата
                                                выполнения: </b>{{$task->executed_at ?? 'отсутствует'}}</li>
                                    @endif
                                    @if(count($subtasks) > 0)
                                        <li class="list-group-item"><span> </span></li>
                                        <li class="list-group-item"><b>Подзадачи: </b></li>
                                        <ul class="list-group">
                                            @forelse($subtasks as $subtask)
                                                <li class="list-group-item"><a
                                                        href="{{route('tasks.show', $subtask->id)}}">{{$subtask->description}}</a>
                                                </li>
                                            @empty
                                                <li class="list-group-item">Подзадач нет</li>
                                            @endforelse
                                        </ul>
                                    @endif
                                </ul>
                                @if(count($task->orders) > 0)
                                <ul class="list-group mb-3">
                                    <li class="list-group-item">
                                        <b>Заказы запчастей</b></li>
                                    @forelse($task->orders as $order)
                                        <li class="list-group-item">
                                            <a href="{{route('orders.show', $order->id)}}">
                                            {{$order->created_at}}
                                        </a></li>
                                    @empty
                                        <li class="list-group-item">
                                            нет данных ...</li>
                                    @endforelse
                                </ul>
                                @endif
                                <ul class="list-group mb-3">
                                    <li class="list-group-item">
                                        <b>Создана: </b>{{$task->created_at . ', '}}{{$task->author->name}}</li>
                                    <li class="list-group-item">
                                        <b>Исправлена: </b>{{$task->updated_at . ', '}}{{$task->last_editor->name}}</li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript: history.back(); false;"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if($task->done_progress < 100)
                                        @if($task->responsible->id == Auth::id() || auth()->user()->hasRole('sadmin'))
                                        <a href="{{route('tasks.done_progress', $task)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/check2-all.svg')}}"
                                                alt="save_done_progress" title="Выполнение задачи"></a>
                                       @endif
                                        <a href="{{route('tasks.create_subtask', $task)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/arrows-expand.svg')}}"
                                                alt="create_subtask" title="Создать подзадачу"></a>
                                    @endif
                                    @if($task->author->id == Auth::id() || auth()->user()->hasRole('sadmin'))
                                        <a href="{{route('tasks.edit', $task)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->hasRole('sadmin'))
                                        <form action="{{route('tasks.destroy', $task)}}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-outline-danger"><img
                                                    src="{{asset('assets/images/backend/svg/trash3.svg')}}" alt="delete"
                                                    title="Удалить"></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- profile init js -->
            <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
