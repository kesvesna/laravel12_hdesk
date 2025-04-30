@extends('layouts.backend.main')

@section('title', 'Просмотр | Ремонт')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Ремонт</h4>
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
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <ul class="list-group mb-3">
                                    @if(!empty($trk_repair->operation_app_id))
                                        <li class="list-group-item"><b>По заявке: </b><a href="{{route('operation_applications.show', $trk_repair->operation_app_id)}}">{{$trk_repair->operation_application->created_at}}</a>
                                    </li>
                                    @endif
                                        <li class="list-group-item"><b>ТРК: </b>{{$trk_repair->trk_room->trk->name}}
                                        <li class="list-group-item"><b>Блок: </b>{{$trk_repair->trk_room->building->name}}
                                        <li class="list-group-item"><b>Этаж: </b>{{$trk_repair->trk_room->floor->name}}
                                        <li class="list-group-item"><b>Помещение: </b>{{$trk_repair->trk_room->room->name}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Оборудование: </b><a href="{{route('trk_equipments.show', $trk_repair->trk_equipment->id)}}">{{$trk_repair->trk_equipment->equipment_name->name}}</a></li>
                                    <li class="list-group-item">
                                        <b>Задача: </b>{{$trk_repair->description ?? 'не выбрано'}}</li>
                                    <li class="list-group-item"><b>Выполнить
                                            до: </b>{{$trk_repair->deadline_at ?? 'не выбрано'}}</li>
                                    <li class="list-group-item">
                                        <b>Сделано: </b>{{$trk_repair->executed_result ?? 'пока ничего'}}</li>
                                    <li class="list-group-item"><b>Готовность: </b>{{$trk_repair->done_progress . '%'}}
                                    </li>
                                    <li class="list-group-item"><b>Кто выполнил: </b>
                                        <ul class="list-group my-2">
                                            @forelse($trk_repair->executors as $executor)
                                                <li class="list-group-item">{{$executor->executor_name->name}}</li>
                                            @empty
                                                <span>нет данных ...</span>
                                            @endforelse
                                        </ul>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Выполнен: </b>{{$trk_repair->executed_at ?? 'отсутствует'}}</li>
                                        @if(count($trk_repair->avrs) > 0)
                                        <li class="list-group-item">
                                            @forelse($trk_repair->avrs as $avr)
                                                <b>Акты: </b><br>
                                                <a href="{{route('avrs.show', $avr->id)}}">{{$avr->date ?? 'отсутствует'}}</a>
                                                <br>
                                            @empty
                                                нет данных ...
                                            @endforelse
                                        </li>
                                        @endif
                                </ul>
                                @if(count($trk_repair->orders) > 0)
                                    <ul class="list-group mb-4">
                                        <li class="list-group-item">
                                            <b>Заказы запчастей: </b></li>
                                        @forelse($trk_repair->orders as $order)
                                            <li class="list-group-item">
                                                <a href="{{route('orders.show', $order->id)}}">
                                                    {{$order->created_at}}
                                                </a></li>
                                        @empty
                                            <li class="list-group-item">
                                                нет данных ...</li>
                                        @endforelse
                                    </ul>
                                @endif
                                @if(count($trk_repair->tech_acts) > 0)
                                    <ul class="list-group mb-4">
                                        <li class="list-group-item">
                                            <b>Технические акты: </b></li>
                                        @forelse($trk_repair->tech_acts as $tech_act)
                                            <li class="list-group-item">
                                                <a href="{{route('tech_acts.show', $tech_act->id)}}">
                                                    {{$tech_act->created_at}}
                                                </a></li>
                                        @empty
                                            <li class="list-group-item">
                                                нет данных ...</li>
                                        @endforelse
                                    </ul>
                                @endif
                                <ul class="list-group mb-4">
                                    <li class="list-group-item">
                                        <b>Создан: </b>{{$trk_repair->created_at}}{{ ', ' . $trk_repair->author->name}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Исправлен: </b>{{$trk_repair->updated_at}}{{ ', ' . $trk_repair->last_editor->name}}
                                    </li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back()"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if($trk_repair->author->id == auth()->id()
                                            || auth()->user()->can('repair update')
                                            || auth()->user()->can('all'))
                                        <a href="{{route('trk_repairs.done_progress', $trk_repair)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/check2-all.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if($trk_repair->author->id == auth()->id()
                                             || auth()->user()->can('repair update')
                                             || auth()->user()->can('all'))
                                        <a href="{{route('trk_repairs.edit', $trk_repair)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('trk_repairs.destroy', $trk_repair)}}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-outline-danger"><img
                                                    src="{{asset('assets/images/backend/svg/trash3.svg')}}" alt="delete"
                                                    title="Удалить"></button>
                                        </form>
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
