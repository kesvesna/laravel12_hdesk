@extends('layouts.backend.main')

@section('title', 'Главная | Название работы редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Название работы редактирование</h4>
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

                <div class="p-1 p-md-2">
                            <form action="{{route('work_names.update', $work_name)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow">
                                    <div class="row">
                                        <div class="col px-3">
                                            <ul class="list-group mb-3">
                                                <li class="list-group-item mb-3">
                                                    <label for="name" class="form-label form-label-sm">Название<span
                                                            class="text-danger"><b> *</b></span></label>
                                                    <input type="text" placeholder="Название работы" name="name" required class="form-control form-control-sm" value="{{$work_name->name}}">
                                                </li>
                                                <li class="list-group-item mb-3">
                                                    <label for="visibility" class="form-label form-label-sm">Видимость в списках<span
                                                            class="text-danger"><b> *</b></span></label>
                                                    <select required class="form-select form-select-sm" name="visibility">
                                                        <option value="1" {{$work_name->visibility == 1 ? 'selected' : null}}>Показывать</option>
                                                        <option value="2" {{$work_name->visibility == 2 ? 'selected' : null}}>Скрывать</option>
                                                    </select>
                                                </li>
                                                <li class="list-group-item"><b>Создано: </b>{{$work_name->created_at . ', ' . $work_name->author->name}}
                                                </li>
                                                <li class="list-group-item"><b>Изменено: </b>{{$work_name->updated_at . ', ' . $work_name->last_editor->name}}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 px-3 input-group-sm">
                                        <a href="javascript:history.back()"
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
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
