@extends('layouts.backend.main')

@section('title', 'Главная | Редактирование ремонта')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Редактирование ремонта</h4>
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
                            <form action="{{route('trk_repairs.update', $trk_repair)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <input hidden id="trk_id" value="{{$trk_repair->trk_room->trk->id}}">
                                            <input readonly class="form-control form-control-sm" value="{{$trk_repair->trk_room->trk->name}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="building_id" class="form-label form-label-sm">Блок/Зона <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="building_id" class="form-select form-select-sm" id="building_id">
                                                @forelse($buildings as $building)
                                                    <option
                                                        {{$building->id == $trk_repair->trk_room->building->id ? 'selected' : null}} value="{{$building->id}}">{{$building->name}}</option>
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
                                                        {{$floor->id == $trk_repair->trk_room->floor->id ? 'selected' : null}} value="{{$floor->id}}">{{$floor->name}}</option>
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
                                                        {{$room->id == $trk_repair->trk_room->room->id ? 'selected' : null}} value="{{$room->id}}">{{$room->name}}</option>
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
                                            <label for="equipment_id" class="form-label form-label-sm">Оборудование<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select required name="equipment_id" id="equipment_id"
                                                    class="form-select form-select-sm">
                                                @forelse($equipments as $equipment)
                                                    <option
                                                        value="{{$equipment->id}}" {{old('equipment_id', $trk_repair->trk_equipment->equipment_name->id) == $equipment->id ? 'selected' : null}}>{{$equipment->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('equipment_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="description" class="form-label form-label-sm">Ремонт <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <textarea required name="description" class="form-control form-control-sm"
                                                      placeholder="Что планируется">{{old('description', $trk_repair->description)}}</textarea>
                                            @error('description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="executed_result" class="form-label form-label-sm">Что
                                                сделано</label>
                                            <textarea autofocus name="executed_result"
                                                      class="form-control form-control-sm"
                                                      placeholder="Что было сделано">{{old('executed_result', $trk_repair->executed_result)}}</textarea>
                                            @error('executed_result')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="done_progress" class="form-label form-label-sm">Процент
                                                выполнения</label>
                                            <input value="{{old('done_progress', $trk_repair->done_progress)}}"
                                                   type="range" class="form-range" min="0" max="100" step="10"
                                                   id="done_progress" name="done_progress">
                                            @error('done_progress')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1">
                                        <div class="col-12 col-md-4 executors-add-parent-div mb-2">
                                            <label for="basic-url" class="form-label form-label-sm">Кто выполнял</label>
                                            <div class="input-group mb-3 executor-add-div">
                                                <input required type="text" list="1"
                                                       class="form-control form-control-sm" id="basic-url"
                                                       aria-describedby="basic-addon3" placeholder="Начните писать ..."
                                                       name="executor_names[]" value="{{Auth::user()->name}}">
                                                <datalist id="1">
                                                    @forelse($executor_names as $executor)
                                                        <option data-room_key="{{$executor->id}}"
                                                                value="{{$executor->name}}">
                                                    @empty
                                                        <option data-room_key="" value="нет данных ...">
                                                    @endforelse
                                                </datalist>
                                                <span class="input-group-text executor-add-button"
                                                      id="basic-addon3"><img
                                                        src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                        alt="add" title="Добавить" height="20"></span>
                                                <span class="input-group-text executor-delete-button" id="basic-addon3"><img
                                                        src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                        alt="delete" title="Удалить" height="20"></span>
                                            </div>
                                            @error('executor_names.*')
                                            <div class="text-danger"
                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="executed_at" class="form-label form-label-sm">Дата и время
                                                выполнения</label>
                                            <input class="form-control form-control-sm" type="datetime-local"
                                                   id="done_at"
                                                   name="executed_at"
                                                   value="{{old('executed_at', $trk_repair->executed_at)}}"
                                                   min="2019-01-07T00:00" max="2050-12-14T00:00">
                                            @error('executed_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 input-group-sm">
                                        <a href="{{route('trk_repairs.show', $trk_repair)}}"
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
        <script src="{{asset('assets/js/trk_repairs/add_executor.js')}}" defer></script>
        <script src="{{asset('assets/js/trk_repairs/delete_executor.js')}}" defer></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
        <script>
            $(document).ready(function () {

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
                                console.log('Нет помещений для заполнения АВР');
                            }

                            $('#floor_id').html('');
                            $.each(result.floors, function (key, value) {
                                $("#floor_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.floors.length === 0) {
                                $("#floor_id").append('<option value="">нет этажей ...</option>');
                                console.log('Нет этажей для заполнения АВР');
                            }

                            $('#system_id').html('');
                            $.each(result.systems, function (key, value) {
                                $("#system_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.systems.length === 0) {
                                $("#system_id").append('<option value="">нет помещений ...</option>');
                                console.log('Нет систем для заполнения АВР');
                            }

                            $('#equipment_id').html('');
                            $.each(result.equipment_names, function (key, value) {
                                $("#equipment_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.equipment_names.length === 0) {
                                $('#equipment_id').html('');
                                $("#equipment_id").append('<option value=""> нет оборудования ... </option>');
                            }
                            if (result.equipments && result.equipments.length === 0) {
                                $(".equipment_id").append('<option value="">Нет оборудования ...</option>');
                                console.log('Нет оборудования в этом помещении для заполнения');
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
                                console.log('Нет помещений для заполнения АВР');
                            }

                            $('#system_id').html('');
                            $.each(result.systems, function (key, value) {
                                $("#system_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.systems.length === 0) {
                                $("#system_id").append('<option value="">нет помещений ...</option>');
                                console.log('Нет систем для заполнения АВР');
                            }

                            $('#equipment_id').html('');
                            $.each(result.equipment_names, function (key, value) {
                                $("#equipment_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.equipment_names.length === 0) {
                                $('#equipment_id').html('');
                                $("#equipment_id").append('<option value=""> нет оборудования ... </option>');
                            }
                            if (result.equipments && result.equipments.length === 0) {
                                $(".equipment_id").append('<option value="">Нет оборудования ...</option>');
                                console.log('Нет оборудования в этом помещении для заполнения');
                            }
                        }
                    });
                });

                $('#room_id').on('change', function () {

                    let idTrk = $('#trk_id').val();
                    let idBuilding = $("#building_id").val();
                    let idFloor = $("#floor_id").val();
                    let idRoom = this.value;
                    let idSystem = $('#system_id').val();

                    $.ajax({
                        url: "{{url('api/fetch-equipments-by-room')}}",
                        type: "POST",
                        data: {
                            room_id: idRoom,
                            trk_id: idTrk,
                            system_id: idSystem,
                            building_id: idBuilding,
                            floor_id: idFloor,
                            _token: '{{csrf_token()}}',
                        },
                        dataType: 'json',
                        success: function (result) {

                            $('#system_id').html('');
                            $.each(result.systems, function (key, value) {
                                $("#system_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.systems.length === 0) {
                                $("#system_id").append('<option value="">нет помещений ...</option>');
                                console.log('Нет систем для заполнения АВР');
                            }

                            $('#equipment_id').html('');
                            $.each(result.equipment_names, function (key, value) {
                                $("#equipment_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.equipment_names.length === 0) {
                                $('#equipment_id').html('');
                                $("#equipment_id").append('<option value=""> нет оборудования ... </option>');
                            }
                            if (result.equipments && result.equipments.length === 0) {
                                $(".equipment_id").append('<option value="">Нет оборудования ...</option>');
                                console.log('Нет оборудования в этом помещении для заполнения');
                            }
                        }
                    });
                });

                $('#system_id').on('change', function () {

                    let idSystem = this.value;
                    let idTrk = $('#trk_id').val();
                    let idRoom = $("#room_id").val();
                    let idBuilding = $("#building_id").val();
                    let idFloor = $("#floor_id").val();

                    $.ajax({

                        url: "{{url('api/fetch-equipments-by-system')}}",
                        type: "POST",

                        data: {
                            room_id: idRoom,
                            trk_id: idTrk,
                            system_id: idSystem,
                            building_id: idBuilding,
                            floor_id: idFloor,
                            _token: '{{csrf_token()}}',
                        },

                        dataType: 'json',
                        success: function (result) {

                            $('#equipment_id').html('');
                            $.each(result.equipment_names, function (key, value) {
                                $("#equipment_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.equipment_names.length === 0) {
                                $('#equipment_id').html('');
                                $("#equipment_id").append('<option value=""> нет оборудования ... </option>');
                            }
                        }
                    });
                });
            });
        </script>
@endsection
