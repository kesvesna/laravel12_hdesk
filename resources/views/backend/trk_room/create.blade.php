@extends('layouts.backend.main')

@section('title', 'Главная | ТРК/Помещение создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">ТРК/Помещение создание</h4>
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
                            <form action="{{route('trk_room.store')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="col mb-3">
                                        <!-- Button trigger modal responsibility -->
                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal2">Как создать?
                                        </button>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-3">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК <span class="text-danger"><b>*</b></span></label>
                                            <select name="trk_id" class="form-select form-select-sm" autofocus>
                                                @forelse($trks as $trk)
                                                    <option value="{{$trk->id}}">{{$trk->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="building_id" class="form-label form-label-sm">Блок/Зона <span class="text-danger"><b>*</b></span></label>
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
                                        <div class="col mb-4">
                                            <label for="floor_id" class="form-label form-label-sm">Этаж/Отметка <span class="text-danger"><b>*</b></span></label>
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
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <input class="form-check-input me-2" type="checkbox" value="1"
                                                   name="need_daily_checking">
                                            <label class="form-label form-label-sm" for="need_daily_checking">Нужен
                                                ежедневный обход</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label for="room_purpose_id"
                                                   class="form-label form-label-sm">Назначение <span class="text-danger"><b>*</b></span></label>
                                            <select name="room_purpose_id" class="form-select form-select-sm">
                                                @forelse($room_purposes as $room_purpose)
                                                    <option
                                                        value="{{$room_purpose->id}}">{{$room_purpose->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('room_purpose_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1">
                                        <div class="col-12 col-md-4 rooms-add-parent-div mb-2">
                                            <label for="basic-url" class="form-label form-label-sm">Помещение <span class="text-danger"><b>*</b></span></label>
                                            <div class="input-group input-group-sm mb-2 room-add-div">
                                                <input type="text" list="1" class="form-control form-control-sm"
                                                       id="basic-url" aria-describedby="basic-addon3"
                                                       placeholder="Начните писать ..." name="room_names[]" required>
                                                <datalist id="1">
                                                    @forelse($rooms as $room)
                                                        <option data-room_key="{{$room->id}}" value="{{$room->name}}">
                                                    @empty
                                                        <option data-room_key="" value="нет данных ...">
                                                    @endforelse
                                                </datalist>
                                                <span class="input-group-text room-add-button" id="basic-addon3"><img
                                                        src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                        alt="add" title="Добавить" height="20"></span>
                                                <span class="input-group-text room-delete-button" id="basic-addon3"><img
                                                        src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                        alt="delete" title="Удалить" height="20"></span>
                                            </div>
                                            @error('room_names.*')
                                            <div class="text-danger"
                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-3">
                                        <div class="col">
                                            <div class="input-group input-group-sm">
                                                <a href="{{route('trk_room.index')}}"
                                                   class="btn btn-sm btn-outline-success col-6"><img
                                                        src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                        alt="back" title="Назад"></a>
                                                <button type="submit" class="btn btn-sm btn-outline-danger col-6"><img
                                                        src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                        title="Сохранить"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal alert -->
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel2">Создание ТРК/Помещения</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Если в списках чего-то нет, попросите админа добавить</p>
                        <p>Помещения заполняются поэтажно и один тип помещений, заполнили этаж, сохранили.</p>
                        <p>Ежедневный обход, как правило нужен только в технических помещениях.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
        <script src="{{asset('assets/js/trk_rooms/add_room.js')}}" defer></script>
        <script src="{{asset('assets/js/trk_rooms/delete_room.js')}}" defer></script>
@endsection
