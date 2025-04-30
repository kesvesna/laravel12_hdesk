@extends('layouts.backend.main')

@section('title', 'Главная | Потребители оборудования')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Потребители оборудования</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('equipment_users.create')}}"><img
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
                                            <th>Оборудование источник</th>
                                            <th>Помещение потребитель</th>
                                            <th>ТРК</th>
                                            <th>Блок/Зона</th>
                                            <th>Этаж</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        @if(auth()->user()->can('read'))
                                            <form action="{{route('equipment_users.index')}}" method="get">
                                                @csrf
                                                <tr>
                                                    <td>
                                                        <input class="form-control form-control-sm"
                                                               list="equipment_names_data_list" type="search"
                                                               id="spare_part_model" placeholder="Поиск ..."
                                                               onchange="this.form.submit();" name="equipment_name"
                                                               value="{{$old_filters['equipment_name'] ?? null}}">
                                                        <datalist id="equipment_names_data_list">
                                                            @forelse($equipment_names as $equipment_name)
                                                                <option value="{{$equipment_name->name}}">
                                                            @empty
                                                                <option value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm"
                                                               list="room_names_data_list" type="search"
                                                               id="spare_part_model" placeholder="Поиск ..."
                                                               onchange="this.form.submit();" name="room_name"
                                                               value="{{$old_filters['room_name'] ?? null}}">
                                                        <datalist id="room_names_data_list">
                                                            @forelse($room_names as $room_name)
                                                                <option value="{{$room_name->name}}">
                                                            @empty
                                                                <option value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm" id="trk_id"
                                                                onchange="this.form.submit();" name="trk_id">
                                                            <option value="">Все</option>
                                                            @forelse($trks as $trk)
                                                                <option
                                                                    value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm" id="building_id"
                                                                onchange="this.form.submit();" name="building_id">
                                                            <option value="">Все</option>
                                                            @forelse($buildings as $building)
                                                                <option
                                                                    value="{{$building->id}}" {{isset($old_filters['building_id']) && $old_filters['building_id'] == $building->id ? 'selected' : null}}>{{$building->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm" id="floor_id"
                                                                onchange="this.form.submit();" name="floor_id">
                                                            <option value="">Все</option>
                                                            @forelse($floors as $floor)
                                                                <option
                                                                    value="{{$floor->id}}" {{isset($old_filters['floor_id']) && $old_filters['floor_id'] == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                </tr>
                                            </form>
                                            @forelse($equipment_users as $equipment_user)
                                                <tr onclick="window.location='{{ route('equipment_users.show', $equipment_user->id) }}';">
                                                    <td>{{$equipment_user->trk_equipment->equipment_name->name}}</td>
                                                    <td>{{$equipment_user->trk_room->room->name}}</td>
                                                    <td>{{$equipment_user->trk_room->trk->name}}</td>
                                                    <td>{{$equipment_user->trk_room->building->name}}</td>
                                                    <td>{{$equipment_user->trk_room->floor->name}}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">нет данных ...</td>
                                                </tr>
                                            @endforelse
                                        @endif
                                        </tbody>
                                    </table>
                                    {{$equipment_users->withQueryString()->links()}}
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
