@extends('layouts.backend.main')

@section('title', 'Главная | Чеклист балки создание')

@section('content')
    @csrf
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Заполнение чеклиста балки</h4>
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
                            <form action="{{route('checklists_balk.store_from_trk_equipment', $trk_equipment)}}" method="post">
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
                                    <div class="row row-cols-1 row-cols-md-4">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required readonly type="text"
                                                   class="form-control form-control-sm"
                                                   placeholder="Начните писать ..."
                                                   name="trk_id" value="{{$trk_equipment->trk_room->trk->name}}">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="system_id" class="form-label form-label-sm">Система <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required readonly type="text"
                                                   class="form-control form-control-sm"
                                                   placeholder="Начните писать ..."
                                                   name="system_id" value="{{$trk_equipment->system->name}}">
                                        </div>
                                        <div class="col mb-4">
                                            <label for="room_id" class="form-label form-label-sm">Помещение <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required readonly type="text"
                                                   class="form-control form-control-sm"
                                                   placeholder="Начните писать ..."
                                                   name="room_id" value="{{$trk_equipment->trk_room->room->name}}">
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
                                                                <option value="{{$trk_equipment->equipment_name->id}}" {{old('equipment_id') == $trk_equipment->equipment_name->id ? 'selected' : null}}>{{$trk_equipment->equipment_name->name}}</option>
                                                        </select>
                                                    </div>
                                                    @error('equipment_id')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="air_inlet_temperature" class="form-label form-label-sm">Типоразмер
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <input required name="balk_size_type" id="balk_size_type"
                                                           class="form-control form-control-sm" placeholder="300" type="number" value="{{old('balk_size_type')}}">
                                                    @error('balk_size_type')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="air_speed" class="form-label form-label-sm">Скорость воздуха, м/сек
                                                    </label>
                                                    <input name="air_speed" id="air_speed"
                                                           class="form-control form-control-sm" placeholder="3.6" type="number" step="0.1" value="{{old('air_speed')}}">
                                                    @error('air_speed')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="air_flow_rate" class="form-label form-label-sm">Расход воздуха, куб.м/час
                                                    </label>
                                                    <input name="air_flow_rate" id="air_flow_rate"
                                                           class="form-control form-control-sm" placeholder="85.0" type="number" step="0.1" value="{{old('air_flow_rate')}}">
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
                                                           class="form-control form-control-sm" placeholder="23.0" type="number" step="0.1" value="{{old('air_pressure')}}">
                                                    @error('air_pressure')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
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
                                                <div class="col-12 col-md-3">
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
                                                <div class="col-12 col-md-3">
                                                    <label for="air_flap" class="form-label form-label-sm">Створки
                                                    </label>
                                                    <input name="air_flap" id="air_flap"
                                                           class="form-control form-control-sm" placeholder="24/24" type="text" value="{{old('air_flap')}}">
                                                    @error('air_flap')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="cold_water_inlet_temperature" class="form-label form-label-sm">Т воды на входе
                                                    </label>
                                                    <input name="cold_water_inlet_temperature" id="cold_water_inlet_temperature"
                                                           class="form-control form-control-sm" placeholder="7.6" type="number" step="0.1" value="{{old('cold_water_inlet_temperature')}}">
                                                    @error('cold_water_inlet_temperature')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="cold_water_outlet_temperature" class="form-label form-label-sm">Т воды на выходе
                                                    </label>
                                                    <input name="cold_water_outlet_temperature" id="cold_water_outlet_temperature"
                                                           class="form-control form-control-sm" placeholder="14.3" type="number" step="0.1" value="{{old('cold_water_outlet_temperature')}}">
                                                    @error('cold_water_outlet_temperature')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="cold_water_pressure_drop" class="form-label form-label-sm">Разница давления воды
                                                    </label>
                                                    <input name="cold_water_pressure_drop" id="cold_water_pressure_drop"
                                                           class="form-control form-control-sm" placeholder="1.1" type="number" step="0.1" value="{{old('cold_water_pressure_drop')}}">
                                                    @error('cold_water_pressure_drop')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="cold_water_valve" class="form-label form-label-sm">Клапан ХВС
                                                    </label>
                                                    <input name="cold_water_valve" id="cold_water_valve"
                                                           class="form-control form-control-sm" placeholder="10.1" type="number" step="0.1" value="{{old('cold_water_valve')}}">
                                                    @error('cold_water_valve')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="cold_water_rate" class="form-label form-label-sm">Расход ХВС
                                                    </label>
                                                    <input name="cold_water_rate" id="cold_water_rate"
                                                           class="form-control form-control-sm" placeholder="5.6" type="number" step="0.1" value="{{old('cold_water_rate')}}">
                                                    @error('cold_water_rate')
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
                                                <a href="{{route('checklists_balk.index')}}"
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
        <script src="{{asset('assets/js/checklists/balk/add_equipment.js')}}"></script>
        <script src="{{asset('assets/js/checklists/balk/delete_equipment.js')}}"></script>
        <script src="{{asset('assets/js/checklists/balk/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/checklists/balk/delete_executor.js')}}"></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
@endsection
