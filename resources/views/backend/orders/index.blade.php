@extends('layouts.backend.main')

@section('title', 'Главная | Заказы')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Заказы</h4>
                        @if(auth()->user()->can('order_spare_part create'))
                            <a href="{{route('orders.create')}}"><img
                                    src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                    title="Добавить" height="30"></a>
                        @endif()
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
                                    <div class="table-responsive">
                                    <table class="table table-striped table-hover shadow table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Счет №</th>
                                            <th>ТРК</th>
                                            <th>Срочность</th>
                                            <th>Статус</th>
                                            <th>Запчасти</th>
                                            <th>Комментарий</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('orders.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td>
                                                    <input class="form-control form-control-sm" list="account_number_data_list"
                                                           type="search" id="account_number" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="account_number"
                                                           value="{{$old_filters['account_number'] ?? null}}">
                                                    <datalist id="account_number_data_list">
                                                        @forelse($all_account_numbers as $account_number)
                                                            <option value="{{$account_number}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                                <td>
                                                    <select name="trk_id" class="form-select form-select-sm" id="trk_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_trks as $trk)
                                                            <option
                                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="is_urgency" class="form-select form-select-sm"
                                                            id="is_urgency" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        <option value="0" {{isset($old_filters['is_urgency']) && $old_filters['is_urgency'] == '0' ? 'selected' : null}}>не срочно</option>
                                                        <option value="1" {{isset($old_filters['is_urgency']) && $old_filters['is_urgency'] == '1' ? 'selected' : null}}>срочно</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="status_id" class="form-select form-select-sm" id="trk_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_statuses as $status)
                                                            <option
                                                                value="{{$status->id}}" {{isset($old_filters['status_id']) && $old_filters['status_id'] === $status->id ? 'selected' : null}}>{{$status->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" list="spare_part_name_data_list"
                                                           type="search" id="spare_part_name" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="spare_part_name"
                                                           value="{{$old_filters['spare_part_name'] ?? null}}">
                                                    <datalist id="spare_part_name_data_list">
                                                        @forelse($spare_part_names as $spare_part_name)
                                                            <option value="{{$spare_part_name->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" list="comment_data_list"
                                                           type="search" id="comment_data_list" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="comment"
                                                           value="{{$old_filters['comment'] ?? null}}">
                                                    <datalist id="comment_data_list">
                                                        @forelse($comments as $comment)
                                                            <option value="{{$comment}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                            </tr>
                                        </form>
                                        @forelse($orders as $order)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('orders.show', $order->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="text-nowrap">{{$order->account_number}}</td>
                                                    <td class="text-nowrap">{{$order->trk->name}}</td>
                                                    <td class="text-nowrap">{{$order->is_urgency ? 'срочно' : 'не срочно'}}</td>
                                                    <td class="text-nowrap">{{$order->status->name}}</td>
                                                    <td class="text-nowrap">
                                                    @forelse($order->spare_parts as $order_spare_part)
                                                        {{$order_spare_part->spare_part_name->name}}<br>
                                                    @empty
                                                        нет данных...
                                                    @endforelse
                                                    </td>
                                                    <td>{{$order->comment}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    </div>
                                    {{$orders->withQueryString()->links()}}
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
