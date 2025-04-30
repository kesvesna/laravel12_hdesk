@extends('layouts.backend.main')

@section('title', 'Главная | Чеклист притока создание')

@section('content')
    @csrf
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Заполнение чеклиста приточной установки</h4>
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
                            <form action="{{route('checklists_air_supply.store_from_trk_equipment', $trk_equipment)}}" method="post">
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
                                                                class="form-select form-select-sm">
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
                                                    <label for="outside_air_t" class="form-label form-label-sm">Температура на улице, град
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <input required name="outside_air_t" id="outside_air_t"
                                                           class="form-control form-control-sm" placeholder="18.4" type="number" step="0.1" value="{{old('outside_air_t')}}">
                                                    @error('outside_air_t')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="setpoint_air_t" class="form-label form-label-sm">Уставка притока, град
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <input required name="setpoint_air_t" id="setpoint_air_t"
                                                           class="form-control form-control-sm" placeholder="20.4" type="number" step="0.1" value="{{old('setpoint_air_t')}}">
                                                    @error('setpoint_air_t')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="supply_air_t" class="form-label form-label-sm">Т притока, град
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <input required name="supply_air_t" id="supply_air_t"
                                                           class="form-control form-control-sm" placeholder="21.0" type="number" step="0.1" value="{{old('supply_air_t')}}">
                                                    @error('supply_air_t')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="supply_engine_t" class="form-label form-label-sm">Т двигателя притока, град
                                                    </label>
                                                    <input name="supply_engine_t" id="supply_engine_t"
                                                           class="form-control form-control-sm" placeholder="50" type="number" value="{{old('supply_engine_t')}}">
                                                    @error('supply_engine_t')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="front_bearing_t" class="form-label form-label-sm">Т переднего подшипника, град
                                                    </label>
                                                    <input name="front_bearing_t" id="front_bearing_t"
                                                           class="form-control form-control-sm" placeholder="22.6" type="number" step="0.1" value="{{old('front_bearing_t')}}">
                                                    @error('front_bearing_t')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="supply_engine_terminal_contact_t" class="form-label form-label-sm">Т контактов в коробке двигателя, град
                                                    </label>
                                                    <input name="supply_engine_terminal_contact_t" id="supply_engine_terminal_contact_t"
                                                           class="form-control form-control-sm" placeholder="20" type="number" value="{{old('supply_engine_terminal_contact_t')}}">
                                                    @error('supply_engine_terminal_contact_t')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="service_switch_contact_t" class="form-label form-label-sm">Т контактов сервисного выключателя, град
                                                    </label>
                                                    <input name="service_switch_contact_t" id="service_switch_contact_t"
                                                           class="form-control form-control-sm" placeholder="20" type="number" value="{{old('service_switch_contact_t')}}">
                                                    @error('service_switch_contact_t')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="supply_engine_actual_current" class="form-label form-label-sm">Ток двигателя по факту, А
                                                    </label>
                                                    <input name="supply_engine_actual_current" id="supply_engine_actual_current"
                                                           class="form-control form-control-sm" placeholder="7.6" type="number" step="0.1" value="{{old('supply_engine_actual_current')}}">
                                                    @error('supply_engine_actual_current')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="supply_engine_passport_current" class="form-label form-label-sm">Ток двигателя по паспорту, А
                                                    </label>
                                                    <input name="supply_engine_passport_current" id="supply_engine_passport_current"
                                                           class="form-control form-control-sm" placeholder="9.2" type="number" step="0.1" value="{{old('supply_engine_passport_current', $trk_equipment->checklists_air_supply->last()->supply_engine_passport_current ?? null)}}">
                                                    @error('supply_engine_passport_current')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="supply_engine_actual_frequency" class="form-label form-label-sm">Частота двигателя по факту, Гц
                                                    </label>
                                                    <input name="supply_engine_actual_frequency" id="supply_engine_actual_frequency"
                                                           class="form-control form-control-sm" placeholder="45" type="number" value="{{old('supply_engine_actual_frequency')}}">
                                                    @error('supply_engine_actual_frequency')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="supply_engine_passport_frequency" class="form-label form-label-sm">Частота двигателя по паспорту, Гц
                                                    </label>
                                                    <input name="supply_engine_passport_frequency" id="supply_engine_passport_frequency"
                                                           class="form-control form-control-sm" placeholder="50" type="number" value="{{old('supply_engine_passport_frequency', $trk_equipment->checklists_air_supply->last()->supply_engine_passport_frequency ?? null)}}">
                                                    @error('supply_engine_passport_frequency')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="supply_air_actual_rate" class="form-label form-label-sm">Расход воздуха по факту, куб.м/час
                                                    </label>
                                                    <input name="supply_air_actual_rate" id="supply_air_actual_rate"
                                                           class="form-control form-control-sm" placeholder="800" type="number" value="{{old('supply_air_actual_rate')}}">
                                                    @error('supply_air_actual_rate')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="supply_air_passport_rate" class="form-label form-label-sm">Расход воздуха по паспорту, куб.м/час
                                                    </label>
                                                    <input name="supply_air_passport_rate" id="supply_air_passport_rate"
                                                           class="form-control form-control-sm" placeholder="1000" type="number" value="{{old('supply_air_passport_rate', $trk_equipment->checklists_air_supply->last()->supply_air_passport_rate ?? null)}}">
                                                    @error('supply_air_passport_rate')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="hot_water_valve_open_percent" class="form-label form-label-sm">Клапан ГВС, %
                                                    </label>
                                                    <input name="hot_water_valve_open_percent" id="hot_water_valve_open_percent"
                                                           class="form-control form-control-sm" placeholder="14.3" type="number" step="0.1" value="{{old('hot_water_valve_open_percent')}}">
                                                    @error('hot_water_valve_open_percent')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="inlet_hot_water_t" class="form-label form-label-sm">Т ГВС на входе, град
                                                    </label>
                                                    <input name="inlet_hot_water_t" id="inlet_hot_water_t"
                                                           class="form-control form-control-sm" placeholder="70" type="number" value="{{old('inlet_hot_water_t')}}">
                                                    @error('inlet_hot_water_t')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="outlet_hot_water_t" class="form-label form-label-sm">Т ГВС на выходе, град
                                                    </label>
                                                    <input name="outlet_hot_water_t" id="outlet_hot_water_t"
                                                           class="form-control form-control-sm" placeholder="40" type="number" value="{{old('outlet_hot_water_t')}}">
                                                    @error('outlet_hot_water_t')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="cold_water_valve_open_percent" class="form-label form-label-sm">Клапан ХВС, %
                                                    </label>
                                                    <input name="cold_water_valve_open_percent" id="cold_water_valve_open_percent"
                                                           class="form-control form-control-sm" placeholder="25" type="number" value="{{old('cold_water_valve_open_percent')}}">
                                                    @error('cold_water_valve_open_percent')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="inlet_cold_water_t" class="form-label form-label-sm">Т ХВС на входе, град
                                                    </label>
                                                    <input name="inlet_cold_water_t" id="inlet_cold_water_t"
                                                           class="form-control form-control-sm" placeholder="7" type="number" value="{{old('inlet_cold_water_t')}}">
                                                    @error('inlet_cold_water_t')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="outlet_cold_water_t" class="form-label form-label-sm">Т ХВС на выходе, град
                                                    </label>
                                                    <input name="outlet_cold_water_t" id="outlet_cold_water_t"
                                                           class="form-control form-control-sm" placeholder="15" type="number" value="{{old('outlet_cold_water_t')}}">
                                                    @error('outlet_cold_water_t')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="supply_air_dumper_open_percent" class="form-label form-label-sm">Заслонки притока, %
                                                    </label>
                                                    <input name="supply_air_dumper_open_percent" id="supply_air_dumper_open_percent"
                                                           class="form-control form-control-sm" placeholder="60" type="number" value="{{old('supply_air_dumper_open_percent')}}">
                                                    @error('supply_air_dumper_open_percent')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="recycle_air_dumper_open_percent" class="form-label form-label-sm">Заслонки рециркуляции, %
                                                    </label>
                                                    <input name="recycle_air_dumper_open_percent" id="recycle_air_dumper_open_percent"
                                                           class="form-control form-control-sm" placeholder="40" type="number" value="{{old('recycle_air_dumper_open_percent')}}">
                                                    @error('recycle_air_dumper_open_percent')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="recuperator_speed_rate_percent" class="form-label form-label-sm">Рекуператор, %
                                                    </label>
                                                    <input name="recuperator_speed_rate_percent" id="recuperator_speed_rate_percent"
                                                           class="form-control form-control-sm" placeholder="80" type="number" value="{{old('recuperator_speed_rate_percent')}}">
                                                    @error('recuperator_speed_rate_percent')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="hot_water_pump_actual_current" class="form-label form-label-sm">Ток насоса ГВС по факту, А
                                                    </label>
                                                    <input name="hot_water_pump_actual_current" id="hot_water_pump_actual_current"
                                                           class="form-control form-control-sm" placeholder="5.3" type="number" step="0.01" value="{{old('hot_water_pump_actual_current')}}">
                                                    @error('hot_water_pump_actual_current')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="hot_water_pump_passport_current" class="form-label form-label-sm">Ток насоса ГВС по паспорту, А
                                                    </label>
                                                    <input name="hot_water_pump_passport_current" id="hot_water_pump_passport_current"
                                                           class="form-control form-control-sm" placeholder="6.7" type="number" step="0.01" value="{{old('hot_water_pump_passport_current', $trk_equipment->checklists_air_supply->last()->hot_water_pump_passport_current ?? null)}}">
                                                    @error('hot_water_pump_passport_current')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="glycol_pump_actual_current" class="form-label form-label-sm">Ток гликолевого насоса по факту, А
                                                    </label>
                                                    <input name="glycol_pump_actual_current" id="glycol_pump_actual_current"
                                                           class="form-control form-control-sm" placeholder="4.3" type="number" step="0.01" value="{{old('glycol_pump_actual_current')}}">
                                                    @error('glycol_pump_actual_current')
                                                    <div class="text-danger">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="row row-cols-1 mb-3">
                                                <div class="col-12 col-md-3">
                                                    <label for="glycol_pump_passport_current" class="form-label form-label-sm">Ток гликолевого насоса по паспорту, А
                                                    </label>
                                                    <input name="glycol_pump_passport_current" id="glycol_pump_passport_current"
                                                           class="form-control form-control-sm" placeholder="5.7" type="number" step="0.01" value="{{old('glycol_pump_passport_current', $trk_equipment->checklists_air_supply->last()->glycol_pump_passport_current ?? null)}}">
                                                    @error('glycol_pump_passport_current')
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
                                                <a href="{{route('checklists_air_supply.index')}}"
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
        <script src="{{asset('assets/js/checklists/air_supply/add_equipment.js')}}"></script>
        <script src="{{asset('assets/js/checklists/air_supply/delete_equipment.js')}}"></script>
        <script src="{{asset('assets/js/checklists/air_supply/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/checklists/air_supply/delete_executor.js')}}"></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
@endsection
