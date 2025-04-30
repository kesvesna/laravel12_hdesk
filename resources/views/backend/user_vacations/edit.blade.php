@extends('layouts.backend.main')

@section('title', 'Главная | Отпуск редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Отпуск редактирование</h4>
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
                            <form action="{{route('user_vacations.update', $user_vacation)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-2">
                                            <label for="user_id"
                                                   class="form-label form-label-sm">{{$user_vacation->user->name}}</label>
                                            <input hidden readonly class="form-control form-control-sm" name="user_id"
                                                   value="{{$user_vacation->user_id}}">
                                            @error('user_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="start" class="form-label form-label-sm">Начало отпуска <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input autofocus required type="date" class="form-control form-control-sm"
                                                   name="start" value="{{$user_vacation->start}}">
                                            @error('start')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="finish" class="form-label form-label-sm">Конец отпуска <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required type="date" class="form-control form-control-sm"
                                                   name="finish" value="{{$user_vacation->finish}}">
                                            @error('finish')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label for="result" class="form-label form-label-sm">Дней</label>
                                            <input readonly disabled class="form-control form-control-sm" name="result"
                                                   value="{{$user_vacation->result}}">
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('user_vacations.show',  $user_vacation)}}"
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
