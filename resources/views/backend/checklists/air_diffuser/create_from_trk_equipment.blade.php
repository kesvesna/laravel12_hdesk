@extends('layouts.backend.main')

@section('title', 'Главная | Чеклист диффузоров создание')

@section('content')
    @csrf
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">{{$trk_equipment->equipment_name->name . ' - '}} заполнение чеклиста диффузоров</h4>
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
                            <form action="{{route('checklists_air_diffuser.store_from_trk_equipment', $trk_equipment)}}" method="post">
                                @csrf
                                @method('post')
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
                                    <div class="equipment-add-div p-2 my-3 rounded"
                                         style="background-color: rgba(218, 117, 255, 0.2)">
                                        <p><b>Где выполняются замеры:</b></p>
                                        <div class="row">
                                            <div class="col mb-3">
                                                <label for="building_id_2" class="form-label form-label-sm">Блок/Зона<span
                                                        class="text-danger"><b>*</b></span></label>
                                                <select required name="building_id_2" class="form-select form-select-sm"
                                                        id="building_id_2">
                                                    @forelse($buildings as $building)
                                                        <option value="{{$building->id}}" {{old('building_id_2') == $building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                                        <option value="{{$floor->id}}" {{old('floor_id_2') == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                    @empty
                                                        <option value="">нет данных ...</option>
                                                    @endforelse
                                                </select>
                                                @error('floor_id_2')
                                                <div class="text-danger">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <div class="col mb-4">
                                                <label for="room_id_2" class="form-label form-label-sm">Помещение (где замеры)<span
                                                        class="text-danger"><b>*</b></span></label>
                                                <select required name="room_id_2" class="form-select form-select-sm"
                                                        id="room_id_2">
                                                    @forelse($rooms as $room)
                                                        <option value="{{$room->id}}" {{old('room_id_2') == $room->id ? 'selected' : null}}>{{$room->name}}</option>
                                                    @empty
                                                        <option value="">нет данных ...</option>
                                                    @endforelse
                                                </select>
                                                @error('room_id_2')
                                                <div class="text-danger">{{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="equipment-add-div p-2 my-3 rounded"
                                         style="background-color: rgba(145, 135, 255, 0.2)">
                                        <p><b>Где находится установка источник воздухообмена:</b></p>
                                        <div class="row row-cols-1 row-cols-md-5">
                                            <div class="col mb-3">
                                                <label for="trk_room_id" class="form-label form-label-sm">ТРК</label>
                                                <input readonly disabled value="{{$trk_equipment->trk_room->trk->name}}"
                                                       class="form-control form-control-sm">
                                            </div>
                                            <div class="col mb-3">
                                                <label for="building_id" class="form-label form-label-sm">Блок/Зона</label>
                                                <input readonly disabled value="{{$trk_equipment->trk_room->building->name}}"
                                                       class="form-control form-control-sm">
                                            </div>
                                            <div class="col mb-3">
                                                <label for="floor_id" class="form-label form-label-sm">Этаж/Отметка</label>
                                                <input readonly disabled value="{{$trk_equipment->trk_room->floor->name}}"
                                                       class="form-control form-control-sm">
                                            </div>
                                            <div class="col mb-3">
                                                <label for="room_id" class="form-label form-label-sm">Помещение</label>
                                                <input readonly disabled value="{{$trk_equipment->trk_room->room->name}}"
                                                       class="form-control form-control-sm">
                                            </div>
                                            <div class="col mb-3">
                                                <label for="room_id" class="form-label form-label-sm">Оборудование</label>
                                                <input readonly disabled value="{{$trk_equipment->equipment_name->name}}"
                                                       class="form-control form-control-sm">
                                            </div>
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
                                    <div class="row row-cols-1 mb-2">
                                        <div class="col-12">
                                            <label for="comment" class="form-label form-label-sm">Комментарии (для акта)
                                            </label>
                                            <div class="input-group input-group-sm mb-3">
                                                <textarea class="form-control form-control-sm" name="comment" placeholder="Любые комментарии (необязательно)"></textarea>
                                            </div>
                                            @error('comment')
                                            <div class="text-danger"
                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <!-- Button trigger modal responsibility -->
                                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal2">Расшифровка обозначений</button>
                                        </div>
                                        <div class="table-responsive mt-3">
                                            <table class="table table-striped table-sm table-bordered" id="equipment_table">
                                                <thead class="table-dark">
                                                <tr>
                                                    <th>Номер&nbsp;точки&nbsp;замера</th>
                                                    <th>Тип&nbsp;точки&nbsp;замера</th>
                                                    <th>Длина/&nbsp;Диаметр,&nbsp;мм</th>
                                                    <th>Ширина,&nbsp;мм</th>
                                                    <th>V_air,&nbsp;м/&nbsp;сек</th>
                                                    <th>S,&nbsp;кв.м</th>
                                                    <th>Q_air,&nbsp;куб.м/&nbsp;час</th>
                                                    <th>P_air,&nbsp;Па</th>
                                                    <th>T_air,&nbsp;град</th>
                                                    <th>Дроссель,&nbsp;%</th>
                                                    <th>Комментарий</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr class="equipment-add-tr">
                                                    <td>
                                                        <div class="input-group">
                                                            <img class="input-group-text equipment-add-button" src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="add" title="Добавить" height="30">
                                                            <img class="input-group-text equipment-delete-button" src="{{asset('assets/images/backend/svg/trash3.svg')}}" alt="delete" title="Удалить" height="30">
                                                            <select required name="equipments[0][measuring_point_number]" class="form-select form-select-sm measuring_point_number equipment-name-input">
                                                                @for($i = 1; $i <= 25; $i++)
                                                                    <option value="{{$i}}">{{$i}}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <select required name="equipments[0][air_direction_type]" class="form-select form-select-sm air_direction_type">
                                                            <option value="0">Приток</option>
                                                            <option value="1">Вытяжка</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input required class="form-control form-control-sm length_or_diameter" name="equipments[0][length_or_diameter]" type="number" placeholder="200" min="0" max="10000">
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm width" name="equipments[0][width]" type="number" placeholder="200" min="0" max="10000">
                                                    </td>
                                                    <td>
                                                        <input required class="form-control form-control-sm air_speed" name="equipments[0][air_speed]" type="number" step="0.1" placeholder="3.5" min="0" max="30.0">
                                                    </td>
                                                    <td>
                                                        <input required class="form-control form-control-sm estimated_coefficient" name="equipments[0][estimated_coefficient]" type="number" step="0.1" placeholder="0.8" value="1.0">
                                                    </td>
                                                    <td>
                                                        <input required class="form-control form-control-sm diffuser_cross_sectional_area" name="equipments[0][diffuser_cross_sectional_area]" type="number" step="0.001" placeholder="0.123" min="0" max="10.0">
                                                    </td>
                                                    <td>
                                                        <input required class="form-control form-control-sm air_flow_rate" name="equipments[0][air_flow_rate]" type="number" placeholder="900" min="0" max="500000">
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm air_pressure" name="equipments[0][air_pressure]" type="number" placeholder="25" min="0" max="1000">
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm air_temperature" name="equipments[0][air_temperature]" type="number" step="0.1" placeholder="21.0" min="0" max="100">
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm air_throttling_valve" name="equipments[0][air_throttling_valve]" type="number" placeholder="80" min="0" max="100">
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm comment" name="equipments[0][comment]" placeholder="Комментарий" type="text">
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="executors-add-parent-div mt-4">
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
                                                <a href="{{route('checklists_air_diffuser.index')}}"
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
                        <h1 class="modal-title fs-5" id="exampleModalLabel2">Параметры в таблице</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>V_air - скорость воздуха</p>
                        <p>Коэф. - расчетный коэффициент, зависит от типа диффузора</p>
                        <p>S - площадь поперечного сечения воздуховода</p>
                        <p>Q_air - расход воздуха</p>
                        <p>P_air - давление воздуха</p>
                        <p>T_air - температура воздуха</p>
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
        <script src="{{asset('assets/js/checklists/air_diffuser/add_equipment.js')}}"></script>
        <script src="{{asset('assets/js/checklists/air_diffuser/delete_equipment.js')}}"></script>
        <script src="{{asset('assets/js/checklists/air_diffuser/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/checklists/air_diffuser/delete_executor.js')}}"></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
@endsection
