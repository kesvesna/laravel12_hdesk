@extends('layouts.backend.main')

@section('title', 'Главная | Чеклист приточной установки')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Чеклист приточной установки</h4><a href="{{route('checklists_air_supply.create')}}"><img
                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add" title="Добавить"
                                height="30"></a>
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
                            <div class="card shadow p-3">
                                <ul class="list-group mb-4">
                                    <li class="list-group-item"><b>Дата: </b>{{$checklist->created_at ?? 'не выбрано'}}</li>
                                    <li class="list-group-item">
                                        <b>ТРК: </b>{{$checklist->trk_equipment->trk_room->trk->name ?? 'не выбрано'}}</li>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Помещение: </b><a href="{{route('trk_room.show', $checklist->trk_equipment->trk_room->id)}}">{{$checklist->trk_equipment->trk_room->room->name ?? 'не выбрано'}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Оборудование: </b><a href="{{route('trk_equipments.show', $checklist->trk_equipment->id)}}">{{$checklist->trk_equipment->equipment_name->name ?? 'не выбрано'}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Т на улице: </b>{{$checklist->outside_air_t ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Уставка притока: </b>{{$checklist->setpoint_air_t ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Т притока: </b>{{$checklist->supply_air_t ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Т двигателя притока: </b>{{$checklist->supply_engine_t ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Т переднего подшипника: </b>{{$checklist->front_bearing_t ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Т клемм двигателя: </b>{{$checklist->supply_engine_terminal_contact_t ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Т клемм сервисного выключателя: </b>{{$checklist->service_switch_contact_t ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Ток двигателя факт: </b>{{$checklist->supply_engine_actual_current ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Ток двигателя паспорт: </b>{{$checklist->supply_engine_passport_current ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Частота двигателя факт: </b>{{$checklist->supply_engine_actual_frequency ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Частота двигателя паспорт: </b>{{$checklist->supply_engine_passport_frequency ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Расход воздуха факт: </b>{{$checklist->supply_air_actual_rate ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Расход воздуха паспорт: </b>{{$checklist->supply_air_passport_rate ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Клапан ГВС: </b>{{$checklist->hot_water_valve_open_percent ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Т ГВС на входе: </b>{{$checklist->inlet_hot_water_t ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Т ГВС на выходе: </b>{{$checklist->outlet_hot_water_t ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Клапан ХВС: </b>{{$checklist->cold_water_valve_open_percent ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Т ХВС на входе: </b>{{$checklist->inlet_cold_water_t ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Т ХВС на выходе: </b>{{$checklist->outlet_cold_water_t ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Заслонки притока: </b>{{$checklist->supply_air_dumper_open_percent ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Заслонки рециркуляции: </b>{{$checklist->recycle_air_dumper_open_percent ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Рекуператор: </b>{{$checklist->recuperator_speed_rate_percent ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Ток насоса ГВС факт: </b>{{$checklist->hot_water_pump_actual_current ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Ток насоса ГВС паспорт: </b>{{$checklist->hot_water_pump_passport_current ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Ток гликолевого насоса ГВС факт: </b>{{$checklist->glycol_pump_actual_current ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Ток гликолевого насоса ГВС паспорт: </b>{{$checklist->glycol_pump_passport_current ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Комментарий: </b>{{$checklist->comment ?? 'не выбрано'}}
                                    </li>
                                    @if(!empty($checklist->avr) && count($checklist->avr) > 0)
                                    <li class="list-group-item">
                                        @forelse($checklist->avr as $checklist_avr)
                                            <b>Выполнено: </b>
                                            @foreach($checklist_avr->avr_works as $avr_work)
                                                {{$avr_work->work_name->name . ', '}}
                                            @endforeach                                        @empty
                                            <span>нет данных ...</span>
                                        @endforelse
                                    </li>
                                    <li class="list-group-item">
                                        <a href="{{route('avrs.show', $checklist->avr->first()->id)}}">Акт выполненных работ</a>
                                    </li>
                                    @endif
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back()"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit') || auth()->id() == $checklist->author_id)
                                        <a href="{{route('checklists_air_supply.edit', $checklist->id)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('checklists_air_supply.destroy', $checklist)}}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-outline-danger"><img
                                                    src="{{asset('assets/images/backend/svg/trash3.svg')}}" alt="delete"
                                                    title="Удалить"></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
