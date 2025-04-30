@extends('layouts.backend.main')

@section('title', 'Главная | Заказ')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Заказ</h4><a href="{{route('orders.create')}}"><img
                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add" title="Добавить"
                                height="30"></a>
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
                            <div class="card shadow p-3">
                                <ul class="list-group mb-4">
                                    <li class="list-group-item"><b>Дата: </b>{{$order->created_at ?? 'не выбрано'}}</li>
                                    <li class="list-group-item">
                                        <b>ТРК: </b>{{$order->trk->name ?? 'не выбрано'}}</li>
                                    <li class="list-group-item"><b>Система: </b>{{$order->system->name ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Помещение: </b>{{$order->room->name ?? 'не выбрано'}}</li>
                                    <li class="list-group-item">
                                        <b>Оборудование: </b>{{$order->equipment->name ?? 'не выбрано'}}</li>
                                    <li class="list-group-item">
                                        <b>Статус заказа: </b>{{$order->order_status->name ?? 'не выбрано'}}</li>
                                    <li class="list-group-item">
                                        <b>Срочность: </b>{{$order->is_urgency ? 'срочно' : 'не срочно'}}</li>
                                    <li class="list-group-item">
                                        <b>Комментарий: </b>{{$order->comment ?? 'отсутствует'}}</li>
                                    <li class="list-group-item">
                                        <b>Создал: </b>{{$order->author->name}}</li>
                                    <li class="list-group-item">
                                        <b>Номер счета: </b>{{$order->account_number}}</li>
                                    <li class="list-group-item">
                                        <b>Поставщик: </b>{{$order->provider}}</li>
                                    <li class="list-group-item">
                                        <b>Дата поставки: </b>{{$order->delivery_at}}</li>
                                    <li class="list-group-item">
                                        <b>Дата закрытия: </b>{{$order->closed_at}}</li>
                                </ul>
                                @if(count($order->tasks) > 0)
                                <ul class="list-group mb-4">
                                    <li class="list-group-item">
                                        <b>Создан через задачу: </b></li>
                                    @forelse($order->tasks as $task)
                                        <li class="list-group-item">
                                            <a href="{{route('tasks.show', $task->id)}}">
                                                {{$task->created_at}}
                                            </a></li>
                                    @empty
                                        <li class="list-group-item">
                                            нет данных ...</li>
                                    @endforelse
                                </ul>
                                @endif
                                <label class="form-label form-label-sm">Запчасти</label>
                                <ul class="list-group mb-4">
                                @forelse($order->spare_parts as $spare_part)
                                        <li class="list-group-item">{{$spare_part->spare_part_name->name . ': '}}{{$spare_part->model . ', кол-во: '}}{{$spare_part->value}}</li>
                                @empty
                                    <span>нет данных ...</span>
                                @endforelse
                                </ul>
                                @if(count($order->repairs) > 0)
                                    <ul class="list-group mb-4">
                                        <li class="list-group-item">
                                            <b>Для ремонта: </b></li>
                                        @forelse($order->repairs as $repair)
                                            <li class="list-group-item">
                                                <a href="{{route('trk_repairs.show', $repair->id)}}">
                                                    {{$repair->created_at}}
                                                </a></li>
                                        @empty
                                            <li class="list-group-item">
                                                нет данных ...</li>
                                        @endforelse
                                    </ul>
                                @endif
                                <div class="btn-group btn-group-sm col-12 col-md-6 mt-3" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back();"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('order_spare_part update'))
                                        <a href="{{route('orders.edit', $order)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('orders.destroy', $order)}}" method="post">
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
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
