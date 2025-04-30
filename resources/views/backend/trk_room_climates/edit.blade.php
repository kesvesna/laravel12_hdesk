@extends('layouts.backend.main')

@section('title', 'Главная | Климат/Помещение редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Климат/Помещение редактирование</h4>
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
                            <form action="{{route('trk_room_climates.update', $trk_room_climate)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row row-cols-1 row-cols-md-4">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="trk_id">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="trk_id" id="trk_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_trks as $trk)
                                                    <option
                                                        value="{{$trk->id}}" {{old('trk_id', $trk_room_climate->trk_room->trk->id) == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="building_id">Блок/Здание <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="building_id" id="building_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_buildings as $building)
                                                    <option
                                                        value="{{$building->id}}"  {{old('building_id', $trk_room_climate->trk_room->building->id) == $building->id ? 'selected' : null}}>{{$building->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('building_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="floor_id">Этаж <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="floor_id" id="floor_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_floors as $floor)
                                                    <option
                                                        value="{{$floor->id}}"  {{old('floor_id', $trk_room_climate->trk_room->floor->id) == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('floor_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="room_id">Помещение <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="room_id" id="room_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_rooms as $room)
                                                    <option
                                                        value="{{$room->id}}" {{$trk_room_climate->trk_room->room->id == $room->id ? 'selected' : null}}>{{$room->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-4 mt-3">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_inside">Т в помещении <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required class="form-control form-control-sm" type="number"
                                                   step="0.1" name="t_inside" placeholder="21.5"
                                                   value="{{$trk_room_climate->t_inside}}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_outside">Т на улице</label>
                                            <input class="form-control form-control-sm" type="number" step="0.1"
                                                   name="t_outside" placeholder="-5.5"
                                                   value="{{$trk_room_climate->t_outside}}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_supply_air">Т притока</label>
                                            <input class="form-control form-control-sm" type="number" step="0.1"
                                                   name="t_supply_air" placeholder="22.3"
                                                   value="{{$trk_room_climate->t_supply_air}}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_extract_air">Т
                                                вытяжки</label>
                                            <input class="form-control form-control-sm" type="number" step="0.1"
                                                   name="t_extract_air" placeholder="25.4"
                                                   value="{{$trk_room_climate->t_extract_air}}">
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-4">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="h_inside">Влажность в
                                                помещении</label>
                                            <input class="form-control form-control-sm" type="number" step="1"
                                                   name="h_inside" placeholder="55"
                                                   value="{{$trk_room_climate->h_inside}}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="q_supply_air_total">Расход
                                                притока общий</label>
                                            <input class="form-control form-control-sm" type="number" step="1"
                                                   name="q_supply_air_total" placeholder="120"
                                                   value="{{$trk_room_climate->q_supply_air_total}}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="q_extract_air_total">Расход
                                                вытяжки общий</label>
                                            <input class="form-control form-control-sm" type="number" step="1"
                                                   name="q_extract_air_total" placeholder="100"
                                                   value="{{$trk_room_climate->q_extract_air_total}}">
                                        </div>
                                        <div class="col mb-3">
                                        </div>
                                    </div>
                                        <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="comment">Комментарий (255 символов)</label>
                                            <input class="form-control form-control-sm" type="text" name="comment"
                                                   value="{{$trk_room_climate->comment}}">
                                            @error('comment')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>

                                    </div>
                                    <div class="input-group mb-2 input-group-sm mt-2">
                                        <a href="{{route('trk_room_climates.show', $trk_room_climate)}}"
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
                    var idTrk = this.value;
                    $("#room_id").html('');
                    $.ajax({
                        url: "{{url('api/fetch-rooms')}}",
                        type: "POST",
                        data: {
                            trk_id: idTrk,
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
