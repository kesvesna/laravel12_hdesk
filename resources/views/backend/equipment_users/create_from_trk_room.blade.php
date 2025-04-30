@extends('layouts.backend.main')

@section('title', 'Источники | Создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Источник</h4>
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
                            <form action="{{route('equipment_users.store_from_trk_room')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row row-cols-1 row-cols-md-4 mb-3">
                                        <div class="col mb-3">
                                            <label for="trk_room_id" class="form-label form-label-sm">ТРК
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input required readonly hidden name="trk_room_id"
                                                   value="{{$trk_room->id}}">
                                            <input readonly class="form-control form-control-sm"
                                                   value="{{$trk_room->trk->name}}">
                                            @error('trk_room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="building_id" class="form-label form-label-sm">Блок
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input readonly class="form-control form-control-sm"
                                                   value="{{$trk_room->building->name}}">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="floor_id" class="form-label form-label-sm">Этаж
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input readonly class="form-control form-control-sm"
                                                   value="{{$trk_room->floor->name}}">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="room_id" class="form-label form-label-sm">Помещение
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input readonly class="form-control form-control-sm"
                                                   value="{{$trk_room->room->name}}">
                                        </div>
                                    </div>
                                    <p>Оборудование, которое добавляется в качестве источника</p>
                                    <div class="row row-cols-1 row-cols-md-5">
                                        <div class="col mb-3">
                                            <label for="building_id" class="form-label form-label-sm">Блок<span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select name="building_id" class="form-select form-select-sm">
                                                @forelse($buildings as $building)
                                                    <option value="{{$building->id}}">{{$building->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('building_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="floor_id" class="form-label form-label-sm">Этаж<span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select name="floor_id" class="form-select form-select-sm">
                                                @forelse($floors as $floor)
                                                    <option value="{{$floor->id}}">{{$floor->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('floor_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="room_id" class="form-label form-label-sm">Помещение<span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select name="room_id" class="form-select form-select-sm">
                                                @forelse($rooms as $room)
                                                    <option value="{{$room->id}}">{{$room->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="system_id" class="form-label form-label-sm">Тип оборудования<span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select name="system_id" class="form-select form-select-sm">
                                                @forelse($systems as $system)
                                                    <option value="{{$system->id}}">{{$system->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('system_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="equipment_name_id" class="form-label form-label-sm">Название<span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select name="equipment_name_id" class="form-select form-select-sm">
                                                @forelse($equipment_names as $equipment_name)
                                                    <option value="{{$equipment_name->id}}">{{$equipment_name->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('equipment_name_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label for="comment" class="form-label form-label-sm">Комментарий</label>
                                            <input name="comment" type="text" class="form-control form-control-sm"
                                                   value="{{old('comment')}}" placeholder="Комментарий">
                                            @error('comment')
                                            <div class="text-danger px-3 pb-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('trk_room.show', $trk_room)}}"
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
    </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
    {{--        <script src="{{asset('assets/js/equipment_users/add_user.js')}}" defer></script>--}}
    {{--        <script src="{{asset('assets/js/equipment_users/delete_user.js')}}" defer></script>--}}
@endsection
