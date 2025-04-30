@extends('layouts.backend.main')

@section('title', 'Главная | Задачи')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Задачи</h4>
                        @if(auth()->user()->can('task create'))
                            <a href="{{route('tasks.create')}}"><img
                                    src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                    title="Добавить" height="30"></a>
                        @endif
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
                    <div class="col">
                        @include('components.backend.message')
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="card shadow">
                                <div class="card-body">
                                    <table class="table table-striped table-hover shadow">
                                        <thead>
                                        <tr>
                                            <th class="d-none d-md-table-cell">Выполнить до</th>
                                            <th class="d-none d-md-table-cell">Приоритет</th>
                                            <th>Задача</th>
                                            <th>Ответственный</th>
                                            <th class="d-none d-md-table-cell">Выполнено, %</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('tasks.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td class="d-none d-md-table-cell">
                                                    <input class="form-control form-control-sm" type="search"
                                                           id="deadline_at" placeholder="2023-08, -08-26"
                                                           onchange="this.form.submit();" name="deadline_at"
                                                           value="{{$old_filters['deadline_at'] ?? null}}">
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <select class="form-select form-select-sm" name="priority_id"
                                                            id="priority_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_priorities as $priority)
                                                            <option
                                                                value="{{$priority->id}}" {{isset($old_filters['priority_id']) && $old_filters['priority_id'] === $priority->id ? 'selected' : null}}>{{$priority->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" type="search"
                                                           id="description" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="description"
                                                           value="{{$old_filters['description'] ?? null}}">
                                                </td>
                                                <td>
                                                    <select name="responsible_id" class="form-select form-select-sm"
                                                            id="responsible_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_responsibles as $responsible)
                                                            <option
                                                                value="{{$responsible->id}}" {{isset($old_filters['responsible_id']) && $old_filters['responsible_id'] == $responsible->id ? 'selected' : null}}>{{$responsible->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <select name="task_status" class="form-select form-select-sm"
                                                            id="task_status" onchange="this.form.submit();">
                                                        <option value="" {{isset($old_filters['task_status']) && $old_filters['task_status'] == '' ? 'selected' : null}}>Все</option>
                                                        <option value="new" {{isset($old_filters['task_status']) && $old_filters['task_status'] == 'new' ? 'selected' : null}}>Новые</option>
                                                        <option value="in_progress" {{isset($old_filters['task_status']) && $old_filters['task_status'] == 'in_progress' ? 'selected' : null}}>Выполняются</option>
                                                        <option value="closed" {{isset($old_filters['task_status']) && $old_filters['task_status'] == 'closed' ? 'selected' : null}}>Выполнены</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </form>
                                        @forelse($tasks as $task)
                                            @if(
                                                $task->author->id == Auth::id()
                                                || $task->responsible->id == Auth::id()
                                                || auth()->user()->hasRole('sadmin')
                                                )
                                                <tr onclick="window.location='{{ route('tasks.show', $task->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="d-none d-md-table-cell"
                                                        style="background-color:
                                                     {{(int)$task->done_progress < 100 && Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $task->deadline_at)->lt(date('Y-m-d H:i:s')) ? '#ffd5d5' : null }}
                                                    ;">
                                                        {{$task->deadline_at}}
                                                    </td>
                                                    <td class="d-none d-md-table-cell">{{$task->priority->name}}</td>
                                                    <td>{{$task->description}}</td>
                                                    <td>{{$task->responsible->name}}</td>
                                                    <td class="d-none d-md-table-cell">{{$task->done_progress}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$tasks->withQueryString()->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
