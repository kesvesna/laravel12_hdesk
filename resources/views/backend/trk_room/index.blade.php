@extends('layouts.backend.main')

@section('title', 'Главная | ТРК/Помещения')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">ТРК/Помещения</h4>
                        @if(auth()->user()->can('trk_room create') || Auth::user()->hasRole('sadmin'))
                            <a href="{{route('trk_room.create')}}"><img
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
                                            <th>ТРК</th>
                                            <th>Блок/Зона</th>
                                            <th>Этаж/Отметка</th>
                                            <th>Тип</th>
                                            <th>Помещение</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('trk_room.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td>
                                                    <select class="form-select form-select-sm" name="trk_id" id="trk_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_trks as $trk)
                                                            <option
                                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="building_id" id="building_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_buildings as $building)
                                                            <option
                                                                value="{{$building->id}}" {{isset($old_filters['building_id']) && $old_filters['building_id'] == $building->id ? 'selected' : null}}>{{$building->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-select form-select-sm" name="floor_id" id="floor_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_floors as $floor)
                                                            <option
                                                                value="{{$floor->id}}" {{isset($old_filters['floor_id']) && $old_filters['floor_id'] == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <select class="form-control form-control-sm" list="room_purpose_data_list"
                                                           type="search" id="room_purpose_id"
                                                           onchange="this.form.submit();" name="room_purpose_id">
                                                        <option value="">Все</option>
                                                        @foreach($all_room_purposes as $room_purpose)
                                                            <option value="{{$room_purpose->id}}" {{isset($old_filters['room_purpose_id']) && $old_filters['room_purpose_id'] == $room_purpose->id ? 'selected' : null}}>{{$room_purpose->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" list="room_data_list"
                                                           type="search" id="room_id" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="room_id"
                                                           value="{{$old_filters['room_id'] ?? null}}">
                                                    <datalist id="room_data_list">
                                                        @forelse($all_rooms as $room)
                                                            <option value="{{$room->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                            </tr>
                                        </form>
                                        @forelse($trk_rooms as $trk_room)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('trk_room.show', $trk_room->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="text-nowrap">{{$trk_room->trk->name}}</td>
                                                    <td class="text-nowrap">{{$trk_room->building->name}}</td>
                                                    <td class="text-nowrap">{{$trk_room->floor->name}}</td>
                                                    <td class="text-nowrap">{{$trk_room->room_purpose->name ?? 'неизвестно'}}</td>
                                                    <td class="text-nowrap">{{$trk_room->room->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    </div>
                                    {{$trk_rooms->withQueryString()->links()}}
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
