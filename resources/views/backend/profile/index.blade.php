@extends('layouts.backend.main')

@section('title', 'Главная | Профиль')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Профиль</h4>
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

                <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 px-2">
                    <div class="row">
                        <div class="col-auto">
                            <div class="avatar-lg">
                                <img src="{{asset('assets/images/backend/svg/default-avatar.svg')}}" alt="user-img"
                                     class="img-thumbnail rounded-circle"/>
                            </div>
                        </div>
                        <!--end col-->
                        <div class="col">
                            <div class="p-2">
                                <h3 class="text-white mb-1">{{$user->name}}</h3>
                            </div>
                            <div class="p-2">
                                <a href="{{route('profile.edit', Auth::id())}}" class="btn btn-success"><i
                                        class="ri-edit-box-line align-bottom"></i> Редактировать профиль</a>
                            </div>
                        </div>
                    </div>
                    <!--end row-->
                </div>
                <div class="card mx-3 shadow-sm">
                    <div class="card-body">
                        <div class="card shadow-sm">
                            <div class="card-header">
                                <div class="row">
                                    <div class="col">
                                        <b>Информация</b>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><b>Город:</b> {{$user->town->name ?? 'не заполнено'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Организация:</b> {{$user->organization->name ?? 'не заполнено'}}</li>
                                    <li class="list-group-item"><b>Телефон:</b> {{$user->phone ?? 'не заполнено'}}</li>
                                    <li class="list-group-item">
                                        <b>Подразделение:</b> {{$user->division->name ?? 'не заполнено'}}</li>
                                    <li class="list-group-item">
                                        <b>Должность:</b> {{$user->function->name ?? 'не заполнено'}}</li>
                                    <li class="list-group-item">
                                        <b>Руководитель:</b> {{$user->superior->name ?? 'отсутствует'}}</li>
                                    <li class="list-group-item"><b>Email:</b> {{$user->email}}</li>
                                    <li class="list-group-item"><b>Права: </b></li>
                                    <ol class="list-group list-group-numbered list-group-flush mb-3">
                                        @forelse($permissionNames = $user->getPermissionsViaRoles() as $permission)
                                            <li class="list-group-item">{{$permission->name}}</li>
                                        @empty
                                            <li class="list-group-item">нет данных ...</li>
                                        @endforelse
                                    </ol>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!--end card-body-->
                </div><!-- end card -->
            </div>
            <!--end col-->
        </div>
        <!--end row-->
        <!--end tab-content-->

        <!--end row-->

        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
