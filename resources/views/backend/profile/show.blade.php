@extends('layouts.backend.main')

@section('title', 'Просмотр | Профиль')

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

                <div class="pt-4 mb-lg-3 pb-lg-4 px-4">
                    <div class="row">
                        <div class="col">
                            <div class="card shadow p-3">
                                <ul class="list-group mb-3">
                                    <li class="list-group-item"><b>ФИО: </b>{{$user->name}}</li>
                                    <li class="list-group-item"><b>Почта: </b>{{$user->email}}</li>
                                    <li class="list-group-item"><b>Телефон: </b>{{$user->phone ?? 'не заполнено'}}</li>
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

                                    @if(auth()->user()->hasRole('sadmin'))

                                    <li class="list-group-item"><b>Роль в приложении: </b></li>
                                    <ol class="list-group list-group-numbered list-group-flush mb-3">
                                        @forelse($user->getRoleNames() as $role)
                                            <li class="list-group-item">{{$role}}</li>
                                        @empty
                                            <li class="list-group-item">нет данных ...</li>
                                        @endforelse
                                    </ol>
                                    <li class="list-group-item"><b>Права: </b></li>
                                    <ol class="list-group list-group-numbered list-group-flush mb-3">
                                        @forelse($permissionNames = $user->getPermissionsViaRoles() as $permission)
                                            <li class="list-group-item">{{$permission->name}}</li>
                                        @empty
                                            <li class="list-group-item">нет данных ...</li>
                                        @endforelse
                                    </ol>
                                        <table class="table table-sm table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ТРК</th>
                                                    <th>Система</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($user_trk_systems as $trk_system)
                                                    <tr>
                                                        <td>{{$trk_system->trk->name}}</td>
                                                        <td>{{$trk_system->system->name}}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td>нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                        <table class="table table-sm table-striped">
                                            <thead>
                                            <tr>
                                                <th>Оповещения</th>
                                                <th>Статус</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>Оповещать о выполнении задач от сотрудника</td>
                                                    <td>{{isset($user->notification->task_from_user) && $user->notification->task_from_user ? 'да' : 'нет'}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Оповещать о новых задачах сотруднику</td>
                                                    <td>{{isset($user->notification->task_to_user) && $user->notification->task_to_user ? 'да' : 'нет'}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Оповещать о новых заявках подразделению</td>
                                                    <td>{{isset($user->notification->app_to_user_division) && $user->notification->app_to_user_division ? 'да' : 'нет'}}</td>
                                                </tr>
                                                <tr>
                                                    <td>Оповещать о выполнении заявок от сотрудника</td>
                                                    <td>{{isset($user->notification->app_from_user) && $user->notification->app_from_user ? 'да' : 'нет'}}</td>
                                                </tr>
                                            </tbody>
                                        </table>

                                    @endif

                                    <li class="list-group-item">
                                        <b>Заблокирован: </b>{{$user->is_blocked ? 'да' : 'нет'}}</li>

                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('profile.all')}}"
                                       class="btn btn-outline-success col-4 col-md-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit'))
                                        <a href="{{route('profile.edit', $user)}}"
                                           class="btn btn-outline-warning col-4 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <a href="#" class="btn btn-outline-danger col-4 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/trash3.svg')}}" alt="delete"
                                                title="Удалить"></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- profile init js -->
            <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
