@extends('layouts.backend.main')

@section('title', 'Главная | ТРК/Оборудование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">ТРК/Оборудование</h4>
                        @if(auth()->user()->can('equipment create') || auth()->user()->hasRole('sadmin'))
                            <a href="{{route('trk_equipments.create')}}"><img
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
                                <div class="card-title ps-3 pt-3">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#equipments">Выгрузка оборудования
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-hover shadow table-bordered">
                                        <thead>
                                        <tr>
                                            <th>ТРК</th>
                                            <th>Система</th>
                                            <th>Блок/Зона</th>
                                            <th>Этаж</th>
                                            <th>Помещение</th>
                                            <th>Оборудование</th>
                                            <th>Оси</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('trk_equipments.index')}}" method="get">
                                            @csrf
                                            <tr>
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
                                                    <select name="system_id" class="form-select form-select-sm"
                                                            id="system_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_systems as $system)
                                                            <option
                                                                value="{{$system->id}}" {{isset($old_filters['system_id']) && $old_filters['system_id'] === $system->id ? 'selected' : null}}>{{$system->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="building_id" class="form-select form-select-sm"
                                                            id="building_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_buildings as $building)
                                                            <option
                                                                value="{{$building->id}}" {{isset($old_filters['building_id']) && $old_filters['building_id'] === $building->id ? 'selected' : null}}>{{$building->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="floor_id" class="form-select form-select-sm"
                                                            id="floor_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_floors as $floor)
                                                            <option
                                                                value="{{$floor->id}}" {{isset($old_filters['floor_id']) && $old_filters['floor_id'] === $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
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
                                                <td></td>
                                            </tr>
                                        </form>
                                        @forelse($trk_equipments as $trk_equipment)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('trk_equipments.show', $trk_equipment->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="text-nowrap">{{$trk_equipment->trk_room->trk->name}}</td>
                                                    <td class="text-nowrap">{{$trk_equipment->system->name}}</td>
                                                    <td class="text-nowrap">{{$trk_equipment->trk_room->building->name}}</td>
                                                    <td class="text-nowrap">{{$trk_equipment->trk_room->floor->name}}</td>
                                                    <td class="text-nowrap">{{$trk_equipment->trk_room->room->name}}</td>
                                                    <td class="text-nowrap">{{$trk_equipment->equipment_name->name}}</td>
                                                    <td class="text-nowrap">{{$trk_equipment->axe->name ?? ''}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    </div>
                                    {{$trk_equipments->withQueryString()->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal for equipments -->
        <div class="modal fade" id="equipments" tabindex="-1"
             aria-labelledby="equipments" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel5">Выгрузка оборудования</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('equipments.export')}}" method="post">
                            @csrf
                            @method('post')
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="trk_id_2">Трк
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <select required name="trk_id"
                                            class="form-select form-select-sm" id="trk_id_2">
                                        @forelse($all_trks as $trk)
                                            <option
                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="system_id_2">Тип оборудования
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <select name="system_id"
                                            class="form-select form-select-sm" id="system_id_2">
                                        <option value="">Все</option>
                                        @forelse($all_systems as $system)
                                            <option
                                                value="{{$system->id}}" {{isset($old_filters['system_id']) && $old_filters['system_id'] == $system->id ? 'selected' : null}}>{{$system->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="building_id_2">Блок/Зона
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <select name="building_id"
                                            class="form-select form-select-sm" id="building_id_2">
                                        <option value="">Все</option>
                                        @forelse($all_buildings as $building)
                                            <option
                                                value="{{$building->id}}" {{isset($old_filters['building_id']) && $old_filters['building_id'] == $building->id ? 'selected' : null}}>{{$building->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="floor_id">Этаж</label>
                                    <select name="floor_id"
                                            class="form-select form-select-sm" id="floor_id">
                                        <option value="">Все</option>
                                        @forelse($all_floors as $floor)
                                            <option
                                                value="{{$floor->id}}" {{isset($old_filters['floor_id']) && $old_filters['floor_id'] == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="room_id">Помещение</label>
                                    <select name="room_id"
                                            class="form-select form-select-sm" id="room_id">
                                        <option value="">Все</option>
                                        @forelse($all_rooms as $room)
                                            <option
                                                value="{{$room->name}}" {{isset($old_filters['room_id']) && $old_filters['room_id'] == $room->id ? 'selected' : null}}>{{$room->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="file_type">Тип файла
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <select required name="file_type"
                                            class="form-select form-select-sm" id="file_type">
                                        <option value=".pdf">PDF</option>
                                        <option value=".xslx">EXCEL XSLX</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Выгрузить
                            </button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
