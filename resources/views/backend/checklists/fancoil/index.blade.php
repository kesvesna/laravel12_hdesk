@extends('layouts.backend.main')

@section('title', 'Главная | Чеклист фанкойла')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Чеклист фанкойла</h4>
                        @if(auth()->user()->can('checklist_fancoil create'))
                            <a href="{{route('checklists_fancoil.create')}}"><img
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
                                    <table class="table table-striped table-hover shadow table-bordered">
                                        <thead>
                                        <tr>
                                            <th class="d-none d-md-table-cell">Дата</th>
                                            <th class="d-none d-md-table-cell">ТРК</th>
                                            <th>Помещение</th>
                                            <th>Оборудование</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('checklists_fancoil.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td class="d-none d-md-table-cell">
                                                    <input class="form-control form-control-sm"
                                                           type="search" id="date" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="date"
                                                           value="{{$old_filters['date'] ?? null}}">
                                                </td>
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
                                                <td>
                                                    <input class="form-control form-control-sm"
                                                           list="equipment_name_data_list" type="search"
                                                           id="equipment_name_id" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="equipment_name_id"
                                                           value="{{$old_filters['equipment_name_id'] ?? null}}">
                                                    <datalist id="equipment_name_data_list">
                                                        @forelse($all_equipment_names as $equipment)
                                                            <option value="{{$equipment->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                            </tr>
                                        </form>
                                        @forelse($checklists as $checklists_fancoil)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('checklists_fancoil.show', $checklists_fancoil) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="d-none d-md-table-cell">{{$checklists_fancoil->created_at}}</td>
                                                    <td class="d-none d-md-table-cell">{{$checklists_fancoil->trk_room->trk->name}}</td>
                                                    <td>{{$checklists_fancoil->trk_room->room->name}}</td>
                                                    <td>{{$checklists_fancoil->trk_equipment->equipment_name->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$checklists->withQueryString()->links()}}
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
