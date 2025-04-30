@extends('layouts.backend.main')

@section('title', 'Главная | Создание подзадачи')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Создание подзадачи</h4>
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
                            <form action="{{route('tasks.store_subtask', $task)}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="description">Задача <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <textarea required name="description" class="form-control form-control-sm"
                                                      placeholder="Что нужно сделать">{{old('description')}}</textarea>
                                            @error('description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="priority_id">Приоритет <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select class="form-select form-select-sm" name="priority_id"
                                                    id="priority_id">
                                                @forelse($priorities as $priority)
                                                    <option
                                                        value="{{$priority->id}}" {{isset($old_filters['priority_id']) && $old_filters['priority_id'] === $priority->id ? 'selected' : null}}>{{$priority->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('priority_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="responsible_id">Кому задача
                                                <span class="text-danger"><b>*</b></span></label>
                                            <select class="form-select form-select-sm" name="responsible_id"
                                                    id="responsible_id">
                                                @forelse($responsibles as $responsible)
                                                    <option
                                                        value="{{$responsible->id}}" {{old('responsible_id', Auth::id()) ? 'selected' : null}}>{{$responsible->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('responsible_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="deadline_at">Выполнить до <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required type="datetime-local" name="deadline_at"
                                                   class="form-control form-control-sm">
                                            @error('deadline_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('tasks.index')}}"
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
@endsection
