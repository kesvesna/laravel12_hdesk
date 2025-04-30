@extends('layouts.backend.main')

@section('title', 'Главная | Диспетчеризация Европолис')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Данные</h4>
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
                                    <p>Канал: mqttclient1.devices_hwmon_controls_Board_Temperature</p>
                                    <p>Тема: /devices/hwmon/controls/Board Temperature</p>
                                    <p>Получено сообщение: температура процессора wiren board  {{$message}} град.</p>
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
