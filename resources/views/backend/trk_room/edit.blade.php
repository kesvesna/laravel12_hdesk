@extends('layouts.backend.main')

@section('title', 'Главная | ТРК/Помещение редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">ТРК/Помещение редактирование</h4>
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
                            <form action="{{route('trk_room.update', $trk_room)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="trk_id">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select disabled name="trk_id" class="form-select form-select-sm">
                                                @forelse($trks as $trk)
                                                    <option
                                                        {{$trk->id == $trk_room->trk->id ? 'selected' : null}} value="{{$trk->id}}">{{$trk->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse

                                            </select>
                                        </div>
                                    </div>
                                    @error('trk_id')
                                    <div class="text-danger">{{$message}}</div>
                                    @enderror
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="building_id">Блок/Зона <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="building_id" class="form-select form-select-sm">
                                                @forelse($buildings as $building)
                                                    <option
                                                        {{$building->id == $trk_room->building->id ? 'selected' : null}} value="{{$building->id}}">{{$building->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('building_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="floor_id">Этаж/Отметка <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select name="floor_id" class="form-select form-select-sm">
                                                @forelse($floors as $floor)
                                                    <option
                                                        {{$floor->id == $trk_room->floor->id ? 'selected' : null}} value="{{$floor->id}}">{{$floor->name}}</option>
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
                                            <label class="form-label form-label-sm" for="room_id">Помещение <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select name="room_id" class="form-select form-select-sm">
                                                @forelse($rooms as $room)
                                                    <option
                                                        {{$room->id == $trk_room->room->id ? 'selected' : null}} value="{{$room->id}}">{{$room->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="axe_id">Оси</label>
                                            <select name="axe_id" class="form-select form-select-sm">
                                                <option value="">не выбраны</option>
                                                @forelse($axes as $axe)
                                                    <option
                                                        {{ isset($trk_room->axe) && $axe->id == $trk_room->axe->id ? 'selected' : null}} value="{{$axe->id}}">{{$axe->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('axe_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm"
                                                   for="room_purpose_id">Назначение</label>
                                            <select name="room_purpose_id" class="form-select form-select-sm">
                                                @forelse($room_purposes as $room_purpose)
                                                    <option
                                                        {{ isset($trk_room->room_purpose) && $room_purpose->id == $trk_room->room_purpose->id ? 'selected' : null}} value="{{$room_purpose->id}}">{{$room_purpose->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('room_purpose_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <input class="form-check-input me-2" type="checkbox" value="1"
                                                   {{$trk_room->need_daily_checking ? 'checked' : null}} name="need_daily_checking">
                                            <label class="form-label form-label-sm" for="need_daily_checking">Нужен
                                                ежедневный обход</label>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="comment">Комментарий</label>
                                            <input class="form-control form-control-sm" value="{{$trk_room->comment}}"
                                                   type="text" name="comment" placeholder="Любой комментарий">
                                            @error('comment')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 input-group-sm">
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
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
