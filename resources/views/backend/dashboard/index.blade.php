
<p>{{$info}}</p>


{{--@extends('layouts.backend.main')--}}

{{--@section('title', 'Главная | Админ панель')--}}

{{--@section('content')--}}
{{--    <div class="page-content">--}}
{{--        <div class="container-fluid">--}}
{{--            <div class="row">--}}
{{--                <div class="col-12">--}}
{{--                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">--}}
{{--                        <h4 class="mb-sm-0">Главная</h4>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            <div class="row row-cols-1 row-cols-md-2">--}}
{{--                @if($tasks['show_tasks_to_user'] ?? false)--}}
{{--                    <div class="col">--}}
{{--                        <div class="card  shadow">--}}
{{--                            <div class="card-header">--}}
{{--                                <span class="fs-4 me-2">Задачи Вам</span>--}}
{{--                            </div>--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="accordion accordion-flush" id="accordionFlushExample2">--}}
{{--                                    @if(count($tasks['to_user']['new']) > 0)--}}
{{--                                        <div class="accordion-item">--}}
{{--                                            <h2 class="accordion-header" id="flush-headingOne">--}}
{{--                                                <button class="accordion-button collapsed" type="button"--}}
{{--                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"--}}
{{--                                                        aria-expanded="false" aria-controls="flush-collapseOne">--}}
{{--                                                    Новые--}}
{{--                                                    <span--}}
{{--                                                        class="ms-2 badge text-bg-warning">{{count($tasks['to_user']['new'])}}</span>--}}
{{--                                                </button>--}}
{{--                                            </h2>--}}
{{--                                            <div id="flush-collapseOne" class="accordion-collapse collapse"--}}
{{--                                                 aria-labelledby="flush-headingOne"--}}
{{--                                                 data-bs-parent="#accordionFlushExample2">--}}
{{--                                                <div class="accordion-body">--}}
{{--                                                    <ol class="list-group list-group-numbered list-group-flush">--}}
{{--                                                        @forelse($tasks['to_user']['new'] as $new_task_to_user)--}}
{{--                                                            <li class="list-group-item"><a--}}
{{--                                                                    href="{{route('tasks.show', $new_task_to_user->id)}}">{{$new_task_to_user->description}}</a>--}}
{{--                                                            </li>--}}
{{--                                                        @empty--}}
{{--                                                            <li class="list-group-item">Новых задач нет</li>--}}
{{--                                                        @endforelse--}}
{{--                                                    </ol>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                    @if(count($tasks['to_user']['in_process']) > 0)--}}
{{--                                        <div class="accordion-item">--}}
{{--                                            <h2 class="accordion-header" id="flush-headingTwo">--}}
{{--                                                <button class="accordion-button collapsed" type="button"--}}
{{--                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo"--}}
{{--                                                        aria-expanded="false" aria-controls="flush-collapseTwo">--}}
{{--                                                    Выполняются<span--}}
{{--                                                        class="ms-2 badge text-bg-success">{{count($tasks['to_user']['in_process'])}}</span>--}}
{{--                                                </button>--}}
{{--                                            </h2>--}}
{{--                                            <div id="flush-collapseTwo" class="accordion-collapse collapse"--}}
{{--                                                 aria-labelledby="flush-headingTwo"--}}
{{--                                                 data-bs-parent="#accordionFlushExample2">--}}
{{--                                                <div class="accordion-body">--}}
{{--                                                    <ol class="list-group list-group-numbered list-group-flush">--}}
{{--                                                        @forelse($tasks['to_user']['in_process'] as $task_to_user_in_process)--}}
{{--                                                            <li class="list-group-item"><a--}}
{{--                                                                    href="{{route('tasks.show', $task_to_user_in_process->id)}}">--}}
{{--                                                                    {{$task_to_user_in_process->description}}--}}
{{--                                                                </a>--}}
{{--                                                                {{$task_to_user_in_process->done_progress . '%'}}--}}
{{--                                                            </li>--}}
{{--                                                        @empty--}}
{{--                                                            <li class="list-group-item">Задач в процессе нет</li>--}}
{{--                                                        @endforelse--}}
{{--                                                    </ol>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                    @if(count($tasks['to_user']['expired']) > 0)--}}
{{--                                        <div class="accordion-item">--}}
{{--                                            <h2 class="accordion-header" id="flush-heading3">--}}
{{--                                                <button class="accordion-button collapsed" type="button"--}}
{{--                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapse3"--}}
{{--                                                        aria-expanded="false" aria-controls="flush-collapse3">--}}
{{--                                                    Просроченные<span--}}
{{--                                                        class="ms-2 badge text-bg-danger">{{count($tasks['to_user']['expired'])}}</span>--}}
{{--                                                </button>--}}
{{--                                            </h2>--}}
{{--                                            <div id="flush-collapse3" class="accordion-collapse collapse"--}}
{{--                                                 aria-labelledby="flush-heading3"--}}
{{--                                                 data-bs-parent="#accordionFlushExample2">--}}
{{--                                                <div class="accordion-body">--}}
{{--                                                    <ol class="list-group list-group-numbered list-group-flush">--}}
{{--                                                        @forelse($tasks['to_user']['expired'] as $expired_task_to_user)--}}
{{--                                                            <li class="list-group-item"><a--}}
{{--                                                                    href="{{route('tasks.show', $expired_task_to_user->id)}}">--}}
{{--                                                                    {{$expired_task_to_user->description}}--}}
{{--                                                                </a>--}}
{{--                                                                {{$expired_task_to_user->done_progress . '%'}}--}}
{{--                                                            </li>--}}
{{--                                                        @empty--}}
{{--                                                            <li class="list-group-item">Просроченных задач нет</li>--}}
{{--                                                        @endforelse--}}
{{--                                                    </ol>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--                @if($tasks['show_tasks_from_user'] ?? false)--}}
{{--                    <div class="col">--}}
{{--                        <div class="card shadow">--}}
{{--                            <div class="card-header">--}}
{{--                                <span class="fs-4 me-2">Задачи сотрудникам от Вас</span>--}}
{{--                                @if(auth()->user()->can('task create') || auth()->user()->can('all'))--}}
{{--                                    <a href="{{route('tasks.create')}}"><img class="pb-1"--}}
{{--                                                                             src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"--}}
{{--                                                                             alt="Add" title="Добавить" height="30"></a>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="accordion accordion-flush" id="accordionFlushExample">--}}
{{--                                    @if(!empty($tasks['from_user']['new']) && count($tasks['from_user']['new']) > 0)--}}
{{--                                        <div class="accordion-item">--}}
{{--                                            <h2 class="accordion-header" id="flush-headingThree">--}}
{{--                                                <button class="accordion-button collapsed" type="button"--}}
{{--                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseThree"--}}
{{--                                                        aria-expanded="false" aria-controls="flush-collapseThree">--}}
{{--                                                    Новые<span--}}
{{--                                                        class="ms-2 badge text-bg-warning">{{count($tasks['from_user']['new'])}}</span>--}}
{{--                                                </button>--}}
{{--                                            </h2>--}}
{{--                                            <div id="flush-collapseThree" class="accordion-collapse collapse"--}}
{{--                                                 aria-labelledby="flush-headingThree"--}}
{{--                                                 data-bs-parent="#accordionFlushExample">--}}
{{--                                                <div class="accordion-body">--}}
{{--                                                    <ol class="list-group list-group-numbered list-group-flush">--}}
{{--                                                        @forelse($tasks['from_user']['new'] as $new_task_from_user)--}}
{{--                                                            <li class="list-group-item"><a--}}
{{--                                                                    href="{{route('tasks.show', $new_task_from_user->id)}}">{{$new_task_from_user->description}}</a>--}}
{{--                                                            </li>--}}
{{--                                                        @empty--}}
{{--                                                            <li class="list-group-item">Новых задач нет</li>--}}
{{--                                                        @endforelse--}}
{{--                                                    </ol>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                    @if(!empty($tasks['from_user']['in_process']) && count($tasks['from_user']['in_process']) > 0)--}}
{{--                                        <div class="accordion-item">--}}
{{--                                            <h2 class="accordion-header" id="flush-heading4">--}}
{{--                                                <button class="accordion-button collapsed" type="button"--}}
{{--                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapse4"--}}
{{--                                                        aria-expanded="false" aria-controls="flush-collapse4">--}}
{{--                                                    Выполняются<span--}}
{{--                                                        class="ms-2 badge text-bg-success">{{count($tasks['from_user']['in_process'])}}</span>--}}
{{--                                                </button>--}}
{{--                                            </h2>--}}
{{--                                            <div id="flush-collapse4" class="accordion-collapse collapse"--}}
{{--                                                 aria-labelledby="flush-heading4"--}}
{{--                                                 data-bs-parent="#accordionFlushExample">--}}
{{--                                                <div class="accordion-body">--}}
{{--                                                    <ol class="list-group list-group-numbered list-group-flush">--}}
{{--                                                        @forelse($tasks['from_user']['in_process'] as $task_from_user_in_process)--}}
{{--                                                            <li class="list-group-item"><a--}}
{{--                                                                    href="{{route('tasks.show', $task_from_user_in_process->id)}}">--}}
{{--                                                                    {{$task_from_user_in_process->description}}--}}
{{--                                                                </a>--}}
{{--                                                                {{$task_from_user_in_process->done_progress . '%'}}--}}
{{--                                                            </li>--}}
{{--                                                        @empty--}}
{{--                                                            <li class="list-group-item">Задач в процессе нет</li>--}}
{{--                                                        @endforelse--}}
{{--                                                    </ol>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                    @if(!empty($tasks['from_user']['expired']) && count($tasks['from_user']['expired']) > 0)--}}
{{--                                        <div class="accordion-item">--}}
{{--                                            <h2 class="accordion-header" id="flush-heading5">--}}
{{--                                                <button class="accordion-button collapsed" type="button"--}}
{{--                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapse5"--}}
{{--                                                        aria-expanded="false" aria-controls="flush-collapse5">--}}
{{--                                                    Просроченные<span--}}
{{--                                                        class="ms-2 badge text-bg-danger">{{count($tasks['from_user']['expired'])}}</span>--}}
{{--                                                </button>--}}
{{--                                            </h2>--}}
{{--                                            <div id="flush-collapse5" class="accordion-collapse collapse"--}}
{{--                                                 aria-labelledby="flush-heading5"--}}
{{--                                                 data-bs-parent="#accordionFlushExample">--}}
{{--                                                <div class="accordion-body">--}}
{{--                                                    <ol class="list-group list-group-numbered list-group-flush">--}}
{{--                                                        @forelse($tasks['from_user']['expired'] as $expired_task_from_user)--}}
{{--                                                            <li class="list-group-item"><a--}}
{{--                                                                    href="{{route('tasks.show', $expired_task_from_user->id)}}">--}}
{{--                                                                    {{$expired_task_from_user->description}}--}}
{{--                                                                </a>--}}
{{--                                                                {{$expired_task_from_user->done_progress . '%'}}--}}
{{--                                                            </li>--}}
{{--                                                        @empty--}}
{{--                                                            <li class="list-group-item">Просроченных задач нет</li>--}}
{{--                                                        @endforelse--}}
{{--                                                    </ol>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--            </div>--}}
{{--            <div class="row row-cols-1 row-cols-md-2">--}}
{{--                <div id="apps-to-your-division"></div>--}}
{{--                @if($operation_applications['show_from_user'] ?? false)--}}
{{--                    <div class="col">--}}
{{--                        <div class="card shadow">--}}
{{--                            <div class="card-header">--}}
{{--                                <span class="fs-4 me-2">Заявки подразделениям от Вас</span>--}}
{{--                                @if(auth()->user()->can('operation_application create') || auth()->user()->can('all'))--}}
{{--                                    <a href="{{route('operation_applications.create')}}"><img class="pb-1"--}}
{{--                                                                                              src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"--}}
{{--                                                                                              alt="Add" title="Добавить"--}}
{{--                                                                                              height="30"></a>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                            <div class="card-body">--}}
{{--                                <div class="accordion accordion-flush" id="accordionFlushExample876">--}}
{{--                                    @if(!empty($operation_applications['from_user']['new']) && count($operation_applications['from_user']['new']) > 0)--}}
{{--                                        <div class="accordion-item">--}}
{{--                                            <h2 class="accordion-header" id="flush-headingThree876">--}}
{{--                                                <button class="accordion-button collapsed" type="button"--}}
{{--                                                        data-bs-toggle="collapse"--}}
{{--                                                        data-bs-target="#flush-collapseThree876"--}}
{{--                                                        aria-expanded="false" aria-controls="flush-collapseThree876">--}}
{{--                                                    Новые<span--}}
{{--                                                        class="ms-2 badge text-bg-warning">{{count($operation_applications['from_user']['new'])}}</span>--}}
{{--                                                </button>--}}
{{--                                            </h2>--}}
{{--                                            <div id="flush-collapseThree876" class="accordion-collapse collapse"--}}
{{--                                                 aria-labelledby="flush-headingThree876"--}}
{{--                                                 data-bs-parent="#accordionFlushExample876">--}}
{{--                                                <div class="accordion-body">--}}
{{--                                                    <ol class="list-group list-group-numbered list-group-flush">--}}
{{--                                                        @forelse($operation_applications['from_user']['new'] as $new_operation_application)--}}
{{--                                                            <li class="list-group-item"><a--}}
{{--                                                                    href="{{route('operation_applications.show', $new_operation_application->id)}}">{{$new_operation_application->trouble_description}}</a>--}}
{{--                                                            </li>--}}
{{--                                                        @empty--}}
{{--                                                            <li class="list-group-item">Новых задач нет</li>--}}
{{--                                                        @endforelse--}}
{{--                                                    </ol>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                    @if(!empty($operation_applications['from_user']['in_process']) && count($operation_applications['from_user']['in_process']) > 0)--}}
{{--                                        <div class="accordion-item">--}}
{{--                                            <h2 class="accordion-header" id="flush-heading4345">--}}
{{--                                                <button class="accordion-button collapsed" type="button"--}}
{{--                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapse4345"--}}
{{--                                                        aria-expanded="false" aria-controls="flush-collapse4345">--}}
{{--                                                    Выполняются<span--}}
{{--                                                        class="ms-2 badge text-bg-success">{{count($operation_applications['from_user']['in_process'])}}</span>--}}
{{--                                                </button>--}}
{{--                                            </h2>--}}
{{--                                            <div id="flush-collapse4345" class="accordion-collapse collapse"--}}
{{--                                                 aria-labelledby="flush-heading4345"--}}
{{--                                                 data-bs-parent="#accordionFlushExample4345">--}}
{{--                                                <div class="accordion-body">--}}
{{--                                                    <ol class="list-group list-group-numbered list-group-flush">--}}
{{--                                                        @forelse($operation_applications['from_user']['in_process'] as $operation_application_in_process)--}}
{{--                                                            <li class="list-group-item"><a--}}
{{--                                                                    href="{{route('operation_applications.show', $operation_application_in_process->id)}}">--}}
{{--                                                                    {{$operation_application_in_process->trouble_description}}--}}
{{--                                                                </a>--}}
{{--                                                                {{$operation_application_in_process->done_percents . '%'}}--}}
{{--                                                            </li>--}}
{{--                                                        @empty--}}
{{--                                                            <li class="list-group-item">Задач в процессе нет</li>--}}
{{--                                                        @endforelse--}}
{{--                                                    </ol>--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    @endif--}}
{{--                                </div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                @endif--}}
{{--                <div id="repairs-section"></div>--}}
{{--                <div class="col">--}}
{{--                    <div class="card shadow">--}}
{{--                        <div class="card-header">--}}
{{--                            <span class="fs-4 me-2">Тех. мероприятия</span>--}}
{{--                            @if(auth()->user()->can('equipment_work_period create') || auth()->user()->can('all'))--}}
{{--                                <a href="{{route('equipment_work_periods.create')}}"><img class="pb-1"--}}
{{--                                                                                          src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"--}}
{{--                                                                                          alt="Add" title="Добавить"--}}
{{--                                                                                          height="30"></a>--}}
{{--                            @endif--}}
{{--                        </div>--}}
{{--                        <div class="card-body">--}}
{{--                            <div class="accordion accordion-flush" id="accordionFlushExample997">--}}
{{--                                <div id="works-this-month"></div>--}}
{{--                                <div id="works-next-month"></div>--}}
{{--                                <div id="works-expired"></div>--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--            @if(count($user_trks) < 1)--}}
{{--                <p>Для отображения технических мероприятий и ремонта на Вашем ТРК Ваших систем</p>--}}
{{--                <p><a href="{{route('settings.edit', Auth::id())}}">Настройки --> Редактировать настройки -->--}}
{{--                        Трк/Системы</a></p>--}}
{{--            @endif--}}
{{--        </div>--}}
{{--    </div>--}}
{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', function () {--}}
{{--            const sections = [--}}
{{--                {id: 'apps-to-your-division', url: '{{ route("dashboard.apps_to_your_division") }}'},--}}
{{--                {id: 'repairs-section', url: '{{ route("dashboard.repairs_section") }}'},--}}
{{--                {id: 'works-this-month', url: '{{route("dashboard.works_this_month")}}'},--}}
{{--                {id: 'works-next-month', url: '{{route("dashboard.works_next_month")}}'},--}}
{{--                {id: 'works-expired', url: '{{route("dashboard.works_expired")}}'},--}}
{{--            ];--}}

{{--            // Массив промисов загрузки секций--}}
{{--            const promises = sections.map(section =>--}}
{{--                fetch(section.url)--}}
{{--                    .then(response => response.text())--}}
{{--                    .then(html => {--}}
{{--                        const el = document.getElementById(section.id);--}}
{{--                        if (el) {--}}
{{--                            el.innerHTML = html;--}}
{{--                        }--}}
{{--                    })--}}
{{--            );--}}

{{--            // Ждём загрузки всех секций--}}
{{--            Promise.all(promises)--}}
{{--                .then(() => {--}}
{{--                    console.log('Все секции загружены');--}}
{{--                })--}}
{{--                .catch(error => {--}}
{{--                    console.error('Ошибка при загрузке секций:', error);--}}
{{--                });--}}
{{--        });--}}
{{--    </script>--}}
{{--@endsection--}}
