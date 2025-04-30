@extends('layouts.backend.main')

@section('title', 'Главная | Выполнение задачи')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Выполнение задачи</h4>
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
                            <form action="{{route('tasks.update_done_progress', $task)}}" method="post">
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
                                        <div class="col mb-5">
                                            <label for="executed_result" class="form-label form-label-sm">Что сделано
                                                <span class="text-danger"><b>*</b></span></label>
                                            <textarea required
                                                      {{$task->done_progress > 99 ? 'readonly' : 'autofocus'}} name="executed_result"
                                                      class="form-control form-control-sm"
                                                      placeholder="Что было сделано для решения задачи">{{$task->done_progress > 99 ? $task->executed_result : null}}</textarea>
                                            @error('executed_result')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-4">
                                            <label for="done_progress" class="form-label form-label-sm">Процент
                                                исполнения: <span
                                                    id="percents_done_progress">{{$task->done_progress . '%'}}</span></label>
                                            <input value="{{old('done_progress', $task->done_progress)}}" type="range"
                                                   class="form-range" min="0" max="100" step="10" id="done_progress"
                                                   name="done_progress">
                                            @error('done_progress')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @if($task->done_progress < 100)
                                        <div class="executors-add-parent-div mb-3">
                                            <label class="form-label form-label-sm">Исполнители <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <div class="executor-add-div">
                                                <div class="row row-cols-1">
                                                    <div class="col-12 col-md-6">
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
                                                                    <option data-equipment_key=""
                                                                            value="нет данных ...">
                                                                @endforelse
                                                            </datalist>
                                                        </div>
                                                        @error('executors.*')
                                                        <div class="text-danger"
                                                             style="margin-top: -1rem !important;">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    @else
                                        <label class="form-label form-label-sm">Исполнители:</label>
                                        <ul>
                                            @foreach($task->executors as $executor)
                                                <li>{{$executor->name}}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-4 mt-3">
                                            <label for="executed_at" class="form-label form-label-sm">Дата и время
                                                исполнения <span class="text-danger"><b>*</b></span></label>
                                            <input required class="form-control form-control-sm" type="datetime-local"
                                                   id="executed_at"
                                                   name="executed_at" value="{{date('Y-m-d H:i')}}"
                                                   min="2019-01-07T00:00" max="2050-12-14T00:00">
                                            @error('executed_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-4">
                                            <div class="btn-group-vertical btn-group-sm col-12" role="group" aria-label="Vertical radio toggle button group">
                                                <input type="radio" class="btn-check" name="operation_type" value="just_save" id="vbtn-radio1" autocomplete="off" checked>
                                                <label class="btn btn-outline-success" for="vbtn-radio1">Просто сохранить исполнение</label>
                                                <input type="radio" class="btn-check" name="operation_type" value="save_and_create_spare_part_order" id="vbtn-radio2" autocomplete="off">
                                                <label class="btn btn-outline-success" for="vbtn-radio2">Заказать запчасти</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-2">
                                        <div class="col-6 col-md-3">
                                        <a href="{{route('tasks.show', $task)}}"
                                           class="btn btn-sm btn-outline-success col-12"><img
                                                src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                alt="back" title="Назад"></a>
                                        </div>
                                            <div class="col-6 col-md-3">
                                        <button type="submit"
                                                class="{{$task->done_progress > 99 ? 'd-none' : null}} btn btn-sm btn-outline-danger col-12"
                                                >
                                            <img src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                 title="Сохранить"></button>
                                    </div>
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
