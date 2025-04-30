@extends('layouts.backend.main')

@section('title', 'Главная | Статусы заявок в администрацию редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Статусы заявок в администрацию редактирование</h4>
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
                            <form action="{{route('admin_app_statuses.update', $admin_app_status)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                        <label class="form-label form-label-sm"><b>Название:
                                                <span class="text-danger"> *</span></b></label>
                                        <input required value="{{$admin_app_status->name}}" name="name" type="text"
                                               class="form-control form-control-sm" placeholder="Название"
                                               aria-label="name" aria-describedby="basic-addon1" autofocus>
                                    @error('name')
                                    <div class="text-danger">{{$message}}</div>
                                    @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label class="form-label form-label-sm"><b>Создан:</b></label>
                                        <span>{{$admin_app_status->created_at . ', '}}{{$admin_app_status->author->name}}</span>
                                    </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm"><b>Исправлен:</b></label>
                                            <span>{{$admin_app_status->updated_at . ', '}}{{$admin_app_status->last_editor->name}}</span>
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('admin_app_statuses.show', $admin_app_status)}}"
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
