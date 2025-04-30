@extends('layouts.backend.main')

@section('title', 'Главная | Климат/Помещение')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Климат/Помещение создание</h4>
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
                            <form action="{{route('trk_room_climates.store')}}" method="post">
                                @csrf
                                <div class="card shadow p-3">
                                    <div class="equipment-add-div p-2 my-3 rounded"
                                         style="background-color: rgba(145, 135, 255, 0.2)">
                                        <p><b>Где выполняются замеры:</b></p>
                                        <div class="row row-cols-1 row-cols-md-5">
                                            <div class="col mb-3">
                                                <label for="trk_id" class="form-label form-label-sm">ТРК <span
                                                        class="text-danger"><b>*</b></span></label>
                                                <select required name="trk_id" class="form-select form-select-sm"
                                                        id="trk_id">
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
                                                <label for="building_id" class="form-label form-label-sm">Блок/Зона <span
                                                        class="text-danger"><b>*</b></span></label>
                                                <select required name="building_id" class="form-select form-select-sm"
                                                        id="building_id">
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
                                                <label for="floor_id" class="form-label form-label-sm">Этаж <span
                                                        class="text-danger"><b>*</b></span></label>
                                                <select required name="floor_id" class="form-select form-select-sm"
                                                        id="floor_id">
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
                                            <div class="col mb-4">
                                                <label for="room_id" class="form-label form-label-sm">Помещение (где замеры) <span
                                                        class="text-danger"><b>*</b></span></label>
                                                <select required name="room_id" class="form-select form-select-sm"
                                                        id="room_id">
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
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-4 mt-3">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_inside">Т в помещении, град. <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required class="form-control form-control-sm" type="number"
                                                   step="0.1" name="t_inside" placeholder="21.5">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_outside">Т на улице</label>
                                            <input class="form-control form-control-sm" type="number" step="0.1"
                                                   name="t_outside" placeholder="-5.5">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_supply_air">Т притока</label>
                                            <input class="form-control form-control-sm" type="number" step="0.1"
                                                   name="t_supply_air" placeholder="22.3">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_extract_air">Т
                                                вытяжки</label>
                                            <input class="form-control form-control-sm" type="number" step="0.1"
                                                   name="t_extract_air" placeholder="25.4">
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-4">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="h_inside">Влажность в
                                                помещении</label>
                                            <input class="form-control form-control-sm" type="number" step="1"
                                                   name="h_inside" placeholder="55">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="q_supply_air_total">Расход
                                                притока общий</label>
                                            <input class="form-control form-control-sm" type="number" step="1"
                                                   name="q_supply_air_total" placeholder="120">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="q_extract_air_total">Расход
                                                вытяжки общий</label>
                                            <input class="form-control form-control-sm" type="number" step="1"
                                                   name="q_extract_air_total" placeholder="100">
                                        </div>
                                        <div class="col mb-3">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="comment">Комментарий (255 символов)</label>
                                            <input class="form-control form-control-sm" type="text" name="comment">
                                            @error('comment')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 input-group-sm mt-2">
                                        <a href="{{route('trk_room_climates.index')}}"
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
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
        <script>
            $(document).ready(function () {

                $('#trk_id').on('change', function () {

                    let idTrk = this.value;
                    let idBuilding = $("#building_id").val();
                    let idFloor = $("#floor_id").val();
                    let idRoom = $("#room_id").val();
                    let idSystem = $("#system_id").val();

                    $.ajax({
                        url: "{{url('api/fetch-buildings')}}",
                        type: "POST",
                        data: {
                            trk_id: idTrk,
                            building_id: idBuilding,
                            floor_id: idFloor,
                            room_id: idRoom,
                            system_id: idSystem,
                            _token: '{{csrf_token()}}',
                        },
                        dataType: 'json',
                        success: function (result) {

                            $('#room_id').html('');
                            $.each(result.rooms, function (key, value) {
                                $("#room_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.rooms.length === 0) {
                                $("#room_id").append('<option value="">нет помещений ...</option>');
                            }

                            $('#building_id').html('');
                            $.each(result.buildings, function (key, value) {
                                $("#building_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.buildings.length === 0) {
                                $("#building_id").append('<option value="">нет зданий ...</option>');
                            }

                            $('#floor_id').html('');
                            $.each(result.floors, function (key, value) {
                                $("#floor_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.floors.length === 0) {
                                $("#floor_id").append('<option value="">нет этажей ...</option>');
                            }

                            $('#room_id').html('');
                            $.each(result.rooms, function (key, value) {
                                $("#room_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.rooms.length === 0) {
                                $("#room_id").append('<option value="">нет помещений ...</option>');
                            }

                            $('#building_id_2').html('');
                            $.each(result.buildings, function (key, value) {
                                $("#building_id_2").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.buildings.length === 0) {
                                $("#building_id_2").append('<option value="">нет зданий ...</option>');
                            }

                            $('#floor_id_2').html('');
                            $.each(result.floors, function (key, value) {
                                $("#floor_id_2").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.floors.length === 0) {
                                $("#floor_id_2").append('<option value="">нет этажей ...</option>');
                            }

                            $('#room_id_2').html('');
                            $.each(result.rooms, function (key, value) {
                                $("#room_id_2").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.rooms.length === 0) {
                                $("#room_id_2").append('<option value="">нет помещений ...</option>');
                            }
                        }
                    });
                });

                $('#building_id').on('change', function () {

                    let idTrk = $("#trk_id").val();
                    let idBuilding = this.value;
                    let idFloor = $("#floor_id").val();
                    let idRoom = $("#room_id").val();
                    let idSystem = $("#system_id").val();

                    $.ajax({
                        url: "{{url('api/fetch-floors')}}",
                        type: "POST",
                        data: {
                            trk_id: idTrk,
                            building_id: idBuilding,
                            floor_id: idFloor,
                            room_id: idRoom,
                            system_id: idSystem,
                            _token: '{{csrf_token()}}',
                        },
                        dataType: 'json',
                        success: function (result) {

                            $('#room_id').html('');
                            $.each(result.rooms, function (key, value) {
                                $("#room_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.rooms.length === 0) {
                                $("#room_id").append('<option value="">нет помещений ...</option>');
                            }

                            $('#floor_id').html('');
                            $.each(result.floors, function (key, value) {
                                $("#floor_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.floors.length === 0) {
                                $("#floor_id").append('<option value="">нет этажей ...</option>');
                            }
                        }
                    });
                });

                $('#floor_id').on('change', function () {

                    let idTrk = $("#trk_id").val();
                    let idBuilding = $("#building_id").val();
                    let idFloor = this.value;
                    let idRoom = $("#room_id").val();
                    let idSystem = $("#system_id").val();

                    $.ajax({
                        url: "{{url('api/fetch-rooms-by-floor')}}",
                        type: "POST",
                        data: {
                            trk_id: idTrk,
                            building_id: idBuilding,
                            floor_id: idFloor,
                            room_id: idRoom,
                            system_id: idSystem,
                            _token: '{{csrf_token()}}',
                        },
                        dataType: 'json',
                        success: function (result) {

                            $('#room_id').html('');
                            $.each(result.rooms, function (key, value) {
                                $("#room_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.rooms.length === 0) {
                                $("#room_id").append('<option value="">нет помещений ...</option>');
                            }

                        }
                    });
                });
            });
        </script>
@endsection
