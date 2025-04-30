@extends('layouts.backend.main')

@section('title', 'Главная | Статус заказа редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Статус заказа редактирование</h4>
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
                            <form action="{{route('order_statuses.update', $order_status)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow">
                                    <div class="input-group mt-3 px-3 mb-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Название:<span
                                                    class="text-danger"> *</span></b></span>
                                        <input value="{{$order_status->name}}" name="name" type="text"
                                               class="form-control form-control-sm" placeholder="Название"
                                               aria-label="name" aria-describedby="basic-addon1" autofocus>
                                    </div>
                                    @error('name')
                                    <div class="text-danger px-3">{{$message}}</div>
                                    @enderror
                                    <div class="input-group mb-3 px-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Создан:</b></span>
                                        <input value="{{$order_status->created_at . ', '}}{{$order_status->author->name}}" name="created_at" type="text"
                                               class="form-control form-control-sm" placeholder="Создан"
                                               aria-label="alias" aria-describedby="basic-addon1" readonly>
                                    </div>
                                    <div class="input-group mb-3 px-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Исправлен:</b></span>
                                        <input value="{{$order_status->updated_at . ', '}}{{$order_status->last_editor->name}}" name="updated_at" type="text"
                                               class="form-control form-control-sm" placeholder="Исправлен"
                                               aria-label="alias" aria-describedby="basic-addon1" readonly>
                                    </div>
                                    <div class="input-group mb-3 px-3 input-group-sm">
                                        <a href="{{route('order_statuses.show', $order_status)}}"
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
