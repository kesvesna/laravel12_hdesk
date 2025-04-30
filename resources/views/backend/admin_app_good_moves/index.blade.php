@extends('layouts.backend.main')

@section('title', 'Главная | Заявки на ввоз/вывоз')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Заявки на ввоз/вывоз</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('admin_app_good_moves.create')}}"><img
                                    src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                    title="Добавить" height="30"></a>
                        @endif
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
                                    <table class="table table-striped table-hover shadow">
                                        <thead>
                                            <tr>
                                                <td class="d-none d-md-table-cell">ТРК</td>
                                                <td>Помещение</td>
                                                <td class="d-none d-md-table-cell">Статус</td>
                                                <td>Торговая марка</td>
                                            </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                                <form action="{{route('admin_app_good_moves.index')}}" method="get">
                                                    @csrf
                                                    <tr>
                                                        <td class="d-none d-md-table-cell">
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
                                                            <input class="form-control form-control-sm" list="room_data_list"
                                                                   type="search" id="room_name" placeholder="Поиск ..."
                                                                   onchange="this.form.submit();" name="room_name"
                                                                   value="{{$old_filters['room_name'] ?? null}}">
                                                            <datalist id="room_data_list">
                                                                @forelse($all_rooms as $room)
                                                                    <option value="{{$room->name}}">
                                                                @empty
                                                                    <option value="нет данных ...">
                                                                @endforelse
                                                            </datalist>
                                                        </td>
                                                        <td class="d-none d-md-table-cell">
                                                            <select name="admin_app_status_id" class="form-select form-select-sm" id="admin_app_status_id"
                                                                    onchange="this.form.submit();">
                                                                <option value="">Все</option>
                                                                @forelse($all_statuses as $status)
                                                                    <option
                                                                        value="{{$status->id}}" {{isset($old_filters['admin_app_status_id']) && $old_filters['admin_app_status_id'] === $status->id ? 'selected' : null}}>{{$status->name}}</option>
                                                                @empty
                                                                    <option value="">нет данных ...</option>
                                                                @endforelse
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input class="form-control form-control-sm"
                                                                   list="brand_name_data_list" type="search"
                                                                   id="brand_name" placeholder="Поиск ..."
                                                                   onchange="this.form.submit();" name="brand_name"
                                                                   value="{{$old_filters['brand_name'] ?? null}}">
                                                            <datalist id="brand_name_data_list">
                                                                @forelse($all_brand_names as $brand_name)
                                                                    <option value="{{$brand_name->name}}">
                                                                @empty
                                                                    <option value="нет данных ...">
                                                                @endforelse
                                                            </datalist>
                                                        </td>
                                                    </tr>
                                                </form>
                                        @forelse($admin_app_good_moves as $admin_app_good_move)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('admin_app_good_moves.show', $admin_app_good_move->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="d-none d-md-table-cell">{{$admin_app_good_move->trk_room->trk->name}}</td>
                                                    <td class="d-none d-md-table-cell">{{$admin_app_good_move->trk_room->room->name}}</td>
                                                    <td>{{$admin_app_good_move->admin_app_status->name}}</td>
                                                    <td>{{$admin_app_good_move->brand->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td>нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$admin_app_good_moves->withQueryString()->links()}}
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
