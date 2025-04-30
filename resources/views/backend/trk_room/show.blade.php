@extends('layouts.backend.main')

@section('title', 'Главная | ТРК/Помещения')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">ТРК/Помещения</h4>
                        @if(auth()->user()->can('trk_room create') || Auth::user()->hasRole('sadmin'))
                        <a href="{{route('trk_room.create')}}"><img
                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add" title="Добавить"
                                height="30"></a>
                        @endif
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
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped table-hover shadow rounded table-bordered">
                                        <thead>
                                            <tr>
                                                <th>Помещение</th>
                                                <th>Площадь</th>
                                                <th>Арендатор</th>
                                                <th>Назначение</th>
                                                <th>Обход</th>
                                                <th>Этаж</th>
                                                <th>Блок</th>
                                                <th>Комментарий</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th class="text-nowrap">{{$trk_room->room->name}}</th>
                                                <td class="text-nowrap">{{$trk_room->square}}</td>
                                                <td class="text-nowrap">{{$trk_room->renter?->brand->name ?? 'отсутствует'}}</td>
                                                <td class="text-nowrap">{{$trk_room->room_purpose->name}}</td>
                                                <td class="text-nowrap">{{$trk_room->need_daily_checking ? 'да' : 'нет'}}</td>
                                                <td class="text-nowrap">{{$trk_room->floor->name}}</td>
                                                <td class="text-nowrap">{{$trk_room->building->name}}</td>
                                                <td class="text-nowrap">{{$trk_room->comment}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    @if(!isset($trk_room->renter->id))
                                        @if(auth()->user()->can('renter_trk_room_brand create') || Auth::user()->hasRole('sadmin'))
                                            <a href="{{route('renter_trk_room_brands.create_from_trk_room', $trk_room->id)}}"
                                               class="btn btn-outline-warning btn-sm">
                                                <img src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit" style="height: 20px;" title="Редактировать">
                                            </a>
                                        @endif
                                    @endif
                                </div>
                                <ul class="list-group mb-3 mt-3">
                                    <div class="accordion accordion-flush" id="accordionFlushExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne234">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne234"
                                                        aria-expanded="false" aria-controls="flush-collapseOne234">
                                                    Источники (воздух, холод, тепло и т.д.):
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne234" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne234"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-striped table-hover">
                                                            <thead>
                                                                <tr>
                                                                    <th>Оборудование</th>
                                                                    <th>Система</th>
                                                                    <th>Помещение</th>
                                                                    <th>Этаж</th>
                                                                    <th>Кто обслуживает</th>
                                                                    <th>Комментарий</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            @forelse($trk_room->sources as $source)
                                                                <tr style="cursor: pointer;" onclick="window.location='{{route('trk_equipments.show', $source->trk_equipment->id)}}';">
                                                                    <td class="text-nowrap">{{$source->trk_equipment->equipment_name->name}}</td>
                                                                    <td class="text-nowrap">{{$source->trk_equipment->system->name}}</td>
                                                                    <td class="text-nowrap">{{$source->trk_equipment->trk_room->room->name}}</td>
                                                                    <td class="text-nowrap">{{$source->trk_equipment->trk_room->floor->name}}</td>
                                                                    <td class="text-nowrap">{{$source->trk_equipment->responsible_division?->name ?? 'не заполнено ...'}}</td>
                                                                    <td class="text-nowrap">{{$source->comment}}</td>
                                                                </tr>
                                                            @empty
                                                                <td colspan="6">не заполнено ...</td>
                                                            @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @if(auth()->user()->can('equipment_user create') || Auth::user()->hasRole('sadmin'))
                                                    <a href="{{route('equipment_users.create_from_trk_room', $trk_room->id)}}"
                                                       class="btn btn-outline-warning btn-sm">
                                                        <img src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit" style="height: 20px;" title="Редактировать">
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                                                        aria-expanded="false" aria-controls="flush-collapseOne">
                                                    Оборудование внутри этого помещения: {{count($trk_room->equipments) > 0 ? count($trk_room->equipments) . ' шт.' : '0 шт.'}}
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <ul class="list-group mb-3">
                                                        @forelse($trk_room->equipments as $equipment)
                                                            <li class="list-group-item"><a class="nav-link"
                                                                                           href="{{route('trk_equipments.show', $equipment->id)}}"><span
                                                                        class="border-bottom-double">{{$equipment->equipment_name->name . ' (' . $equipment->system->name . ')'}}</span></a>
                                                            </li>
                                                        @empty
                                                            не заполнено ...
                                                        @endforelse
                                                    </ul>
                                                    @if(auth()->user()->can('trk_equipment create') || Auth::user()->hasRole('sadmin'))
                                                    <a href="{{route('trk_equipments.create_from_trk_room', $trk_room->id)}}"
                                                       class="btn btn-outline-warning btn-sm">
                                                        <img src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit" style="height: 20px;" title="Редактировать">
                                                    </a>
                                                    @endif
                                                    @if(auth()->user()->hasRole('sadmin'))
                                                        <br>
                                                        <button type="button" class="btn btn-outline-danger rounded btn-sm mt-4" data-bs-toggle="modal"
                                                                data-bs-target="#exampleModal4">Привязать все оборудование к другому помещению
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne98">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne98"
                                                        aria-expanded="false" aria-controls="flush-collapseOne98">
                                                    Акты выполненных работ: {{count($trk_room->avrs) > 0 ? count($trk_room->avrs) . ' шт.' : '0 шт.'}}
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne98" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne98"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">

                                                    <iframe class="rounded mb-3" style="width: 100%; height: 100vh;" src="{{route('avrs.index_frame', ['trk_room_id' => $trk_room->id])}}">
                                                    </iframe>

                                                    @if(auth()->user()->can('trk_room_avr create') || Auth::user()->hasRole('sadmin'))
                                                        <a href="{{route('avrs.create_from_trk_room', $trk_room->id)}}"
                                                           class="btn btn-outline-warning btn-sm">
                                                            <img src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit" style="height: 20px;" title="Редактировать">
                                                        </a>
                                                    @endif
                                                    @if(auth()->user()->hasRole('sadmin'))
                                                        <br>
                                                        <button type="button" class="btn btn-outline-danger rounded btn-sm mt-4" data-bs-toggle="modal"
                                                                data-bs-target="#exampleModal3">Привязать все акты к другому помещению
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne6">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne6"
                                                        aria-expanded="false" aria-controls="flush-collapseOne6">
                                                    Климат:
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne6" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne6"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <ul class="list-group mb-3">
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-striped table-hover">
                                                                <thead>
                                                                <tr>
                                                                    <th>Дата</th>
                                                                    <th>На улице</th>
                                                                    <th>В помещении</th>
                                                                    <th>На притоке</th>
                                                                    <th>Комментарий</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                @forelse($trk_room->climates as $climate)
                                                                    <tr style="cursor: pointer;" onclick="window.location='{{ route('trk_room_climates.show', $climate->id) }}';">
                                                                        <td>{{$climate->created_at}}</td>
                                                                        <td>{{$climate->t_outside}}</td>
                                                                        <td>{{$climate->t_inside}}</td>
                                                                        <td>{{$climate->t_supply_air}}</td>
                                                                        <td>{{$climate->comment}}</td>
                                                                    </tr>
                                                                @empty
                                                                    <td colspan="5">нет данных ...</td>
                                                                @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </ul>
                                                    @if(auth()->user()->can('trk_room_climate create') || auth()->user()->hasRole('sadmin'))
                                                    <a href="{{route('trk_room_climates.create_from_trk_room', $trk_room->id)}}"
                                                       class="btn btn-outline-warning btn-sm">
                                                        <img src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit" style="height: 20px;" title="Редактировать">
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne78">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne78"
                                                        aria-expanded="false" aria-controls="flush-collapseOne78">
                                                    Чеклисты:
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne78" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne78"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <ul class="list-group mb-3">
                                                        <li class="list-group-item">
                                                                <h5 class="mt-2">Кондиционеры</h5>
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm table-striped table-hover">
                                                                        <thead>
                                                                            <tr>
                                                                                <th>Дата</th>
                                                                                <th>Кондиционер</th>
                                                                                <th>Воздух на входе</th>
                                                                                <th>Воздух на выходе</th>
                                                                                <th>Комментарий</th>
                                                                            </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @forelse($trk_room->checklists_conditioner as $checklist_conditioner)
                                                                            <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_conditioner.show', $checklist_conditioner->id) }}';">
                                                                                <td>{{$checklist_conditioner->created_at}}</td>
                                                                                <td>{{$checklist_conditioner->trk_equipment->equipment_name->name}}</td>
                                                                                <td>{{$checklist_conditioner->air_inlet_temperature}}</td>
                                                                                <td>{{$checklist_conditioner->air_outlet_temperature}}</td>
                                                                                <td>{{$checklist_conditioner->comment}}</td>
                                                                            </tr>
                                                                        @empty
                                                                            <td colspan="5">нет данных ...</td>
                                                                        @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @if(auth()->user()->can('checklist_conditioner create') || auth()->user()->hasRole('sadmin'))
                                                                <a href="{{route('checklists_conditioner.create_from_trk_room', $trk_room->id)}}"
                                                                   class="btn btn-outline-success btn-sm mb-2">Добавить чеклист кондиционера</a>
                                                            @endif
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h5 class="mt-2">Фанкойлы</h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-striped table-hover">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Дата</th>
                                                                        <th>Фанкойл</th>
                                                                        <th>Воздух на входе</th>
                                                                        <th>Воздух на выходе</th>
                                                                        <th>Вода на входе</th>
                                                                        <th>Вода на выходе</th>
                                                                        <th>Комментарий</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @forelse($trk_room->checklists_fancoil as $checklist_fancoil)
                                                                        <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_fancoil.show', $checklist_fancoil->id) }}';">
                                                                            <td>{{$checklist_fancoil->created_at}}</td>
                                                                            <td>{{$checklist_fancoil->trk_equipment->equipment_name->name}}</td>
                                                                            <td>{{$checklist_fancoil->air_inlet_temperature}}</td>
                                                                            <td>{{$checklist_fancoil->air_outlet_temperature}}</td>
                                                                            <td>{{$checklist_fancoil->cold_water_inlet_temperature}}</td>
                                                                            <td>{{$checklist_fancoil->cold_water_outlet_temperature}}</td>
                                                                            <td>{{$checklist_fancoil->comment}}</td>
                                                                        </tr>
                                                                    @empty
                                                                        <td colspan="7">нет данных ...</td>
                                                                    @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            @if(auth()->user()->can('checklist_fancoil create') || auth()->user()->hasRole('sadmin'))
                                                                <a href="{{route('checklists_fancoil.create_from_trk_room', $trk_room->id)}}"
                                                                   class="btn btn-outline-success btn-sm mb-2">Добавить чеклист фанкойла</a>
                                                            @endif
                                                        </li>
                                                        <li class="list-group-item">
                                                            <h5 class="mt-2">Балки</h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-striped table-hover">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Дата</th>
                                                                        <th>Балка</th>
                                                                        <th>Типоразмер</th>
                                                                        <th>Скорость воздуха</th>
                                                                        <th>Расход воздуха</th>
                                                                        <th>Давление воздуха</th>
                                                                        <th>Воздух на входе</th>
                                                                        <th>Воздух на выходе</th>
                                                                        <th>Створки</th>
                                                                        <th>Вода на входе</th>
                                                                        <th>Вода на выходе</th>
                                                                        <th>Перепад давления ХВС</th>
                                                                        <th>Клапан ХВС</th>
                                                                        <th>Расход ХВС</th>
                                                                        <th>Комментарий</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @forelse($trk_room->checklists_balk as $checklist_balk)
                                                                        <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_balk.show', $checklist_balk->id) }}';">
                                                                            <td>{{$checklist_balk->created_at}}</td>
                                                                            <td>{{$checklist_balk->trk_equipment->equipment_name->name}}</td>
                                                                            <td>{{$checklist_balk->balk_size_type}}</td>
                                                                            <td>{{$checklist_balk->air_speed}}</td>
                                                                            <td>{{$checklist_balk->air_flow_rate}}</td>
                                                                            <td>{{$checklist_balk->air_pressure}}</td>
                                                                            <td>{{$checklist_balk->air_inlet_temperature}}</td>
                                                                            <td>{{$checklist_balk->air_outlet_temperature}}</td>
                                                                            <td>{{$checklist_balk->air_flap}}</td>
                                                                            <td>{{$checklist_balk->cold_water_inlet_temperature}}</td>
                                                                            <td>{{$checklist_balk->cold_water_outlet_temperature}}</td>
                                                                            <td>{{$checklist_balk->cold_water_pressure_drop}}</td>
                                                                            <td>{{$checklist_balk->cold_water_valve}}</td>
                                                                            <td>{{$checklist_balk->cold_water_rate}}</td>
                                                                            <td>{{$checklist_balk->comment}}</td>
                                                                        </tr>
                                                                    @empty
                                                                        <td colspan="15">нет данных ...</td>
                                                                    @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            @if(auth()->user()->can('checklist_balk create') || auth()->user()->hasRole('sadmin'))
                                                                <a href="{{route('checklists_balk.create_from_trk_room', $trk_room->id)}}"
                                                                   class="btn btn-outline-success btn-sm mb-2">Добавить чеклист балки</a>
                                                            @endif
                                                        </li>
                                                        @if(!is_null($checklists_air_supply))
                                                        <li class="list-group-item">
                                                            <h5 class="mt-2">Приточные установки</h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-striped table-hover">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Дата</th>
                                                                        <th>Установка</th>
                                                                        <th>Т на улице</th>
                                                                        <th>Уставка притока</th>
                                                                        <th>Т притока</th>
                                                                        <th>Клапан ГВС</th>
                                                                        <th>Клапан ХВС</th>
                                                                        <th>Комментарий</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @forelse($checklists_air_supply as $checklist_air_supply)
                                                                        <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_air_supply.show', $checklist_air_supply->id) }}';">
                                                                            <td>{{$checklist_air_supply->created_at}}</td>
                                                                            <td>{{$checklist_air_supply->trk_equipment->equipment_name->name}}</td>
                                                                            <td>{{$checklist_air_supply->outside_air_t}}</td>
                                                                            <td>{{$checklist_air_supply->setpoint_air_t}}</td>
                                                                            <td>{{$checklist_air_supply->supply_air_t}}</td>
                                                                            <td>{{$checklist_air_supply->hot_water_valve_open_percent}}</td>
                                                                            <td>{{$checklist_air_supply->cold_water_valve_open_percent}}</td>
                                                                            <td>{{$checklist_air_supply->comment}}</td>
                                                                        </tr>
                                                                    @empty
                                                                        <td colspan="8">нет данных ...</td>
                                                                    @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            @if(auth()->user()->can('checklist_air_supply create') || auth()->user()->hasRole('sadmin'))
                                                                <a href="{{route('checklists_air_supply.create_from_trk_room', $trk_room->id)}}"
                                                                   class="btn btn-outline-success btn-sm mb-2">Добавить чеклист приточной установки</a>
                                                            @endif
                                                        </li>
                                                        @else
{{--                                                            @if(auth()->user()->can('checklist_air_supply create') || auth()->user()->hasRole('sadmin'))--}}
{{--                                                                <a href="{{route('checklists_air_supply.create_from_trk_room', $trk_room->id)}}"--}}
{{--                                                                   class="btn btn-outline-success btn-sm col-3 mb-2 mt-3">Добавить чеклист приточной установки</a>--}}
{{--                                                            @endif--}}
                                                        @endif
                                                        @if(!is_null($checklists_air_extract))
                                                        <li class="list-group-item">
                                                            <h5 class="mt-2">Вытяжные установки</h5>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-striped table-hover">
                                                                    <thead>
                                                                    <tr>
                                                                        <th>Дата</th>
                                                                        <th>Вытяжка</th>
                                                                        <th>Т воздуха на вытяжке</th>
                                                                        <th>Ток двигателя факт</th>
                                                                        <th>Ток двигателя паспорт</th>
                                                                        <th>Комментарий</th>
                                                                    </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    @forelse($checklists_air_extract as $checklist_air_extract)
                                                                        <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_air_extract.show', $checklist_air_extract->id) }}';">
                                                                            <td>{{$checklist_air_extract->created_at}}</td>
                                                                            <td>{{$checklist_air_extract->trk_equipment->equipment_name->name}}</td>
                                                                            <td>{{$checklist_air_extract->extract_air_t}}</td>
                                                                            <td>{{$checklist_air_extract->extract_engine_actual_current}}</td>
                                                                            <td>{{$checklist_air_extract->extract_engine_passport_current}}</td>
                                                                            <td>{{$checklist_air_extract->comment}}</td>
                                                                        </tr>
                                                                    @empty
                                                                        <td colspan="6">нет данных ...</td>
                                                                    @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                            @if(auth()->user()->can('checklist_air_extract create') || auth()->user()->hasRole('sadmin'))
                                                                <a href="{{route('checklists_air_extract.create_from_trk_room', $trk_room->id)}}"
                                                                   class="btn btn-outline-success btn-sm mb-2">Добавить чеклист вытяжной установки</a>
                                                            @endif
                                                        </li>
                                                        @else
{{--                                                            @if(auth()->user()->can('checklist_air_extract create') || auth()->user()->hasRole('sadmin'))--}}
{{--                                                                <a href="{{route('checklists_air_extract.create_from_trk_room', $trk_room->id)}}"--}}
{{--                                                                   class="btn btn-outline-success btn-sm col-3 mb-2 mt-3">Добавить чеклист вытяжной установки</a>--}}
{{--                                                            @endif--}}
                                                        @endif
                                                        @if(!is_null($checklists_air_duct))
                                                            <li class="list-group-item">
                                                                <h5 class="mt-2">Воздуховод</h5>
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm table-striped table-hover">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>Дата</th>
                                                                            <th>Точка</th>
                                                                            <th>Тип</th>
                                                                            <th>Площадь</th>
                                                                            <th>Скорость</th>
                                                                            <th>Расход</th>
                                                                            <th>Температура</th>
                                                                            <th>Комментарий</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @forelse($checklists_air_duct as $checklist_air_duct)
                                                                            <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_air_duct.show', $checklist_air_duct->id) }}';">
                                                                                <td>{{$checklist_air_duct->created_at}}</td>
                                                                                <td>{{$checklist_air_duct->measuring_point_number}}</td>
                                                                                <td>{{$checklist_air_duct->air_direction_type == 0 ? 'приток' : 'вытяжка'}}</td>
                                                                                <td>{{$checklist_air_duct->duct_cross_sectional_area}}</td>
                                                                                <td>{{$checklist_air_duct->air_speed}}</td>
                                                                                <td>{{$checklist_air_duct->air_flow_rate}}</td>
                                                                                <td>{{$checklist_air_duct->air_temperature}}</td>
                                                                              <td>{{$checklist_air_duct->comment}}</td>
                                                                            </tr>
                                                                        @empty
                                                                            <td colspan="8">нет данных ...</td>
                                                                        @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                @if(auth()->user()->can('checklist_air_duct create') || auth()->user()->hasRole('sadmin'))
                                                                    <a href="{{route('checklists_air_duct.create_from_trk_room', $trk_room->id)}}"
                                                                       class="btn btn-outline-success btn-sm mb-2">Добавить чеклист воздуховода</a>
                                                                @endif
                                                            </li>
                                                        @else
                                                            @if(auth()->user()->can('checklist_air_duct create') || auth()->user()->hasRole('sadmin'))
                                                                <a href="{{route('checklists_air_duct.create_from_trk_room', $trk_room->id)}}"
                                                                   class="btn btn-outline-success btn-sm col-3 mb-2 mt-3">Добавить чеклист воздуховода</a>
                                                            @endif
                                                        @endif
                                                        @if(!is_null($checklists_air_diffuser))
                                                            <li class="list-group-item">
                                                                <h5 class="mt-2">Диффузоры</h5>
                                                                <div class="table-responsive">
                                                                    <table class="table table-sm table-striped table-hover">
                                                                        <thead>
                                                                        <tr>
                                                                            <th>Дата</th>
                                                                            <th>Точка</th>
                                                                            <th>Тип</th>
                                                                            <th>Площадь</th>
                                                                            <th>Скорость</th>
                                                                            <th>Коэффициент</th>
                                                                            <th>Расход</th>
                                                                            <th>Температура</th>
                                                                            <th>Комментарий</th>
                                                                        </tr>
                                                                        </thead>
                                                                        <tbody>
                                                                        @forelse($checklists_air_diffuser as $checklist_air_diffuser)
                                                                            <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_air_diffuser.show', $checklist_air_diffuser->id) }}';">
                                                                                <td>{{$checklist_air_diffuser->created_at}}</td>
                                                                                <td>{{$checklist_air_diffuser->measuring_point_number}}</td>
                                                                                <td>{{$checklist_air_diffuser->air_direction_type == 0 ? 'приток' : 'вытяжка'}}</td>
                                                                                <td>{{$checklist_air_diffuser->duct_cross_sectional_area}}</td>
                                                                                <td>{{$checklist_air_diffuser->air_speed}}</td>
                                                                                <td>{{$checklist_air_diffuser->estimated_coefficient}}</td>
                                                                                <td>{{$checklist_air_diffuser->air_flow_rate}}</td>
                                                                                <td>{{$checklist_air_diffuser->air_temperature}}</td>
                                                                                <td>{{$checklist_air_diffuser->comment}}</td>
                                                                            </tr>
                                                                        @empty
                                                                            <td colspan="6">нет данных ...</td>
                                                                        @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                                @if(auth()->user()->can('checklist_air_diffuser create') || auth()->user()->hasRole('sadmin'))
                                                                    <a href="{{route('checklists_air_diffuser.create_from_trk_room', $trk_room->id)}}"
                                                                       class="btn btn-outline-success btn-sm mb-2">Добавить чеклист диффузоров</a>
                                                                @endif
                                                            </li>
                                                        @else
                                                            @if(auth()->user()->can('checklist_air_diffuser create') || auth()->user()->hasRole('sadmin'))
                                                                <a href="{{route('checklists_air_diffuser.create_from_trk_room', $trk_room->id)}}"
                                                                   class="btn btn-outline-success btn-sm col-3 mb-2 mt-3">Добавить чеклист диффузоров</a>
                                                            @endif
                                                        @endif
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back()"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('trk_room edit') || auth()->user()->hasRole('sadmin'))
                                        <a href="{{route('trk_room.edit', $trk_room)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('trk_room delete') || auth()->user()->hasRole('sadmin'))
                                        <form action="{{route('trk_room.destroy', $trk_room)}}" method="post">
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
        <!-- Modal set avrs to another room -->
        <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel3"
             aria-hidden="true">
            <form action="{{route('avrs.set_avrs_from_this_to_another_room', $trk_room)}}" method="post">
                @csrf
                @method('patch')
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel3">Прикрепление АВР к другому помещению</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="building_id" class="form-label form-label-sm">Блок/Зона <span class="text-danger"><b>*</b></span></label>
                                    <select required name="building_id" class="form-select form-select-sm">
                                        @forelse($buildings as $building)
                                            <option value="{{$building->id}}" {{$building->id == $trk_room->building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                    <label for="floor_id" class="form-label form-label-sm">Этаж/Отметка <span class="text-danger"><b>*</b></span></label>
                                    <select required name="floor_id" class="form-select form-select-sm">
                                        @forelse($floors as $floor)
                                            <option value="{{$floor->id}}" {{$floor->id == $trk_room->floor->id ? 'selected' : null}}>{{$floor->name}}</option>
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
                                    <label for="room_id" class="form-label form-label-sm">Помещение <span class="text-danger"><b>*</b></span></label>
                                    <select required name="room_id" class="form-select form-select-sm"
                                            id="room_id">
                                        @forelse($rooms as $room)
                                            <option value="{{$room->id}}" {{$room->id == $trk_room->room->id ? 'selected' : null}}>{{$room->name}}</option>
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
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Сохранить</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Отмена</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- Modal set equipments to another room -->
        <div class="modal fade" id="exampleModal4" tabindex="-1" aria-labelledby="exampleModalLabel4"
             aria-hidden="true">
            <form action="{{route('trk_equipments.set_equipments_from_this_to_another_room', $trk_room)}}" method="post">
                @csrf
                @method('patch')
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel3">Прикрепление оборудования к другому помещению</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="building_id" class="form-label form-label-sm">Блок/Зона <span class="text-danger"><b>*</b></span></label>
                                    <select required name="building_id" class="form-select form-select-sm">
                                        @forelse($buildings as $building)
                                            <option value="{{$building->id}}" {{$building->id == $trk_room->building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                    <label for="floor_id" class="form-label form-label-sm">Этаж/Отметка <span class="text-danger"><b>*</b></span></label>
                                    <select required name="floor_id" class="form-select form-select-sm">
                                        @forelse($floors as $floor)
                                            <option value="{{$floor->id}}" {{$floor->id == $trk_room->floor->id ? 'selected' : null}}>{{$floor->name}}</option>
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
                                    <label for="room_id" class="form-label form-label-sm">Помещение <span class="text-danger"><b>*</b></span></label>
                                    <select required name="room_id" class="form-select form-select-sm"
                                            id="room_id">
                                        @forelse($rooms as $room)
                                            <option value="{{$room->id}}" {{$room->id == $trk_room->room->id ? 'selected' : null}}>{{$room->name}}</option>
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
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Сохранить</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Отмена</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
