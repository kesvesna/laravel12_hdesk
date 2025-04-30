@extends('layouts.backend.main')

@section('title', 'Главная | Чеклист редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Чеклист воздуховода редактирование</h4>
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
                            <form action="{{route('checklists_air_duct.update', $checklist)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
{{--                                    <div class="col mb-3">--}}
{{--                                        <!-- Button trigger modal responsibility -->--}}
{{--                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"--}}
{{--                                                data-bs-target="#exampleModal2">Как создать?--}}
{{--                                        </button>--}}
{{--                                    </div>--}}
                                    <div class="row row-cols-1">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">Дата</label>
                                            <input readonly class="form-control form-control-sm"
                                                  value="{{$checklist->created_at}}">
                                        </div>
                                    </div>
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
                                                        <option value="{{$trk->id}}" {{old('trk_id', $checklist->trk_room->trk->id) == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
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
                                                        <option value="{{$building->id}}" {{old('building_id', $checklist->trk_room->building->id) == $building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                                        <option value="{{$floor->id}}" {{old('floor_id', $checklist->trk_room->floor->id) == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
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
                                                        <option value="{{$room->id}}" {{old('room_id', $checklist->trk_room->room->id) == $room->id ? 'selected' : null}}>{{$room->name}}</option>
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
                                    <div class="equipment-add-div p-2 my-3 rounded"
                                         style="background-color: rgba(218, 117, 255, 0.2)">
                                        <p><b>Где находится установка источник воздухообмена:</b></p>
                                        <div class="row">
                                            <div class="col mb-3">
                                                <label for="building_id_2" class="form-label form-label-sm">Блок/Зона<span
                                                        class="text-danger"><b>*</b></span></label>
                                                <select required name="building_id_2" class="form-select form-select-sm"
                                                        id="building_id_2">
                                                    @forelse($buildings as $building)
                                                        <option value="{{$building->id}}" {{old('building_id_2', $checklist->trk_equipment->trk_room->building->id) == $building->id ? 'selected' : null}}>{{$building->name}}</option>
                                                    @empty
                                                        <option value="">нет данных ...</option>
                                                    @endforelse
                                                </select>
                                                @error('building_id_2')
                                                <div class="text-danger">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <div class="col mb-3">
                                                <label for="floor_id_2" class="form-label form-label-sm">Этаж <span
                                                        class="text-danger"><b>*</b></span></label>
                                                <select required name="floor_id_2" class="form-select form-select-sm"
                                                        id="floor_id_2">
                                                    @forelse($floors as $floor)
                                                        <option value="{{$floor->id}}" {{old('floor_id_2', $checklist->trk_equipment->trk_room->floor->id) == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                    @empty
                                                        <option value="">нет данных ...</option>
                                                    @endforelse
                                                </select>
                                                @error('floor_id_2')
                                                <div class="text-danger">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <div class="col mb-4">
                                                <label for="room_id_2" class="form-label form-label-sm">Помещение<span
                                                        class="text-danger"><b>*</b></span></label>
                                                <select required name="room_id_2" class="form-select form-select-sm"
                                                        id="room_id_2">
                                                    @forelse($rooms as $room)
                                                        <option value="{{$room->id}}" {{old('room_id_2', $checklist->trk_equipment->trk_room->room->id) == $room->id ? 'selected' : null}}>{{$room->name}}</option>
                                                    @empty
                                                        <option value="">нет данных ...</option>
                                                    @endforelse
                                                </select>
                                                @error('room_id_2')
                                                <div class="text-danger">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <div class="col-12 col-md-3 mb-3">
                                                <label class="form-label form-label-sm" for="equipment_id">Оборудование (источник)
                                                    <span class="text-danger"><b>*</b></span></label>
                                                <input required type="text" list="equipment_names_list"
                                                       class="form-control form-control-sm"
                                                       placeholder="Начните писать ..."
                                                       name="equipment_id" id="equipment_id" data-equipment-id="0" value="{{$checklist->trk_equipment->equipment_name->name}}">
                                                <datalist id="equipment_names_list">
                                                    @forelse($equipment_names as $equipment)
                                                        <option data-equipment_key="{{$equipment->id}}"
                                                                value="{{$equipment->name}}">
                                                    @empty
                                                        <option data-equipment_key="" value="нет данных ...">
                                                    @endforelse
                                                </datalist>
                                                @error('equipment_id')
                                                <div class="text-danger">{{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 mb-2">
                                        <div class="col-12 col-md-3">
                                            <label for="basic-url" class="form-label form-label-sm">Вид работ
                                                <span class="text-danger"><b>*</b></span>
                                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#exampleModal8">Что выбрать?
                                                </button>
                                            </label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select required multiple size="3" name="work_types[]" id="work_types"
                                                        class="form-select form-select-sm">
                                                    @forelse($work_types as $work_type)
                                                        <option value="{{$work_type->id}}"
                                                        @foreach($checklist->avr->first()->avr_works as $avr_work)
                                                            {{$avr_work->work_name_id == $work_type->id ? 'selected' : null}}
                                                            @endforeach
                                                        >{{$work_type->name}}</option>
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
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="measuring_point_number" class="form-label form-label-sm">Номер точки
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <select required name="measuring_point_number" class="form-select form-select-sm">
                                                        @for($i = 1; $i <= 25; $i++)
                                                            <option value="{{$i}}" {{$i == $checklist->measuring_point_number ? 'selected' : null}}>{{$i}}</option>
                                                        @endfor
                                                    </select>
                                                    @error('measuring_point_number')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="air_direction_type" class="form-label form-label-sm">Тип точки
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <select required name="air_direction_type" class="form-select form-select-sm">
                                                        <option value="0" {{0 == $checklist->air_direction_type ? 'selected' : null}}>Приток</option>
                                                        <option value="1" {{1 == $checklist->air_direction_type ? 'selected' : null}}>Вытяжка</option>
                                                    </select>
                                                    @error('air_direction_type')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="length_or_diameter" class="form-label form-label-sm">Длина или диаметр, мм
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <input required name="length_or_diameter" id="length_or_diameter"
                                                           class="form-control form-control-sm"  type="number" placeholder="200" value="{{old('length_or_diameter', $checklist->length_or_diameter)}}">
                                                    @error('length_or_diameter')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="width" class="form-label form-label-sm">Ширина, мм
                                                    </label>
                                                    <input name="width" id="width"
                                                           class="form-control form-control-sm"  type="number" placeholder="200" value="{{old('width', $checklist->width)}}">
                                                    @error('width')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="air_speed" class="form-label form-label-sm">Скорость воздуха, м/сек
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <input required name="air_speed" id="air_speed"
                                                           class="form-control form-control-sm"  type="number" step="0.1" placeholder="3.5" value="{{old('air_speed', $checklist->air_speed)}}">
                                                    @error('air_speed')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="duct_cross_sectional_area" class="form-label form-label-sm">Площадь сечения воздуховода, кв.м
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <input required name="duct_cross_sectional_area" id="duct_cross_sectional_area"
                                                           class="form-control form-control-sm"  type="number" step="0.1" placeholder="0.08" value="{{old('duct_cross_sectional_area', $checklist->duct_cross_sectional_area)}}">
                                                    @error('duct_cross_sectional_area')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="air_flow_rate" class="form-label form-label-sm">Расход воздуха, куб.м/час
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <input required name="air_flow_rate" id="air_flow_rate"
                                                           class="form-control form-control-sm"  type="number" placeholder="900" value="{{old('air_flow_rate', $checklist->air_flow_rate)}}">
                                                    @error('air_flow_rate')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="air_pressure" class="form-label form-label-sm">Давление воздуха, Па
                                                    </label>
                                                    <input name="air_pressure" id="air_pressure"
                                                           class="form-control form-control-sm"  type="number" placeholder="25" value="{{old('air_pressure', $checklist->air_pressure)}}">
                                                    @error('air_pressure')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="air_temperature" class="form-label form-label-sm">Температура воздуха, град
                                                    </label>
                                                    <input name="air_temperature" id="air_temperature"
                                                           class="form-control form-control-sm"  type="number" step="0.1" placeholder="21.0" value="{{old('air_temperature', $checklist->air_temperature)}}">
                                                    @error('air_temperature')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="air_throttling_valve" class="form-label form-label-sm">Дроссель, %
                                                    </label>
                                                    <input name="air_throttling_valve" id="air_throttling_valve"
                                                           class="form-control form-control-sm"  type="number" placeholder="80" value="{{old('air_throttling_valve', $checklist->air_throttling_valve)}}">
                                                    @error('air_throttling_valve')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row row-cols-1 mb-2">
                                                <div class="col-12">
                                                    <label for="air_outlet_temperature" class="form-label form-label-sm">Комментарий
                                                    </label>
                                                    <input name="comment" id="comment"
                                                           class="form-control form-control-sm" placeholder="Любой комментарий" type="text" value="{{old('comment', $checklist->comment)}}">
                                                    @error('comment')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-3">
                                        <div class="col">
                                            <div class="input-group input-group-sm">
                                                <a href="{{route('checklists_air_duct.index')}}"
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
                        <p>Если в списках нет Вашего ТРК, помещения.</p>
                        <p>создайте их через раздел Архитектура - Трк/Помещения.</p>
                        <p>С названиями систем и оборудования тоже самое.</p>
                        <p>Не надо проверять на совпадения названий, за Вас это сделает сервер.</p>
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
