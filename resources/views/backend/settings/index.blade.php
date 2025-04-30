@extends('layouts.backend.main')

@section('title', 'Главная | Настройки')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Настройки</h4>
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

                <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 px-4">
                    <div class="row g-4">
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
                                <a href="{{route('settings.edit', $user)}}" class="btn btn-success"><i
                                        class="ri-edit-box-line align-bottom"></i> Редактировать настройки</a>
                            </div>
                        </div>
                    </div>
                    <!--end row-->
                </div>

                <div class="row px-4">
                    <div class="col-lg-12">
                        <div>
                            <div class="d-flex">
                            </div>
                            <!-- Tab panes -->
                            <div class="tab-content text-muted">
                                <div class="tab-pane active" id="overview-tab" role="tabpanel">
                                    <div class="col">
                                        @include('components.backend.message')
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <div class="card">
                                                <div class="card-body">
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <b>Мои ТРК/Системы</b>
                                                                        </div>
                                                                        <div class="col">
                                                                            <button type="button"
                                                                                    class="btn btn-light btn-sm"
                                                                                    data-bs-toggle="modal"
                                                                                    data-bs-target="#responsibility">Что
                                                                                это?
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="accordion accordion-flush"
                                                                         id="accordionFlushExample789">

                                                                        @forelse($user->responsibility_trks as $trk)
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="flush-heading{{$trk->id}}789">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#flush-collapse{{$trk->id}}789"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="flush-collapse{{$trk->id}}789">
                                                                                        {{$trk->name}}
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="flush-collapse{{$trk->id}}789"
                                                                                     class="accordion-collapse collapse"
                                                                                     aria-labelledby="flush-heading{{$trk->id}}789"
                                                                                     data-bs-parent="#accordionFlushExample789">
                                                                                    <div class="accordion-body">
                                                                                        <ul class="list-group list-group-flush">
                                                                                            @forelse($trk->systems as $system)
                                                                                                <li class="list-group-item">{{$system->name}}</li>
                                                                                            @empty
                                                                                                <li class="list-group-item">
                                                                                                    нет данных ...
                                                                                                </li>
                                                                                            @endforelse
                                                                                        </ul>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        @empty
                                                                            <span>не выбраны</span>
                                                                        @endforelse

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    {{--                                                    <div class="row">--}}
                                                    {{--                                                        <div class="col">--}}
                                                    {{--                                                            <div class="card">--}}
                                                    {{--                                                                <div class="card-header"><div class="row">--}}
                                                    {{--                                                                        <div class="col">--}}
                                                    {{--                                                                            <b>Оповещения на почту</b>--}}
                                                    {{--                                                                        </div>--}}
                                                    {{--                                                                        <div class="col">--}}
                                                    {{--                                                                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#emails">Что это?</button>--}}
                                                    {{--                                                                        </div>--}}
                                                    {{--                                                                    </div></div>--}}
                                                    {{--                                                                <div class="card-body">--}}
                                                    {{--                                                                    <div class="accordion accordion-flush" id="accordionFlushExample2">--}}
                                                    {{--                                                                        @forelse($user->subscription_entities as $entity)--}}
                                                    {{--                                                                            <div class="accordion-item">--}}
                                                    {{--                                                                                <h2 class="accordion-header" id="flush-heading{{$entity->id}}">--}}
                                                    {{--                                                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse{{$entity->id}}" aria-expanded="false" aria-controls="flush-collapse{{$entity->id}}">--}}
                                                    {{--                                                                                        {{$entity->name}}--}}
                                                    {{--                                                                                    </button>--}}
                                                    {{--                                                                                </h2>--}}
                                                    {{--                                                                                <div id="flush-collapse{{$entity->id}}" class="accordion-collapse collapse" aria-labelledby="flush-heading{{$entity->id}}" data-bs-parent="#accordionFlushExample2">--}}
                                                    {{--                                                                                    <div class="accordion-body">--}}
                                                    {{--                                                                                        <ul class="list-group list-group-flush">--}}
                                                    {{--                                                                                            @forelse($entity->events as $event)--}}
                                                    {{--                                                                                                <li class="list-group-item">{{$event->name}}</li>--}}
                                                    {{--                                                                                            @empty--}}
                                                    {{--                                                                                                <li class="list-group-item">нет данных ...</li>--}}
                                                    {{--                                                                                            @endforelse--}}
                                                    {{--                                                                                        </ul>--}}
                                                    {{--                                                                                    </div>--}}
                                                    {{--                                                                                </div>--}}
                                                    {{--                                                                            </div>--}}
                                                    {{--                                                                        @empty--}}
                                                    {{--                                                                            <p>Не выбраны</p>--}}
                                                    {{--                                                                        @endforelse--}}
                                                    {{--                                                                    </div>--}}
                                                    {{--                                                                </div>--}}
                                                    {{--                                                            </div>--}}
                                                    {{--                                                        </div>--}}
                                                    {{--                                                    </div>--}}
{{--                                                    <div class="row">--}}
{{--                                                        <div class="col">--}}
{{--                                                            <div class="card">--}}
{{--                                                                <div class="card-header">--}}
{{--                                                                    <div class="row">--}}
{{--                                                                        <div class="col">--}}
{{--                                                                            <b>Фильтры ТРК/Системы</b>--}}
{{--                                                                        </div>--}}
{{--                                                                        <div class="col">--}}
{{--                                                                            <button type="button"--}}
{{--                                                                                    class="btn btn-light btn-sm"--}}
{{--                                                                                    data-bs-toggle="modal"--}}
{{--                                                                                    data-bs-target="#filters">Что это?--}}
{{--                                                                            </button>--}}
{{--                                                                        </div>--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}
{{--                                                                <div class="card-body">--}}
{{--                                                                    <div class="accordion accordion-flush"--}}
{{--                                                                         id="accordionFlushExample1234">--}}
{{--                                                                        @forelse($user->filter_trks as $trk)--}}
{{--                                                                            <div class="accordion-item">--}}
{{--                                                                                <h2 class="accordion-header"--}}
{{--                                                                                    id="flush-heading{{$trk->id}}1234">--}}
{{--                                                                                    <button--}}
{{--                                                                                        class="accordion-button collapsed"--}}
{{--                                                                                        type="button"--}}
{{--                                                                                        data-bs-toggle="collapse"--}}
{{--                                                                                        data-bs-target="#flush-collapse{{$trk->id}}1234"--}}
{{--                                                                                        aria-expanded="false"--}}
{{--                                                                                        aria-controls="flush-collapse{{$trk->id}}1234">--}}
{{--                                                                                        {{$trk->name}}--}}
{{--                                                                                    </button>--}}
{{--                                                                                </h2>--}}
{{--                                                                                <div id="flush-collapse{{$trk->id}}1234"--}}
{{--                                                                                     class="accordion-collapse collapse"--}}
{{--                                                                                     aria-labelledby="flush-heading{{$trk->id}}"--}}
{{--                                                                                     data-bs-parent="#accordionFlushExample1234">--}}
{{--                                                                                    <div class="accordion-body">--}}
{{--                                                                                        <ul class="list-group list-group-flush">--}}
{{--                                                                                            @forelse($trk->filter_systems as $system)--}}
{{--                                                                                                <li class="list-group-item">{{$system->name}}</li>--}}
{{--                                                                                            @empty--}}
{{--                                                                                                <li class="list-group-item">--}}
{{--                                                                                                    нет данных ...--}}
{{--                                                                                                </li>--}}
{{--                                                                                            @endforelse--}}
{{--                                                                                        </ul>--}}
{{--                                                                                    </div>--}}
{{--                                                                                </div>--}}
{{--                                                                            </div>--}}
{{--                                                                        @empty--}}
{{--                                                                            <p>Не выбраны</p>--}}
{{--                                                                        @endforelse--}}
{{--                                                                    </div>--}}
{{--                                                                </div>--}}
{{--                                                            </div>--}}
{{--                                                        </div>--}}
{{--                                                    </div>--}}
                                                    <div class="row">
                                                        <div class="col">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <b>Оповещения на почту</b>
                                                                        </div>
{{--                                                                        <div class="col">--}}
{{--                                                                            <button type="button"--}}
{{--                                                                                    class="btn btn-light btn-sm"--}}
{{--                                                                                    data-bs-toggle="modal"--}}
{{--                                                                                    data-bs-target="#renters_emails">Что--}}
{{--                                                                                это?--}}
{{--                                                                            </button>--}}
{{--                                                                        </div>--}}
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    @if(!empty($user->notification->task_from_user))
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" value="1"
                                                                               id="task_from_user"
                                                                               name="task_from_user" {{isset($user->notification->task_from_user) && $user->notification->task_from_user ? 'checked' : null}} disabled>
                                                                        <label class="form-check-label" for="task_from_user">
                                                                            Ход выполнения поставленных мною задач
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" value="1"
                                                                               id="task_to_user"
                                                                               name="task_to_user" {{isset($user->notification->task_to_user) && $user->notification->task_to_user ? 'checked' : null}} disabled>
                                                                        <label class="form-check-label" for="task_to_user">
                                                                            Новая задача мне
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-check">
                                                                        <input class="form-check-input" type="checkbox" value="1"
                                                                               id="app_from_user"
                                                                               name="app_from_user" {{isset($user->notification->app_from_user) && $user->notification->app_from_user ? 'checked' : null}} disabled>
                                                                        <label class="form-check-label" for="app_from_user">
                                                                            Ход выполнения созданных мною заявок
                                                                        </label>
                                                                    </div>
                                                                    <div class="form-check mb-3">
                                                                        <input class="form-check-input" type="checkbox" value="1"
                                                                               id="app_to_user_division"
                                                                               name="app_to_user_division" {{isset($user->notification->app_to_user_division) && $user->notification->app_to_user_division ? 'checked' : null}} disabled>
                                                                        <label class="form-check-label" for="app_to_user_division">
                                                                            Новая заявка моему подразделению
                                                                        </label>
                                                                    </div>
                                                                    <!-- TODO еженедельную рассылку -->
{{--                                                                    <p>Еженедельная рассылка:</p>--}}
{{--                                                                    <div class="form-check">--}}
{{--                                                                        <input class="form-check-input" type="checkbox" value="1"--}}
{{--                                                                               id="tasks_need_to_do_till_weekend"--}}
{{--                                                                               name="tasks_need_to_do_till_weekend" {{isset($user->notification->tasks_need_to_do_till_weekend) && $user->notification->tasks_need_to_do_till_weekend ? 'checked' : null}} disabled>--}}
{{--                                                                        <label class="form-check-label" for="tasks_need_to_do_till_weekend">--}}
{{--                                                                            Список задач до конца недели и просроченные--}}
{{--                                                                        </label>--}}
{{--                                                                    </div>--}}
{{--                                                                    <div class="form-check">--}}
{{--                                                                        <input class="form-check-input" type="checkbox" value="1"--}}
{{--                                                                               id="repairs_need_to_do_till_weekend"--}}
{{--                                                                               name="repairs_need_to_do_till_weekend" {{isset($user->notification->repairs_need_to_do_till_weekend) && $user->notification->repairs_need_to_do_till_weekend ? 'checked' : null}} disabled>--}}
{{--                                                                        <label class="form-check-label" for="repairs_need_to_do_till_weekend">--}}
{{--                                                                            Список ремонта до конца недели и просроченный--}}
{{--                                                                        </label>--}}
{{--                                                                    </div>--}}
{{--                                                                    <div class="form-check">--}}
{{--                                                                        <input class="form-check-input" type="checkbox" value="1"--}}
{{--                                                                               id="periodical_works_need_to_do_till_weekend"--}}
{{--                                                                               name="periodical_works_need_to_do_till_weekend" {{isset($user->notification->periodical_works_need_to_do_till_weekend) && $user->notification->periodical_works_need_to_do_till_weekend ? 'checked' : null}} disabled>--}}
{{--                                                                        <label class="form-check-label" for="periodical_works_need_to_do_till_weekend">--}}
{{--                                                                            Список тех.мероприятий до конца недели и просроченные--}}
{{--                                                                        </label>--}}
{{--                                                                    </div>--}}
                                                                    @else
                                                                        <span>не выбраны</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!--end card-body-->
                                            </div><!-- end card -->

                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </div>
                            </div>
                            <!--end tab-content-->
                        </div>
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->
            </div>
        </div>
        <!-- Modal about resposibility -->
        <div class="modal fade" id="responsibility" tabindex="-1" aria-labelledby="responsibility" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel5">ТРК/Системы</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>ТРК и Системы, которые Вы обслуживаете.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
