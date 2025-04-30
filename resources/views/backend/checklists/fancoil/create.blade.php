@extends('layouts.backend.main')

@section('title', 'Главная | Чеклист создание')

@section('content')
    @csrf
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Заполнение чеклиста фанкойла</h4>
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
                            <form action="{{route('checklists_fancoil.store')}}" method="post">
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
                                            <label for="room_id" class="form-label form-label-sm">Помещение<span
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
                                        <div class="col mb-3">
                                            <label for="system_id" class="form-label form-label-sm">Система <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="system_id" class="form-select form-select-sm" id="system_id">
                                                @forelse($systems as $system)
                                                    <option value="{{$system->id}}" {{old('system_id') == $system->id ? 'selected' : null}}>{{$system->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('system_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 mb-2">
                                        <div class="col-12 col-md-3">
                                            <label for="basic-url" class="form-label form-label-sm">Работы (для акта)
                                                <span class="text-danger"><b>*</b></span>
                                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#exampleModal8">Что выбрать?
                                                </button>
                                            </label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select required multiple size="3" name="work_types[]" id="work_types"
                                                        class="form-select form-select-sm">
                                                    @forelse($work_types as $work_type)
                                                        <option
                                                            value="{{$work_type->id}}">{{$work_type->name}}</option>
                                                    @empty
                                                        <option value="">нет данных ...</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                            @error('work_types.*')
                                            <div class="text-danger"
                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="equipments-add-parent-div mb-3">
                                        <div class="equipment-add-div p-2 my-3 rounded"
                                             style="background-color: rgba(145, 135, 255, 0.2)">
                                            <div class="row row-cols-1">
                                                <div class="col-12 col-md-3">
                                                    <label for="basic-url" class="form-label form-label-sm">Оборудование
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <div class="input-group input-group-sm mb-3">
                                                        <select required name="equipment_id" id="equipment_id"
                                                                class="form-select form-select-sm  equipment-name-input">
                                                            @forelse($equipment_names as $equipment)
                                                                <option
                                                                    value="{{$equipment->id}}" {{old('equipment_id') == $equipment->id ? 'selected' : null}}>{{$equipment->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                    @error('equipment_id')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-2">
                                                    <label for="air_inlet_temperature" class="form-label form-label-sm">Т воздуха на входе
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                        <input required name="air_inlet_temperature" id="air_inlet_temperature"
                                                                class="form-control form-control-sm" placeholder="22.6" type="number" step="0.1" value="{{old('air_inlet_temperature')}}">
                                                    @error('air_inlet_temperature')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-2">
                                                    <label for="air_outlet_temperature" class="form-label form-label-sm">Т воздуха на выходе
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <input required name="air_outlet_temperature" id="air_outlet_temperature"
                                                           class="form-control form-control-sm" placeholder="16.3" type="number" step="0.1" value="{{old('air_outlet_temperature')}}">
                                                    @error('air_outlet_temperature')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-2">
                                                    <label for="cold_water_inlet_temperature" class="form-label form-label-sm">Т воды на входе
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <input required name="cold_water_inlet_temperature" id="cold_water_inlet_temperature"
                                                           class="form-control form-control-sm" placeholder="7.6" type="number" step="0.1" value="{{old('cold_water_inlet_temperature')}}">
                                                    @error('cold_water_inlet_temperature')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-2">
                                                    <label for="cold_water_outlet_temperature" class="form-label form-label-sm">Т воды на выходе
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <input required name="cold_water_outlet_temperature" id="cold_water_outlet_temperature"
                                                           class="form-control form-control-sm" placeholder="14.3" type="number" step="0.1" value="{{old('cold_water_outlet_temperature')}}">
                                                    @error('cold_water_outlet_temperature')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-2">
                                                <div class="col-12">
                                                    <label for="air_outlet_temperature" class="form-label form-label-sm">Комментарий
                                                    </label>
                                                    <input name="comment" id="comment"
                                                           class="form-control form-control-sm" placeholder="Любой комментарий" type="text" value="{{old('comment')}}">
                                                    @error('comment')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="executors-add-parent-div">
                                        <label class="form-label form-label-sm">Исполнители <span
                                                class="text-danger"><b>*</b></span></label>
                                        <div class="executor-add-div">
                                            <div class="row row-cols-1">
                                                <div class="col-12 col-md-4">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <span class="input-group-text executor-add-button"><img
                                                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                alt="add" title="Добавить" height="20"></span>
                                                        <span class="input-group-text executor-delete-button"><img
                                                                src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                alt="delete" title="Удалить" height="20"></span>
                                                        <input value="{{Auth::user()->name}}" required type="text"
                                                               list="executors_list"
                                                               class="form-control form-control-sm"
                                                               placeholder="Начните писать ..."
                                                               name="executors[]">
                                                        <datalist id="executors_list">
                                                            @forelse($executors as $executor)
                                                                <option data-equipment_key="{{$executor->id}}"
                                                                        value="{{$executor->name}}">
                                                            @empty
                                                                <option data-equipment_key="" value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </div>
                                                    @error('executors.*')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-3">
                                        <div class="col mt-4">
                                            <div class="input-group input-group-sm">
                                                <a href="{{route('checklists_fancoil.index')}}"
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
                        <h1 class="modal-title fs-5" id="exampleModalLabel2">Заполнение акта</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-body">
                            <p>Если после выбора ТРК, блока, этажа, помещения</p>
                            <p>в выпадающем списке Оборудование написано</p>
                            <p>нет оборудования ...</p>
                            <p>эначит в выбранном Вами помещении</p>
                            <p>нет оборудования типа Кондиционирование</p>
                            <p>Поля со звездочкой необходимо заполнить</p>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal 8 -->
        <div class="modal fade" id="exampleModal8" tabindex="-1" aria-labelledby="exampleModalLabel8"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel3">Какое ТО выбрать</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Поддерживается множественный выбор<br> при зажатой клавише ctrl</p>
                        <p>При выполнении <b>ТО 4</b>: выбираем <b>только ТО 4</b>.</p>
                        <p>При выполнении <b>ТО 5</b>: выбираем <b>ТО 5 и ТО 4</b>,<br> т.к. ТО 4 входит в ТО 5.</p>
                        <p>При выполнении <b>ТО 6</b>: выбираем <b>ТО 6, ТО 5 и ТО 4</b>,<br> т.к. ТО 4 и ТО 5 входят в ТО 6.</p>
                        <p>Какой в этом смысл?</p>
                        <p>Это будет использоваться при планировании технических мероприятий.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
        <script src="{{asset('assets/js/checklists/fancoil/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/checklists/fancoil/delete_executor.js')}}"></script>
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
                        url: "{{url('api/v1/checklists/fetch-room-air-sources-by-trk')}}",
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

                            $('#building_id').html('');
                            $.each(result.buildings, function (key, value) {
                                $("#building_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.buildings.length === 0) {
                                $("#building_id").append('<option value="">нет зданий ...</option>');
                                console.log('Нет зданий для заполнения АВР');
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

                            $('#equipment_id').html('');
                            $.each(result.equipments, function (key, value) {
                                $("#equipment_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.equipments && result.equipments.length === 0) {
                                $("#equipment_id").append('<option value="">нет оборудования ...</option>');
                                console.log('Нет оборудования для заполнения АВР');
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
                        url: "{{url('api/v1/checklists/fetch-room-air-sources-by-building')}}",
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

                            $('#equipment_id').html('');
                            $.each(result.equipments, function (key, value) {
                                $("#equipment_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.equipments && result.equipments.length === 0) {
                                $("#equipment_id").append('<option value="">нет оборудования ...</option>');
                                console.log('Нет оборудования для заполнения АВР');
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
                        url: "{{url('api/v1/checklists/fetch-room-air-sources-by-floor')}}",
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

                            $('#equipment_id').html('');
                            $.each(result.equipments, function (key, value) {
                                $("#equipment_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.equipments && result.equipments.length === 0) {
                                $("#equipment_id").append('<option value="">нет оборудования ...</option>');
                                console.log('Нет оборудования для заполнения АВР');
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
                        url: "{{url('api/v1/checklists/fetch-room-air-sources-by-room')}}",
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
                            $.each(result.equipments, function (key, value) {
                                $("#equipment_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.equipments && result.equipments.length === 0) {
                                $("#equipment_id").append('<option value="">нет оборудования ...</option>');
                                console.log('Нет оборудования для заполнения АВР');
                            }
                        }
                    });
                });
            });
        </script>
@endsection
