@extends('layouts.backend.main')

@section('title', 'Главная | Профиль сотрудника')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Профиль сотрудника</h4>
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
                                <a href="{{route('settings.edit', $user->id)}}" class="btn btn-success"><i
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
                                                                            <b>Ваши ТРК/Системы</b>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="accordion accordion-flush"
                                                                         id="accordionFlushExample">

                                                                        @forelse($user->responsibility_trks as $trk)
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="flush-heading{{$trk->id}}">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#flush-collapse{{$trk->id}}"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="flush-collapse{{$trk->id}}">
                                                                                        {{$trk->name}}
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="flush-collapse{{$trk->id}}"
                                                                                     class="accordion-collapse collapse"
                                                                                     aria-labelledby="flush-heading{{$trk->id}}"
                                                                                     data-bs-parent="#accordionFlushExample">
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
                                                                            <p>Не выбраны</p>
                                                                        @endforelse

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row row-cols-1 row-cols-md-2">
                                                        <div class="col">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <b>Оповещения на почту</b>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="accordion accordion-flush"
                                                                         id="accordionFlushExample2">
                                                                        @forelse($user->subscription_entities as $entity)
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="flush-heading{{$entity->id}}">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#flush-collapse{{$entity->id}}"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="flush-collapse{{$entity->id}}">
                                                                                        {{$entity->name}}
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="flush-collapse{{$entity->id}}"
                                                                                     class="accordion-collapse collapse"
                                                                                     aria-labelledby="flush-heading{{$entity->id}}"
                                                                                     data-bs-parent="#accordionFlushExample2">
                                                                                    <div class="accordion-body">
                                                                                        <ul class="list-group list-group-flush">
                                                                                            @forelse($entity->events as $event)
                                                                                                <li class="list-group-item">{{$event->name}}</li>
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
                                                                            <p>Не выбраны</p>
                                                                        @endforelse
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <div class="row">
                                                                        <div class="col pt-1">
                                                                            <b>Ваши чаты для заявок</b>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="accordion accordion-flush"
                                                                         id="accordionFlushExample888">
                                                                        @forelse($user->chat_trks as $trk)
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="flush-heading{{$trk->id}}">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#flush-collapse{{$trk->id}}"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="flush-collapse{{$trk->id}}">
                                                                                        {{$trk->name}}
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="flush-collapse{{$trk->id}}"
                                                                                     class="accordion-collapse collapse"
                                                                                     aria-labelledby="flush-heading{{$trk->id}}"
                                                                                     data-bs-parent="#accordionFlushExample888">
                                                                                    <div class="accordion-body">
                                                                                        <ul class="list-group list-group-flush">
                                                                                            @forelse($trk->chat_divisions as $division)
                                                                                                <li class="list-group-item">{{$division->name}}</li>
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
                                                                            <p>Не выбраны</p>
                                                                        @endforelse

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end row-->
                                                    <div class="row row-cols-1 row-cols-md-2">
                                                        <div class="col">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <b>Фильтры ТРК/Системы</b>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    <div class="accordion accordion-flush"
                                                                         id="accordionFlushExample1234">
                                                                        @forelse($user->filter_trks as $trk)
                                                                            <div class="accordion-item">
                                                                                <h2 class="accordion-header"
                                                                                    id="flush-heading{{$trk->id}}">
                                                                                    <button
                                                                                        class="accordion-button collapsed"
                                                                                        type="button"
                                                                                        data-bs-toggle="collapse"
                                                                                        data-bs-target="#flush-collapse{{$trk->id}}"
                                                                                        aria-expanded="false"
                                                                                        aria-controls="flush-collapse{{$trk->id}}">
                                                                                        {{$trk->name}}
                                                                                    </button>
                                                                                </h2>
                                                                                <div id="flush-collapse{{$trk->id}}"
                                                                                     class="accordion-collapse collapse"
                                                                                     aria-labelledby="flush-heading{{$trk->id}}"
                                                                                     data-bs-parent="#accordionFlushExample1234">
                                                                                    <div class="accordion-body">
                                                                                        <ul class="list-group list-group-flush">
                                                                                            @forelse($trk->filter_systems as $system)
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
                                                                            <p>Не выбраны</p>
                                                                        @endforelse
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col">
                                                            <div class="card">
                                                                <div class="card-header">
                                                                    <div class="row">
                                                                        <div class="col">
                                                                            <b>Подчиненные</b>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="card-body">
                                                                    @if(!empty($user->subordinates) && count($user->subordinates) > 0)
                                                                        @foreach($user->subordinates as $subordinate)
                                                                            <li class="list-group-item">{{$subordinate->name}}
                                                                                <a href="tel:{{$subordinate->phone}}"
                                                                                   class="ms-2">
                                                                                    <img
                                                                                        src="{{asset('assets/images/backend/svg/phone-line.svg')}}"
                                                                                        alt="Иконка телефона"
                                                                                        title="Позвонить" width="20"
                                                                                        height="20">
                                                                                </a>
                                                                                <a href="mailto:{{$subordinate->email}}"
                                                                                   class="ms-2">
                                                                                    <img
                                                                                        src="{{asset('assets/images/backend/svg/mail-send-line.svg')}}"
                                                                                        alt="Иконка телефона"
                                                                                        title="Написать" width="20"
                                                                                        height="20">
                                                                                </a>
                                                                            </li>
                                                                        @endforeach
                                                                    @else
                                                                        <p>Отсутствуют</p>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!--end row-->
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
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
