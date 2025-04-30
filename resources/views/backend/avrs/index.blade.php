@extends('layouts.backend.main')

@section('title', 'Главная | АВР')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">АВР</h4>
                        @if(auth()->user()->can('avr create'))
                            <a href="{{route('avrs.create')}}"><img
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
                                                data-bs-toggle="modal" data-bs-target="#avrs">Выгрузка актов
                                        </button>
                                    </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <form action="{{route('avrs.index')}}" method="get">
                                        @csrf
                                        <div class="row">
                                            <div class="col-12 col-md-6 mb-3">
                                                <input class="form-control form-control-sm" list="work_name_data_list"
                                                       type="search" id="work_name" placeholder="Поиск по типу работ ..."
                                                       onchange="this.form.submit();" name="work_name"
                                                       value="{{$old_filters['work_name'] ?? null}}">
                                                <datalist id="work_name_data_list">
                                                    @forelse($all_work_names as $work_name)
                                                        <option value="{{$work_name->name}}">
                                                    @empty
                                                        <option value="нет данных ...">
                                                    @endforelse
                                                </datalist>
                                            </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                <input class="form-control form-control-sm" list="executor_name_data_list"
                                                       type="search" id="executor_name" placeholder="Поиск по исполнителю ..."
                                                       onchange="this.form.submit();" name="executor_name"
                                                       value="{{$old_filters['executor_name'] ?? null}}">
                                                <datalist id="executor_name_data_list">
                                                    @forelse($all_executors as $executor)
                                                        <option value="{{$executor->name}}">
                                                    @empty
                                                        <option value="нет данных ...">
                                                    @endforelse
                                                </datalist>
                                            </div>
                                        </div>
                                            <div class="row">
                                                <div class="col-12 col-md-6 mb-3">
                                            <td class="d-none d-md-table-cell">
                                                <select name="city_id" class="form-select form-select-sm" id="city_id"
                                                        onchange="this.form.submit();">
                                                    <option value="">Все города</option>
                                                    @forelse($all_cities as $city)
                                                        <option
                                                            value="{{$city->id}}" {{isset($old_filters['city_id']) && $old_filters['city_id'] === $city->id ? 'selected' : null}}>{{$city->name}}</option>
                                                    @empty
                                                        <option value="">нет данных ...</option>
                                                    @endforelse
                                                </select>
                                            </td>
                                                </div>
                                                <div class="col-12 col-md-6 mb-3">
                                                    <td class="d-none d-md-table-cell">
                                                        <select name="division_id" class="form-select form-select-sm" id="division_id"
                                                                onchange="this.form.submit();">
                                                            <option value="">Все подразделения</option>
                                                            @forelse($all_divisions as $division)
                                                                <option
                                                                    value="{{$division->id}}" {{isset($old_filters['division_id']) && $old_filters['division_id'] === $division->id ? 'selected' : null}}>{{$division->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                </div>
                                        </div>
                                        <div class="table-responsive">
                                    <table class="table table-striped table-hover shadow table-bordered">
                                        <thead>
                                        <tr>
                                            <th>Дата</th>
                                            <th>ТРК</th>
                                            <th>Система</th>
                                            <th>Блок/Зона</th>
                                            <th>Этаж</th>
                                            <th>Помещение</th>
                                            <th>Оборудование</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                            <tr>
                                                <td>
                                                    <input class="form-control form-control-sm"
                                                           type="search" id="date" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="date"
                                                           value="{{$old_filters['date'] ?? null}}">
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
                                            </tr>
                                        </form>
                                        @forelse($avrs as $avr)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('avrs.show', $avr->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="text-nowrap">{{$avr->date}}</td>
                                                    <td class="text-nowrap">{{$avr->trk_room->trk->name}}</td>
                                                    <td class="text-nowrap">{{$avr->system->name}}</td>
                                                    <td class="text-nowrap">{{$avr->trk_room->building->name}}</td>
                                                    <td class="text-nowrap">{{$avr->trk_room->floor->name}}</td>
                                                    <td class="text-nowrap">{{$avr->trk_room->room->name}}</td>
                                                    <td>@forelse($avr->avr_equipments as $equipment)
                                                            {{$equipment->trk_equipment->equipment_name->name}}<br>
                                                        @empty

                                                        @endforelse

                                                    </td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                    {{$avrs->withQueryString()->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal for avrs -->
        <div class="modal fade" id="avrs" tabindex="-1"
             aria-labelledby="avrs" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel5">Выгрузка актов</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('avrs.export')}}" method="post">
                            @csrf
                            @method('post')
                            <div class="row row-cols-1 row-cols-md-2">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="start_date">Начало
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <input required class="form-control form-control-sm" type="date" id="start_date" name="start_date"
                                           value="{{date('Y-m-d')}}"
                                           min="2011-01-01" max="2040-12-31">
                                </div>
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="start_date">Конец
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <input required class="form-control form-control-sm" type="date" id="finish_date" name="finish_date"
                                           value="{{date('Y-m-d')}}"
                                           min="2011-01-01" max="2040-12-31">
                                </div>
                            </div>
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
                                    <label class="form-label form-label-sm" for="division_id_2">Подразделение
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <select name="division_id"
                                            class="form-select form-select-sm" id="division_id_2">
                                            <option value="">Все</option>
                                        @forelse($all_divisions as $division)
                                            <option
                                                value="{{$division->id}}" {{isset($old_filters['division_id']) && $old_filters['division_id'] == $division->id ? 'selected' : null}}>{{$division->name}}</option>
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
                                    <label class="form-label form-label-sm" for="floor_id_2">Этажи</label>
                                    <select name="floor_ids[]" multiple size="10"
                                            class="form-select form-select-sm" id="floor_id_2">
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
                                    <label class="form-label form-label-sm" for="room_id_2">Помещения</label>
                                    <select name="room_ids[]" multiple size="10"
                                            class="form-select form-select-sm" id="room_id_2">
                                        @forelse($all_rooms as $room)
                                            <option
                                                value="{{$room->id}}" {{isset($old_filters['room_id']) && $old_filters['room_id'] == $room->id ? 'selected' : null}}>{{$room->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="equipment_name_id_2">Оборудование</label>
                                    <select name="equipment_name_ids[]" multiple size="10"
                                            class="form-select form-select-sm" id="equipment_name_id_2">
                                        <option value="">Все оборудование</option>
                                        @forelse($all_equipment_names as $equipment_name)
                                            <option
                                                value="{{$equipment_name->id}}" {{isset($old_filters['equipment_name']) && $old_filters['equipment_name'] == $equipment_name->name ? 'selected' : null}}>{{$equipment_name->name}}</option>
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
                                        <option value=".html">HTML</option>
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
