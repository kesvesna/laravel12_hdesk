@extends('layouts.backend.main')

@section('title', 'Редактирование | Настройки')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Редактирование настроек</h4>
                    </div>
                </div>
            </div>
            <div class="position-relative" style="
                        margin-top: -1.5rem !important;
                        margin-right: -1.5rem !important;
                        margin-left: -1.5rem !important;
                     ">
                <div class="profile-wid-bg profile-setting-img">
                    <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 px-4">
                        <div class="row g-4">
                            <!--end col-->
                            <div class="col">
                                <div class="pt-5">
                                    <a href="{{route('settings.index')}}" class="btn btn-success">Назад в настройки</a>
                                </div>
                            </div>
                        </div>
                        <!--end row-->
                    </div>
                    {{--                <img src="{{asset('assets/images/profile-bg.jpg')}}" class="profile-wid-img" alt="">--}}
                    {{--                <div class="overlay-content">--}}
                    {{--                    <div class="text-end p-3">--}}
                    {{--                        <div class="p-0 ms-auto rounded-circle profile-photo-edit">--}}
                    {{--                            <input id="profile-foreground-img-file-input" type="file" class="profile-foreground-img-file-input">--}}
                    {{--                            <label for="profile-foreground-img-file-input" class="profile-photo-edit btn btn-light">--}}
                    {{--                                <i class="ri-image-edit-line align-bottom me-1"></i> Change Cover--}}
                    {{--                            </label>--}}
                    {{--                        </div>--}}
                    {{--                    </div>--}}
                    {{--                </div>--}}
                </div>
            </div>
            <div class="row" style="margin-top: -8rem !important;">
                <div class="col-xxl-3 mb-4">
                    <div class="card">
                        <div class="card-body p-4">
                            <div class="text-center">
                                <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                                    <img src="{{asset('assets/images/backend/svg/default-avatar.svg')}}"
                                         class="rounded-circle avatar-xl img-thumbnail user-profile-image"
                                         alt="user-profile-image">
{{--                                    <div class="avatar-xs p-0 rounded-circle profile-photo-edit">--}}
{{--                                        <input id="profile-img-file-input" type="file" class="profile-img-file-input">--}}
{{--                                        <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">--}}
{{--                                                    <span class="avatar-title rounded-circle bg-light text-body">--}}
{{--                                                        <i class="ri-camera-fill"></i>--}}
{{--                                                    </span>--}}
{{--                                        </label>--}}
{{--                                    </div>--}}
                                </div>
                                <h5 class="fs-16 mb-1">{{$user->name}}</h5>
                                <p class="text-muted mb-0">{{$user->organization->name}}</p>
                                <p class="text-muted mb-0">{{$user->division->name}}</p>
                                <p class="text-muted mb-0">{{$user->function->name}}</p>
                            </div>
                        </div>
                    </div>
                    <!--end card-->
                </div>
                <!--end col-->
                <div class="col-xxl-9">
                    @include('components.backend.message')
                    <div class="card">
                        <div class="pt-2 ps-2">
                            <ul class="nav nav-tabs-custom rounded border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link pb-3 active" data-bs-toggle="tab" href="#changePassword"
                                       role="tab">
                                        <i class="far fa-user"></i> Смена пароля
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link pb-3" data-bs-toggle="tab" href="#experience" role="tab">
                                        <i class="far fa-envelope"></i> Трк/Системы
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link pb-3" data-bs-toggle="tab" href="#emails" role="tab">
                                        <i class="far fa-envelope"></i> Оповещения
                                    </a>
                                </li>
{{--                                <li class="nav-item">--}}
{{--                                    <a class="nav-link pb-3" data-bs-toggle="tab" href="#filters" role="tab">--}}
{{--                                        <i class="far fa-envelope"></i> Фильтры--}}
{{--                                    </a>--}}
{{--                                </li>--}}
                            </ul>
                        </div>
                        <div class="card-body p-4">
                            <div class="tab-content">
                                <div class="tab-pane active" id="changePassword" role="tabpanel">
                                    <form action="{{route('settings.update_password', $user)}}" method="post">
                                        @csrf
                                        @method('patch')
                                        <div class="row g-2">
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="oldpasswordInput" class="form-label">Старый пароль<span
                                                            class="text-danger"><b> *</b></span></label>
                                                    <input type="password" class="form-control" id="oldpasswordInput"
                                                           placeholder="Текущий пароль" name="old_password" required>
                                                    @error('old_password')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="newpasswordInput" class="form-label">Новый пароль<span
                                                            class="text-danger"><b> *</b></span>
                                                        <button type="button" class="btn btn-light btn-sm ms-3"
                                                                style="margin-top: -0.6rem; margin-bottom: -0.5rem;"
                                                                data-bs-toggle="modal" data-bs-target="#exampleModal5">
                                                            Правила
                                                        </button>
                                                    </label>
                                                    <input type="password" class="form-control" id="newpasswordInput"
                                                           placeholder="Новый пароль" name="new_password" required>
                                                    @error('new_password')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-4 mb-3">
                                                <div>
                                                    <label for="confirmpasswordInput" class="form-label">Подтверждение
                                                        пароля<span class="text-danger"><b> *</b></span></label>
                                                    <input type="password" class="form-control"
                                                           id="confirmpasswordInput" placeholder="Еще раз новый пароль"
                                                           name="new_password_confirmed" required>
                                                    @error('new_password_confirmed')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-success col-lg-4">Сохранить
                                                        изменение
                                                    </button>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </form>
                                </div>
                                <!--end tab-pane-->
                                <div class="tab-pane" id="experience" role="tabpanel">
                                    <form action="{{route('settings.update_responsibility_trks_systems', $user)}}"
                                          method="post">
                                        @csrf
                                        @method('patch')
                                        <div class="row">
                                            <div class="col">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="row">
                                                            <div class="col pt-1">
                                                                <b>Выберите свои ТРК и системы</b>
                                                            </div>
                                                            <div class="col">
                                                                <!-- Button trigger modal responsibility -->
                                                                <button type="button" class="btn btn-light btn-sm"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#exampleModal">Зачем это?
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="accordion accordion-flush"
                                                             id="accordionFlushExample">
                                                            @forelse($trks as $trk)
                                                                <div class="accordion-item">
                                                                    <h2 class="accordion-header"
                                                                        id="flush-headingOne{{$trk->id}}">
                                                                        <button class="accordion-button collapsed"
                                                                                type="button" data-bs-toggle="collapse"
                                                                                data-bs-target="#flush-collapseOne{{$trk->id}}"
                                                                                aria-expanded="false"
                                                                                aria-controls="flush-collapseOne{{$trk->id}}">
                                                                            {{$trk->name}}
                                                                        </button>
                                                                    </h2>
                                                                    <div id="flush-collapseOne{{$trk->id}}"
                                                                         class="accordion-collapse collapse"
                                                                         aria-labelledby="flush-headingOne{{$trk->id}}"
                                                                         data-bs-parent="#accordionFlushExample">
                                                                        <div class="accordion-body">
                                                                            <ul class="list-group list-group-flush">
                                                                                @forelse($systems as $system)
                                                                                    <li class="list-group-item">
                                                                                        <input
                                                                                            @if(array_key_exists($trk->id, $user_responsibility_trks_systems) && in_array($system->id, $user_responsibility_trks_systems[$trk->id]))
                                                                                                checked
                                                                                            @endif
                                                                                            class="form-check-input me-3"
                                                                                            type="checkbox" value=""
                                                                                            id="flexCheckDefault{{$trk->id}}{{$system->id}}"
                                                                                            name="trks_systems[{{$trk->id}}][{{$system->id}}][]"
                                                                                            multiple>
                                                                                        <label class="form-check-label"
                                                                                               for="flexCheckDefault{{$trk->id}}{{$system->id}}">
                                                                                            {{$system->name}}
                                                                                        </label>
                                                                                    </li>
                                                                                @empty
                                                                                    <li class="list-group-item">Нет
                                                                                        данных ...
                                                                                    </li>
                                                                                @endforelse
                                                                            </ul>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <p>нет данных ...</p>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 text-end">
                                            <button type="submit" class="btn btn-success col-6">Сохранить</button>
                                        </div>
                                        <!--end col-->
                                    </form>
                                </div>
                                <!--end tab-pane-->
{{--                                <div class="tab-pane" id="filters" role="tabpanel">--}}
{{--                                    <form action="{{route('settings.update_trks_systems_filter', $user)}}"--}}
{{--                                          method="post">--}}
{{--                                        @csrf--}}
{{--                                        @method('patch')--}}
{{--                                        <div class="row">--}}
{{--                                            <div class="col">--}}
{{--                                                <div class="card">--}}
{{--                                                    <div class="card-header">--}}
{{--                                                        <div class="row">--}}
{{--                                                            <div class="col pt-1">--}}
{{--                                                                <b>Фильтры</b>--}}
{{--                                                            </div>--}}
{{--                                                            <div class="col">--}}
{{--                                                                <!-- Button trigger modal responsibility -->--}}
{{--                                                                <button type="button" class="btn btn-light btn-sm"--}}
{{--                                                                        data-bs-toggle="modal"--}}
{{--                                                                        data-bs-target="#exampleModal3">Зачем это?--}}
{{--                                                                </button>--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                    <div class="card-body">--}}
{{--                                                        <div class="accordion accordion-flush"--}}
{{--                                                             id="accordionFlushExample44">--}}

{{--                                                            @forelse($trks as $trk)--}}
{{--                                                                <div class="accordion-item">--}}
{{--                                                                    <h2 class="accordion-header"--}}
{{--                                                                        id="flush-headingOne{{$trk->id}}44">--}}
{{--                                                                        <button class="accordion-button collapsed"--}}
{{--                                                                                type="button" data-bs-toggle="collapse"--}}
{{--                                                                                data-bs-target="#flush-collapseOne{{$trk->id}}44"--}}
{{--                                                                                aria-expanded="false"--}}
{{--                                                                                aria-controls="flush-collapseOne{{$trk->id}}44">--}}
{{--                                                                            {{$trk->name}}--}}
{{--                                                                        </button>--}}
{{--                                                                    </h2>--}}
{{--                                                                    <div id="flush-collapseOne{{$trk->id}}44"--}}
{{--                                                                         class="accordion-collapse collapse"--}}
{{--                                                                         aria-labelledby="flush-headingOne{{$trk->id}}44"--}}
{{--                                                                         data-bs-parent="#accordionFlushExample44">--}}
{{--                                                                        <div class="accordion-body">--}}
{{--                                                                            <ul class="list-group list-group-flush">--}}
{{--                                                                                @forelse($systems as $system)--}}
{{--                                                                                    <li class="list-group-item">--}}
{{--                                                                                        <input--}}
{{--                                                                                            @if(array_key_exists($trk->id, $user_filter_trks_systems) && in_array($system->id, $user_filter_trks_systems[$trk->id]))--}}
{{--                                                                                                checked--}}
{{--                                                                                            @endif--}}
{{--                                                                                            class="form-check-input me-3"--}}
{{--                                                                                            type="checkbox" value=""--}}
{{--                                                                                            id="flexCheckDefault{{$trk->id}}{{$system->id}}44"--}}
{{--                                                                                            name="trks_systems[{{$trk->id}}][{{$system->id}}][]"--}}
{{--                                                                                            multiple>--}}
{{--                                                                                        <label class="form-check-label"--}}
{{--                                                                                               for="flexCheckDefault{{$trk->id}}{{$system->id}}44">--}}
{{--                                                                                            {{$system->name}}--}}
{{--                                                                                        </label>--}}
{{--                                                                                    </li>--}}
{{--                                                                                @empty--}}
{{--                                                                                    <li class="list-group-item">Нет--}}
{{--                                                                                        данных ...--}}
{{--                                                                                    </li>--}}
{{--                                                                                @endforelse--}}
{{--                                                                            </ul>--}}
{{--                                                                        </div>--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}
{{--                                                            @empty--}}
{{--                                                                <p>нет данных ...</p>--}}
{{--                                                            @endforelse--}}

{{--                                                        </div>--}}
{{--                                                    </div>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <div class="col-lg-12 text-end">--}}
{{--                                            <button type="submit" class="btn btn-success col-6">Сохранить</button>--}}
{{--                                        </div>--}}
{{--                                    </form>--}}
{{--                                </div>--}}

                                <div class="tab-pane" id="emails" role="tabpanel">
                                    <form action="{{route('settings.update_event_subscription', $user)}}" method="post">
                                        @csrf
                                        @method('patch')
                                        <div class="row">
                                            <div class="col">
                                                <div class="card">
                                                    <div class="card-header">
                                                        <div class="row">
                                                            <div class="col pt-1">
                                                                <b>Рассылка</b>
                                                            </div>
                                                            <div class="col">
                                                                <!-- Button trigger modal responsibility -->
                                                                <button type="button" class="btn btn-light btn-sm"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#emails_send">Зачем это?
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="card-body">
                                                        <!-- TODO оповещения функционал отправки писем -->
                                                        <p>Хочу получать оповещения:</p>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="1"
                                                                   id="task_from_user"
                                                                   name="task_from_user" {{isset($user->notification->task_from_user) && $user->notification->task_from_user ? 'checked' : null}}>
                                                            <label class="form-check-label" for="task_from_user">
                                                                Ход выполнения поставленных мною задач
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="1"
                                                                   id="task_to_user"
                                                                   name="task_to_user" {{isset($user->notification->task_to_user) && $user->notification->task_to_user ? 'checked' : null}}>
                                                            <label class="form-check-label" for="task_to_user">
                                                                Новая задача мне
                                                            </label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="1"
                                                                   id="app_from_user"
                                                                   name="app_from_user" {{isset($user->notification->app_from_user) && $user->notification->app_from_user ? 'checked' : null}}>
                                                            <label class="form-check-label" for="app_from_user">
                                                                Ход выполнения созданных мною заявок
                                                            </label>
                                                        </div>
                                                        <div class="form-check mb-3">
                                                            <input class="form-check-input" type="checkbox" value="1"
                                                                   id="app_to_user_division"
                                                                   name="app_to_user_division" {{isset($user->notification->app_to_user_division) && $user->notification->app_to_user_division ? 'checked' : null}}>
                                                            <label class="form-check-label" for="app_to_user_division">
                                                                Новая заявка моему подразделению
                                                            </label>
                                                        </div>
                                                        <!-- TODO еженедельную рассылку функционал рассылки писем -->
{{--                                                        <p>Рассылка на почту:</p>--}}
{{--                                                        <div class="form-check">--}}
{{--                                                            <input class="form-check-input" type="checkbox" value="1"--}}
{{--                                                                   id="tasks_need_to_do_till_weekend"--}}
{{--                                                                   name="tasks_need_to_do_till_weekend" {{isset($user->notification->tasks_need_to_do_till_weekend) && $user->notification->tasks_need_to_do_till_weekend ? 'checked' : null}}>--}}
{{--                                                            <label class="form-check-label" for="tasks_need_to_do_till_weekend">--}}
{{--                                                                Список задач до конца недели и просроченные--}}
{{--                                                            </label>--}}
{{--                                                        </div>--}}
{{--                                                        <div class="form-check">--}}
{{--                                                            <input class="form-check-input" type="checkbox" value="1"--}}
{{--                                                                   id="repairs_need_to_do_till_weekend"--}}
{{--                                                                   name="repairs_need_to_do_till_weekend" {{isset($user->notification->repairs_need_to_do_till_weekend) && $user->notification->repairs_need_to_do_till_weekend ? 'checked' : null}}>--}}
{{--                                                            <label class="form-check-label" for="repairs_need_to_do_till_weekend">--}}
{{--                                                                Список ремонта до конца месяца и просроченный--}}
{{--                                                            </label>--}}
{{--                                                        </div>--}}
{{--                                                        <div class="form-check">--}}
{{--                                                            <input class="form-check-input" type="checkbox" value="1"--}}
{{--                                                                   id="periodical_works_need_to_do_till_weekend"--}}
{{--                                                                   name="periodical_works_need_to_do_till_weekend" {{isset($user->notification->periodical_works_need_to_do_till_weekend) && $user->notification->periodical_works_need_to_do_till_weekend ? 'checked' : null}}>--}}
{{--                                                            <label class="form-check-label" for="periodical_works_need_to_do_till_weekend">--}}
{{--                                                                Список тех.мероприятий до конца месяца и просроченные--}}
{{--                                                            </label>--}}
{{--                                                        </div>--}}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 text-end">
                                            <button type="submit" class="btn btn-success col-6">Сохранить</button>
                                        </div>
                                    </form>
                                </div>


                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->

        </div>
        <!-- Modal responsibility -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">ТРК/Системы</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Выберите ТРК и Системы, которые Вы обслуживаете.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal alert -->
        <div class="modal fade" id="emails_send" tabindex="-1" aria-labelledby="emails_label" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="emails_label">Оповещения</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>На Вашу почту будут приходить письма оповещения.</p>
                        <p>Тех. мероприятия - работы на оборудовании, которые выбраны в качестве периодических, ТО, замена фильтров и т.д.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal filters -->
        <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel3"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel3">Фильтры</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Отметьте те ТРК/системы,</p>
                        <p>которые вы хотите видеть</p>
                        <p>при работе с сайтом.</p>
                        <p>Остальные ТРК/системы не будут</p>
                        <p>отображаться на сайте.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal password change -->
        <div class="modal fade" id="exampleModal5" tabindex="-1" aria-labelledby="exampleModalLabel5"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel5">Новый пароль</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Не меньше восьми символов</p>
                        <p>Только латиница</p>
                        <p>Должны использоваться символы в нижнем регистре</p>
                        <p>В верхнем регистре</p>
                        <p>Цифры</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- container-fluid -->
    </div><!-- End Page-content -->
@endsection
