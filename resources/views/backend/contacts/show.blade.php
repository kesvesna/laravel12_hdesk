@extends('layouts.backend.main')

@section('title', 'Просмотр | Контакт')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Контакт</h4>
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
                                <ul class="list-group mb-3">
{{--                                    <li class="list-group-item"><b>Роль на--}}
{{--                                            ТРК: </b>{{isset($user->trk_role) ? $user->trk_role->name : 'не выбрано'}}--}}
{{--                                    </li>--}}
                                    <li class="list-group-item"><b>ФИО: </b>{{$user->name}}</li>
                                    <li class="list-group-item"><a class="nav-link" href="mailto:{{$user->email}}"
                                                                   title="Написать"><b>Почта: </b>{{$user->email}}<img
                                                class="ms-2" height="20"
                                                src="{{asset('assets/images/backend/svg/mail-send-line.svg')}}"
                                                alt="email"></a></li>
                                    <li class="list-group-item">@if(isset($user->phone))
                                            <a class="nav-link" href="tel:{{$user->phone}}" title="Позвонить"><b>Телефон: </b>{{$user->phone ?? 'не заполнено'}}
                                                <img class="ms-2 d-inline-block" height="20"
                                                     src="{{asset('assets/images/backend/svg/phone-line.svg')}}"
                                                     alt="phone"></a>
                                        @else
                                            <b>Телефон: </b>{{'не заполнено'}}
                                        @endif</li>
                                    <li class="list-group-item">
                                        <b>Город: </b>{{isset($user->town) ? $user->town->name : 'не заполнено'}}</li>
                                    <li class="list-group-item">
                                        <b>Организация: </b>{{$user->organization->name ?? 'не заполнено'}}</li>
                                    <li class="list-group-item">
                                        <b>Должность: </b>{{$user->function->name ?? 'не заполнено'}}</li>
                                    <li class="list-group-item">
                                        <b>Подразделение: </b>{{$user->division->name ?? 'не заполнено'}}</li>
                                    <li class="list-group-item">
                                        <b>Руководитель: </b>{{$user->superior->name ?? 'не заполнено'}}</li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('contacts.index')}}"
                                       class="btn btn-outline-success col-4 col-md-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- profile init js -->
            <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
