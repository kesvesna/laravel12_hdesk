@extends('layouts.backend.main')

@section('title', 'Главная | Редактирование задачи')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Редактирование задачи</h4>
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
                            <form action="{{route('tasks.update', $task)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="description" class="form-label form-label-sm">Задача <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <textarea required readonly name="description"
                                                      class="form-control form-control-sm"
                                                      placeholder="Где проблема и в чем она">{{old('description', $task->description)}}</textarea>
                                            @error('description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="deadline_at" class="form-label form-label-sm">Выполнить до <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input class="form-control form-control-sm" type="datetime-local"
                                                   id="deadline_at"
                                                   name="deadline_at" value="{{old('deadline_at', $task->deadline_at)}}"
                                                   min="2019-01-07T00:00" max="2050-12-14T00:00">
                                            @error('deadline_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="executed_result" class="form-label form-label-sm">Что сделано
                                                <span class="text-danger"><b>*</b></span></label>
                                            <textarea autofocus name="executed_result"
                                                      class="form-control form-control-sm"
                                                      placeholder="Что было сделано для решения проблемы">{{old('executed_result', $task->executed_result)}}</textarea>
                                            @error('executed_result')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="done_progress" class="form-label form-label-sm">Процент
                                                выполнения: <span
                                                    id="percents_done_progress">{{$task->done_progress . '%'}}</span></label>
                                            <input value="{{old('done_progress', $task->done_progress)}}" type="range"
                                                   class="form-range" min="0" max="100" step="10" id="done_progress"
                                                   name="done_progress">
                                            @error('done_progress')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="executors-add-parent-div">
                                        <label class="form-label form-label-sm">Исполнители <span
                                                class="text-danger"><b>*</b></span></label>
                                        <div class="executor-add-div">
                                            <div class="row row-cols-1">
                                                <div class="col-12 col-md-4">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <span class="input-group-text executor-add-button"><img
                                                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                alt="add" title="Добавить" height="20"></span>
                                                        <span class="input-group-text executor-delete-button"><img
                                                                src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                alt="delete" title="Удалить" height="20"></span>
                                                        <input value="{{Auth::user()->name}}" required type="text"
                                                               list="executors_list"
                                                               class="form-control form-control-sm"
                                                               placeholder="Начните писать ..."
                                                               name="executors[]">
                                                        <datalist id="executors_list">
                                                            @forelse($executors as $executor)
                                                                <option data-equipment_key="{{$executor->id}}"
                                                                        value="{{$executor->name}}">
                                                            @empty
                                                                <option data-equipment_key="" value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </div>
                                                    @error('equipment_names.*')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3 mt-3">
                                            <label for="executed_at" class="form-label form-label-sm">Дата и время
                                                выполнения <span class="text-danger"><b>*</b></span></label>
                                            <input class="form-control form-control-sm" type="datetime-local"
                                                   id="executed_at"
                                                   name="executed_at" value="{{old('executed_at', $task->executed_at)}}"
                                                   min="2019-01-07T00:00" max="2050-12-14T00:00">
                                            @error('executed_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 input-group-sm">
                                        <a href="{{route('tasks.show', $task)}}"
                                           class="btn btn-sm btn-outline-success col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                alt="back" title="Назад"></a>
                                        <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                title="Сохранить"></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
        <script src="{{asset('assets/js/tasks/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/tasks/delete_executor.js')}}"></script>
        <script src="{{asset('assets/js/tasks/show_percents_for_progress_bar.js')}}"></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
@endsection
