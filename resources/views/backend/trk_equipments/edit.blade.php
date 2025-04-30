@extends('layouts.backend.main')

@section('title', 'Главная | ТРК/Оборудование редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">ТРК/Оборудование редактирование</h4>
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
                            <form action="{{route('trk_equipments.update', $trk_equipment)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select disabled name="trk_id" class="form-select form-select-sm">
                                                @forelse($trks as $trk)
                                                    <option
                                                        {{$trk->id == $trk_equipment->trk_room->trk->id ? 'selected' : null}} value="{{$trk->id}}">{{$trk->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('trk_id')
                                            <div class="text-danger px-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="building_id" class="form-label form-label-sm">Блок/Зона <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="building_id" class="form-select form-select-sm" id="building_id">
                                                @forelse($buildings as $building)
                                                    <option
                                                        {{$building->id == $trk_equipment->trk_room->building->id ? 'selected' : null}} value="{{$building->id}}">{{$building->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('building_id')
                                            <div class="text-danger px-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="floor_id" class="form-label form-label-sm">Этаж <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="floor_id" class="form-select form-select-sm" id="floor_id">
                                                @forelse($floors as $floor)
                                                    <option
                                                        {{$floor->id == $trk_equipment->trk_room->floor->id ? 'selected' : null}} value="{{$floor->id}}">{{$floor->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('floor_id')
                                            <div class="text-danger px-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="room_id" class="form-label form-label-sm">Помещение <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select name="room_id" class="form-select form-select-sm" id="room_id">
                                                @forelse($rooms as $room)
                                                    <option
                                                        {{$room->id == $trk_equipment->trk_room->room->id ? 'selected' : null}} value="{{$room->id}}">{{$room->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('room_id')
                                            <div class="text-danger px-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="system_id" class="form-label form-label-sm">Система <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select name="system_id" class="form-select form-select-sm">
                                                @forelse($systems as $system)
                                                    <option
                                                        {{$system->id == $trk_equipment->system->id ? 'selected' : null}} value="{{$system->id}}">{{$system->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('system_id')
                                            <div class="text-danger px-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="equipment_status_id"
                                                   class="form-label form-label-sm">Статус</label>
                                            <select required name="equipment_status_id"
                                                    class="form-select form-select-sm">
                                                @forelse($equipment_statuses as $equipment_status)
                                                    <option
                                                        value="{{$equipment_status->id}}" {{$trk_equipment->equipment_status->id == $equipment_status->id ? 'selected' : null}}>{{$equipment_status->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('equipment_status_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="responsible_division_id"
                                                   class="form-label form-label-sm">Кто обслуживает <span class="text-danger"><b>*</b></span></label>
                                            <select required name="responsible_division_id"
                                                    class="form-select form-select-sm">
                                                @forelse($divisions as $division)
                                                    <option
                                                        value="{{$division->id}}" {{isset($trk_equipment->responsible_division->id) && $trk_equipment->responsible_division->id == $division->id ? 'selected' : null}}>{{$division->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('responsible_division_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="equipment_name_id" class="form-label form-label-sm">Название
                                                <span class="text-danger"><b>*</b></span></label>
                                            <select name="equipment_name_id" class="form-select form-select-sm">
                                                @forelse($equipment_names as $equipment_name)
                                                    <option
                                                        {{ isset($trk_equipment->equipment_name_id) && $equipment_name->id == $trk_equipment->equipment_name_id ? 'selected' : null}} value="{{$equipment_name->id}}">{{$equipment_name->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('equipment_name_id')
                                            <div class="text-danger px-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label for="axis_id" class="form-label form-label-sm">Оси</label>
                                            <select name="axis_id" class="form-select form-select-sm">
                                                <option value="">не выбрано ...</option>
                                                @forelse($axes as $axe)
                                                    <option
                                                        {{ isset($trk_equipment->axe) && $axe->id == $trk_equipment->axis_id ? 'selected' : null}} value="{{$axe->id}}">{{$axe->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('axis_id')
                                            <div class="text-danger px-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label for="comment" class="form-label form-label-sm">Комментарии</label>
                                            <input name="comment" class="form-control form-control-sm"
                                                   value="{{$trk_equipment->comment}}">
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('trk_equipments.show', $trk_equipment)}}"
                                           class="btn btn-sm btn-outline-success col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                alt="back" title="Назад"></a>
                                        <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                title="Сохранить"></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
