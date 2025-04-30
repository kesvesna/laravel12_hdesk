@extends('layouts.backend.main')

@section('title', 'Потребители | Создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Потребители новые</h4>
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
                            <form action="{{route('equipment_users.store')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="equipment-add-div p-2 my-2 rounded"
                                         style="background-color: rgba(145, 135, 255, 0.2)">
                                        <label class="form-label form-label-sm mt-2">Оборудование источник</label>
                                    <div class="row row-cols-1 row-cols-md-5">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК
                                                <span class="text-danger"><b>*</b></span></label>
                                            <select name="trk_id" class="form-select form-select-sm">
                                                @forelse($trks as $trk)
                                                    <option value="{{$trk->id}}" {{old('trk_id') == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="building_id" class="form-label form-label-sm">Блок/Зона
                                                <span class="text-danger"><b>*</b></span></label>
                                            <select name="building_id" class="form-select form-select-sm">
                                                @forelse($buildings as $building)
                                                    <option value="{{$building->id}}" {{old('building_id') == $building->id ? 'selected' : null}}>{{$building->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('building_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="floor_id" class="form-label form-label-sm">Этаж
                                                <span class="text-danger"><b>*</b></span></label>
                                            <select name="floor_id" class="form-select form-select-sm">
                                                @forelse($floors as $floor)
                                                    <option value="{{$floor->id}}" {{old('floor_id') == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('floor_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="room_id" class="form-label form-label-sm">Помещение
                                                <span class="text-danger"><b>*</b></span></label>
                                            <select name="room_id" class="form-select form-select-sm">
                                                @forelse($rooms as $room)
                                                    <option value="{{$room->id}}" {{old('room_id') == $room->id ? 'selected' : null}}>{{$room->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <!-- todo возможность добавлять много потребителей с выбором их типа (помещение, оборудование) -->
                                        <!-- два типа потребителей: Помещение и Оборудование (для электрощита например другое оборудование является потребителем) -->

                                        <div class="col mb-4">
                                            <label for="equipment_name_id" class="form-label form-label-sm">Оборудование
                                                <span class="text-danger"><b>*</b></span></label>
                                            <select required name="equipment_name_id" class="form-select form-select-sm">
                                                @forelse($equipment_names as $equipment_name)
                                                    <option value="{{$equipment_name->id}}" {{old('equipment_name_id') == $equipment_name->id ? 'selected' : null}}>{{$equipment_name->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('equipment_name_id')
                                            <div class="text-danger px-3 pb-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    </div>
                                    <div class="equipment-users-add-parent-div p-2 mt-3 rounded"
                                         style="background-color: rgba(218, 117, 255, 0.2)">
                                        <label for="basic-url" class="form-label form-label-sm my-2">Помещения потребители</label>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-sm" id="equipment_table">
                                                <thead>
                                                <tr>
                                                    <th>Блок/Зона</th>
                                                    <th>Этаж</th>
                                                    <th>Помещение</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr class="equipment-user-add-tr">
                                                    <td style="width: 13rem;">
                                                        <div class="input-group input-group-sm equipment-add-div" style="width: 13rem;">
                                                        <span class="input-group-text equipment-user-add-button">
                                                            <img src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="add" title="Добавить" height="20">
                                                        </span>
                                                            <span class="input-group-text equipment-user-delete-button">
                                                            <img src="{{asset('assets/images/backend/svg/trash3.svg')}}" alt="delete" title="Удалить" height="20">
                                                        </span>
                                                            <select required name="equipments[0][building]" class="form-select form-select-sm equipment-name-input">
                                                                @forelse($buildings as $building)
                                                                    <option value="{{$building->id}}">{{$building->name}}</option>
                                                                @empty
                                                                    <option value="">нет данных ...</option>
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                        @error('equipments.*')
                                                        <div class="text-danger"
                                                             style="margin-top: -1rem !important;">{{$message}}</div>
                                                        @enderror
                                                    </td>
                                                    <td style="width: 13rem;">
                                                        <select style="width:13rem;" name="equipments[0][floor]" class="form-select form-select-sm balk_size_type">
                                                            @forelse($floors as $floor)
                                                                <option value="{{$floor->id}}">{{$floor->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                    <td style="width: 100%;">
                                                        <select style="width:13rem;" name="equipments[0][room]" class="form-select form-select-sm air_speed">
                                                            @forelse($rooms as $room)
                                                                <option value="{{$room->id}}">{{$room->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm mt-3">
                                        <a href="{{route('equipment_users.index')}}"
                                           class="btn btn-sm btn-outline-success col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                                title="Назад"></a>
                                        <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                title="Сохранить"></button>
                                    </div>
                                </div>
                        </form>
            </div>

        </div>
    </div>
    <!-- profile init js -->
    <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
        <script src="{{asset('assets/js/equipment_users/add_user.js')}}" defer></script>
        <script src="{{asset('assets/js/equipment_users/delete_user.js')}}" defer></script>
@endsection
