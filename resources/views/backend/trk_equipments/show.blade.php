@extends('layouts.backend.main')

@section('title', 'Главная | ТРК/Оборудование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">ТРК/Оборудование</h4>
                        @if(auth()->user()->can('trk_equipment create') || auth()->user()->hasRole('sadmin'))
                        <a
                            href="{{route('trk_equipments.create')}}"><img
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
                                <ul class="list-group mb-4">
{{--                                    <li class="list-group-item">--}}
{{--                                        <button class="btn btn-sm btn-outline-success btn-block">Подать заявку</button>--}}
{{--                                    </li>--}}
                                    <li class="list-group-item">
                                        <b>Оборудование: </b>{{$trk_equipment->equipment_name->name ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Система: </b>{{$trk_equipment->system->name ?? 'не выбрано'}}</li>
                                    <li class="list-group-item">
                                        <b>Статус: </b>{{$trk_equipment->equipment_status->name ?? 'не выбрано'}}</li>
                                    <li class="list-group-item">
                                        <b>Комментарий: </b>{{$trk_equipment->comment ?? 'отсутствует'}}</li>
                                    <div class="accordion accordion-flush" id="accordionFlushExample">
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne11">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne11"
                                                        aria-expanded="false" aria-controls="flush-collapseOne11">
                                                    Где находится:
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne11" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne11"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <ul class="list-group mb-3">
                                                        <li class="list-group-item">
                                                            <b>Город: </b>{{$trk_equipment->trk_room->trk->town->name ?? 'не выбрано'}}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b>ТРК: </b>{{$trk_equipment->trk_room->trk->name ?? 'не выбрано'}}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b>Блок/Зона: </b>{{$trk_equipment->trk_room->building->name ?? 'не выбрано'}}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b>Этаж/Отметка: </b>{{$trk_equipment->trk_room->floor->name ?? 'не выбрано'}}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b>Оси: </b>{{$trk_equipment->axe->name ?? 'не выбрано'}}
                                                        </li>
                                                        <li class="list-group-item">
                                                            <b>Помещение: </b><a href="{{route('trk_room.show', $trk_equipment->trk_room->id)}}">{{$trk_equipment->trk_room->room->name ?? 'не выбрано'}}</a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne"
                                                        aria-expanded="false" aria-controls="flush-collapseOne">
                                                    Запчасти: {{count($trk_equipment->spare_parts) . 'шт.'}}
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <div class="table-responsive">
                                                    <table class="table table-striped table-bordered table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th>Наименование</th>
                                                                <th>Модель</th>
                                                                <th>Количество</th>
                                                                <th>Комментарий</th>
                                                                <th>
{{--                                                                    <a href="#" class="ms-2 btn btn-sm btn-outline-success btn-block">Заказать</a>--}}
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        @forelse($trk_equipment->spare_parts as $spare_part)
                                                            <tr>
                                                                <td onclick="window.location='{{ route('equipment_spare_parts.show', $spare_part->id) }}';" style="cursor: pointer;">{{$spare_part->part_name->name}}</td>
                                                                <td  onclick="window.location='{{ route('equipment_spare_parts.show', $spare_part->id) }}';" style="cursor: pointer;">{{$spare_part->model}}</td>
                                                                <td  onclick="window.location='{{ route('equipment_spare_parts.show', $spare_part->id) }}';" style="cursor: pointer;">{{$spare_part->value}}</td>
                                                                <td  onclick="window.location='{{ route('equipment_spare_parts.show', $spare_part->id) }}';" style="cursor: pointer;">{{$spare_part->comment}}</td>
                                                                <td><a href="{{ route('orders.create_from_trk_equipment', [$trk_equipment->id, $spare_part->id]) }}" class="ms-2 btn btn-sm btn-outline-success btn-block">Заказать</a></td>
                                                            </tr>
                                                        @empty
                                                            <td colspan="5">не заполнено ...</td>
                                                        @endforelse
                                                        </tbody>
                                                    </table>
                                                    </div>
                                                    @if(auth()->user()->can('equipment_spare_part create')
                                                        || auth()->user()->hasRole('sadmin'))
                                                    <a href="{{route('equipment_spare_parts.create_from_equipment', $trk_equipment->id)}}"
                                                       class="btn btn-outline-warning btn-sm">
                                                        <img src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit" style="height: 20px;" title="Редактировать">
                                                    </a>
                                                    @endif
                                                    @if(auth()->user()->hasRole('sadmin'))
                                                        <br>
                                                        <button type="button" class="btn btn-outline-danger rounded btn-sm mt-4" data-bs-toggle="modal"
                                                                data-bs-target="#exampleModal4">Привязать все запчасти к другой установке
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne2">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne2"
                                                        aria-expanded="false" aria-controls="flush-collapseOne2">
                                                    Потребители: {{count($trk_equipment->users) . 'шт.'}}
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne2" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne2"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered table-sm">
                                                            <thead>
                                                            <tr>
                                                                <th>Этаж</th>
                                                                <th>Помещение</th>
                                                                <th>Арендатор</th>
                                                                <th>Юр. лицо</th>
                                                                <th>Комментарий</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @forelse($trk_equipment->users as $equipment_user)
                                                                <tr onclick="window.location='{{ route('equipment_users.show', $equipment_user->id) }}';" style="cursor: pointer;">
                                                                    <td>{{$equipment_user->trk_room->floor->name}}</td>
                                                                    <td>{{$equipment_user->trk_room->room->name}}</td>
                                                                    <td>{{$equipment_user->trk_room->renter->brand->name ?? ''}}</td>
                                                                    <td>{{$equipment_user->trk_room->renter->organization->name ?? ''}}</td>
                                                                    <td>{{$equipment_user->comment}}</td>
                                                                </tr>
                                                            @empty
                                                                <td colspan="5">не заполнено ...</td>
                                                            @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @if(auth()->user()->can('equipment_user create')
                                                      || auth()->user()->hasRole('sadmin'))
                                                    <a href="{{route('equipment_users.create_from_equipment', $trk_equipment->id)}}"
                                                       class="btn btn-outline-warning btn-sm">
                                                        <img src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit" style="height: 20px;" title="Редактировать">
                                                    </a>
                                                    @endif
                                                    @if(auth()->user()->hasRole('sadmin'))
                                                        <br>
                                                        <button type="button" class="btn btn-outline-danger rounded btn-sm mt-3" data-bs-toggle="modal"
                                                                data-bs-target="#exampleModal8">Привязать всех потребителей к другой установке
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne5">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne5"
                                                        aria-expanded="false" aria-controls="flush-collapseOne5">
                                                    Технические характеристики: {{count($trk_equipment->parameters) . 'шт.'}}
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne5" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne5"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <ul class="list-group mb-3">
                                                        @forelse($trk_equipment->parameters as $parameter)
                                                            <li class="list-group-item"><a class="nav-link"
                                                                                           href="{{route('equipment_parameters.show', $parameter->id)}}"><span
                                                                        class="border-bottom-double">{{$parameter->parameter_name->name}}</span>
                                                                    &nbsp;{{$parameter->value}}</a></li>
                                                        @empty
                                                            не заполнено ...
                                                        @endforelse
                                                    </ul>
                                                    @if(auth()->user()->can('equipment_parameter create')
                                                      || auth()->user()->hasRole('sadmin'))
                                                    <a href="{{route('equipment_parameters.create_from_equipment', $trk_equipment->id)}}"
                                                       class="btn btn-outline-warning btn-sm">
                                                        <img src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit" style="height: 20px;" title="Редактировать">
                                                    </a>
                                                    @endif
                                                    @if(auth()->user()->hasRole('sadmin'))
                                                        <br>
                                                        <button type="button" class="btn btn-outline-danger rounded btn-sm mt-4" data-bs-toggle="modal"
                                                                data-bs-target="#exampleModal5">Привязать все параметры к другой установке
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne8">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne8"
                                                        aria-expanded="false" aria-controls="flush-collapseOne8">
                                                    Периодичность работ в днях: {{count($trk_equipment->work_periods) . 'шт.'}}
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne8" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne8"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <ul class="list-group mb-3">
                                                        @forelse($trk_equipment->work_periods as $work_period)
                                                            <li class="list-group-item">
                                                                <a href="{{route('equipment_work_periods.show', $work_period->id)}}">
                                                                    <span>
                                                                       {!! str_replace(' ', '_', $work_period->work_name->name) !!}
                                                                    </span>
                                                                </a>
                                                                &nbsp;<span>{{ 'раз в ' . $work_period->repeat_days . ' дн. (' . $work_period->next_to_be_at . ')'}}</span>
                                                            </li>
                                                        @empty
                                                            не заполнено ...
                                                        @endforelse
                                                    </ul>
                                                    @if(auth()->user()->can('equipment_work_period create')
                                                      || auth()->user()->hasRole('sadmin'))
                                                    <a href="{{route('equipment_work_periods.create_from_equipment', $trk_equipment->id)}}"
                                                       class="btn btn-outline-warning btn-sm">
                                                        <img src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit" style="height: 20px;" title="Редактировать">
                                                    </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne13">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne13"
                                                        aria-expanded="false" aria-controls="flush-collapseOne13">
                                                    Ремонт: {{count($trk_equipment->repairs) . 'шт.'}}
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne13" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne13"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <ul class="list-group mb-3">
                                                        @forelse($trk_equipment->repairs as $repair)
                                                            <li class="list-group-item"><a class="nav-link"
                                                                                           href="{{route('trk_repairs.show', $repair->id)}}"><span
                                                                        class="border-bottom-double">{{$repair->created_at . ', ' . $repair->done_progress . '%'}}</span></a>
                                                                <span>{{$repair->description}}</span>
                                                            </li>
                                                        @empty
                                                            не заполнено ...
                                                        @endforelse
                                                    </ul>
                                                    @if(auth()->user()->can('trk_repair create')
                                                      || auth()->user()->hasRole('sadmin'))
                                                        <a href="{{route('trk_repairs.create_from_equipment', $trk_equipment)}}"
                                                           class="btn btn-outline-warning btn-sm">
                                                            <img src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit" style="height: 20px;" title="Редактировать">
                                                        </a>
                                                    @endif
                                                    @if(auth()->user()->hasRole('sadmin'))
                                                        <br>
                                                        <button type="button" class="btn btn-outline-danger rounded btn-sm mt-4" data-bs-toggle="modal"
                                                                data-bs-target="#exampleModal2">Привязать весь ремонт к другой установке
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne14">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne14"
                                                        aria-expanded="false" aria-controls="flush-collapseOne14">
                                                    Акты: {{count($avrs) . 'шт.'}}
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne14" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne14"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <div class="table-responsive">
                                                        <table class="table table-striped table-bordered table-sm">
                                                            <thead>
                                                            <tr>
                                                                <th class="text-nowrap">Дата</th>
                                                                <th>Вид работ</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                            @forelse($avrs as $avr)
                                                                <tr onclick="window.location='{{ route('avrs.show', $avr->avr_id) }}';" style="cursor: pointer;">
                                                                    <td class="text-nowrap">{{$avr->avr->date}}</td>
                                                                    <td>
                                                                        @forelse($avr->avr->avr_works as $avr_work)
                                                                            @if($avr_work->trk_equipment_id == $trk_equipment->id)
                                                                                <span>{{$avr_work->work_name->name}}</span><br>
                                                                                @if($avr_work->description != '')
                                                                                    <span>{{$avr_work->description}}</span><br>
                                                                                @endif
                                                                            @endif
                                                                        @empty
                                                                            <span>виды работ, нет данных ...</span>
                                                                        @endforelse
                                                                    </td>
                                                                </tr>
                                                            @empty
                                                                <td colspan="4">не заполнено ...</td>
                                                            @endforelse
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    @if(auth()->user()->can('avr create')
                                                      || auth()->user()->hasRole('sadmin'))
                                                        <a href="{{route('avrs.create_from_trk_equipment', $trk_equipment)}}"
                                                           class="btn btn-outline-warning btn-sm">
                                                            <img src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit" style="height: 20px;" title="Редактировать">
                                                        </a>
                                                    @endif
                                                    @if(auth()->user()->hasRole('sadmin'))
                                                        <br>
                                                        <button type="button" class="btn btn-outline-danger rounded btn-sm mt-4" data-bs-toggle="modal"
                                                                data-bs-target="#exampleModal3">Привязать все акты к другой установке
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne17">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne17"
                                                        aria-expanded="false" aria-controls="flush-collapseOne17">
                                                    Чек листы:
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne17" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne17"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    @if(!empty($checklists_conditioner) && count($checklists_conditioner) > 0)
                                                    @if('Кондиционирование' == $trk_equipment->system->name
                                                      && (new \ReflectionClass(get_class($checklists_conditioner->first())))->getShortName() == ChecklistConditioner::class)
                                                            <div class="table-responsive mt-3">
                                                                <h5>Чеклисты кондиционера</h5>
                                                        <table class="table table-sm table-striped table-hover table-bordered">
                                                            <thead>
                                                                <th>Дата</th>
                                                                <th>Воздух вход, т</th>
                                                                <th>Воздух выход, т</th>
                                                                <th>Комментарий</th>
                                                            </thead>
                                                            <tbody>
                                                            @forelse($checklists_conditioner as $checklist_conditioner)
                                                                <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_conditioner.show', $checklist_conditioner) }}';">
                                                                    <td>{{$checklist_conditioner->created_at}}</td>
                                                                    <td>{{$checklist_conditioner->air_inlet_temperature}}</td>
                                                                    <td>{{$checklist_conditioner->air_outlet_temperature}}</td>
                                                                    <td>{{$checklist_conditioner->comment}}</td>
                                                                </tr>
                                                            @empty
                                                                <span>нет данных ...</span>
                                                            @endforelse
                                                            </tbody>
                                                        </table>
                                                            </div>
                                                    @endif
                                                    @endif
                                                        @if(!empty($checklists_fancoil) && count($checklists_fancoil) > 0)
                                                        @if('Кондиционирование' == $trk_equipment->system->name
                                                          && (new \ReflectionClass(get_class($checklists_fancoil->first())))->getShortName() == ChecklistFancoil::class)
                                                            <div class="table-responsive mt-3">
                                                                <h5>Чеклисты фанкойла</h5>
                                                                <table class="table table-sm table-striped table-hover table-bordered">
                                                                    <thead>
                                                                    <th>Дата</th>
                                                                    <th>Воздух вход, т</th>
                                                                    <th>Воздух выход, т</th>
                                                                    <th>Вода вход, т</th>
                                                                    <th>Вода выход, т</th>
                                                                    <th>Комментарий</th>
                                                                    </thead>
                                                                    <tbody>
                                                                    @forelse($checklists_fancoil as $checklist_fancoil)
                                                                        <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_fancoil.show', $checklist_fancoil) }}';">
                                                                            <td>{{$checklist_fancoil->created_at}}</td>
                                                                            <td>{{$checklist_fancoil->air_inlet_temperature}}</td>
                                                                            <td>{{$checklist_fancoil->air_outlet_temperature}}</td>
                                                                            <td>{{$checklist_fancoil->cold_water_inlet_temperature}}</td>
                                                                            <td>{{$checklist_fancoil->cold_water_outlet_temperature}}</td>
                                                                            <td>{{$checklist_fancoil->comment}}</td>
                                                                        </tr>
                                                                    @empty
                                                                        <span>нет данных ...</span>
                                                                    @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endif
                                                        @endif
                                                        @if(!empty($checklists_balk) && count($checklists_balk) > 0)
                                                        @if('Кондиционирование' == $trk_equipment->system->name
                                                          && (new \ReflectionClass(get_class($checklists_balk->first())))->getShortName() == ChecklistBalk::class)
                                                            <div class="table-responsive mt-3">
                                                                <h5>Чеклисты балки</h5>
                                                                <table class="table table-sm table-striped table-hover table-bordered">
                                                                    <thead>
                                                                    <th>Дата</th>
                                                                    <th>Типоразмер</th>
                                                                    <th>V_air</th>
                                                                    <th>Q_air</th>
                                                                    <th>P_air</th>
                                                                    <th>T_air_in</th>
                                                                    <th>T_air_out</th>
                                                                    <th>Створки</th>
                                                                    <th>T_water_in</th>
                                                                    <th>T_water_out</th>
                                                                    <th>P_delta_water</th>
                                                                    <th>Water_valve</th>
                                                                    <th>Q_water</th>
                                                                    <th>Комментарий</th>
                                                                    </thead>
                                                                    <tbody>
                                                                    @forelse($checklists_balk as $checklist_balk)
                                                                        <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_balk.show', $checklist_balk) }}';">
                                                                            <td>{{$checklist_balk->created_at}}</td>
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
                                                                        <span>нет данных ...</span>
                                                                    @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @endif
                                                        @endif
                                                        @if(!empty($checklists_air_supply) && count($checklists_air_supply) > 0)
                                                            @if('Вентиляция' == $trk_equipment->system->name
                                                              && (new \ReflectionClass(get_class($checklists_air_supply->first())))->getShortName() == ChecklistAirSupply::class)
                                                                <div class="table-responsive mt-2">
                                                                    <h5>Чеклисты притока</h5>
                                                                    <table class="table table-sm table-striped table-hover table-bordered">
                                                                        <thead>
                                                                        <th>Дата</th>
                                                                        <th>Т на улице</th>
                                                                        <th>Уставка притока</th>
                                                                        <th>Т притока</th>
                                                                        <th>Клапан ГВС</th>
                                                                        <th>Клапан ХВС</th>
                                                                        <th>Комментарий</th>
                                                                        </thead>
                                                                        <tbody>
                                                                        @forelse($checklists_air_supply as $checklist_air_supply)
                                                                            <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_air_supply.show', $checklist_air_supply) }}';">
                                                                                <td>{{$checklist_air_supply->created_at}}</td>
                                                                                <td>{{$checklist_air_supply->outside_air_t}}</td>
                                                                                <td>{{$checklist_air_supply->setpoint_air_t}}</td>
                                                                                <td>{{$checklist_air_supply->supply_air_t}}</td>
                                                                                <td>{{$checklist_air_supply->hot_water_valve_open_percent}}</td>
                                                                                <td>{{$checklist_air_supply->cold_water_valve_open_percent}}</td>
                                                                                <td>{{$checklist_air_supply->comment}}</td>
                                                                            </tr>
                                                                        @empty
                                                                            <span>нет данных ...</span>
                                                                        @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                    @if(auth()->user()->hasRole('sadmin'))
                                                                        <br>
                                                                        <button type="button" class="btn btn-outline-danger rounded btn-sm" data-bs-toggle="modal"
                                                                                data-bs-target="#exampleModal6">Привязать все чеклисты притока к другой установке
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endif
                                                        @if(!empty($checklists_air_extract) && count($checklists_air_extract) > 0)
                                                            @if('Вентиляция' == $trk_equipment->system->name
                                                              && (new \ReflectionClass(get_class($checklists_air_extract->first())))->getShortName() == ChecklistAirExtract::class)
                                                                <div class="table-responsive mt-3">
                                                                    <h5>Чеклисты вытяжки</h5>
                                                                    <table class="table table-sm table-striped table-hover table-bordered">
                                                                        <thead>
                                                                        <th>Дата</th>
                                                                        <th>Т воздуха на вытяжке</th>
                                                                        <th>Ток двигателя факт</th>
                                                                        <th>Ток двигателя паспорт</th>
                                                                        <th>Комментарий</th>
                                                                        </thead>
                                                                        <tbody>
                                                                        @forelse($checklists_air_extract as $checklist_air_extract)
                                                                            <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_air_extract.show', $checklist_air_extract) }}';">
                                                                                <td>{{$checklist_air_extract->created_at}}</td>
                                                                                <td>{{$checklist_air_extract->extract_air_t}}</td>
                                                                                <td>{{$checklist_air_extract->extract_engine_actual_current}}</td>
                                                                                <td>{{$checklist_air_extract->extract_engine_passport_current}}</td>
                                                                                <td>{{$checklist_air_extract->comment}}</td>
                                                                            </tr>
                                                                        @empty
                                                                            <span>нет данных ...</span>
                                                                        @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                    @if(auth()->user()->hasRole('sadmin'))
                                                                        <br>
                                                                        <button type="button" class="btn btn-outline-danger rounded btn-sm" data-bs-toggle="modal"
                                                                                data-bs-target="#exampleModal7">Привязать все чеклисты вытяжки к другой установке
                                                                        </button>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        @endif
                                                        @if(!empty($checklists_air_duct) && count($checklists_air_duct) > 0)
                                                            @if('Вентиляция' == $trk_equipment->system->name
                                                              && (new \ReflectionClass(get_class($checklists_air_duct->first())))->getShortName() == ChecklistAirDuct::class)
                                                                <div class="table-responsive mt-3">
                                                                    <h5>Чеклисты воздуховодов</h5>
                                                                    <table class="table table-sm table-striped table-hover table-bordered">
                                                                        <thead>
                                                                        <th>Дата</th>
                                                                        <th>Точка</th>
                                                                        <th>Тип</th>
                                                                        <th>Площадь</th>
                                                                        <th>Скорость</th>
                                                                        <th>Расход</th>
                                                                        <th>Температура</th>
                                                                        <th>Комментарий</th>
                                                                        </thead>
                                                                        <tbody>
                                                                        @forelse($checklists_air_duct as $checklist_air_duct)
                                                                            <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_air_duct.show', $checklist_air_duct) }}';">
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
                                                                            <span>нет данных ...</span>
                                                                        @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @endif
                                                        @endif
                                                        @if(!empty($checklists_air_diffuser) && count($checklists_air_diffuser) > 0)
                                                            @if('Вентиляция' == $trk_equipment->system->name
                                                              && (new \ReflectionClass(get_class($checklists_air_diffuser->first())))->getShortName() == ChecklistAirDiffuser::class)
                                                                <div class="table-responsive mt-3">
                                                                    <h5>Чеклисты диффузоров</h5>
                                                                    <table class="table table-sm table-striped table-hover table-bordered">
                                                                        <thead>
                                                                        <th>Дата</th>
                                                                        <th>Точка</th>
                                                                        <th>Тип</th>
                                                                        <th>Площадь</th>
                                                                        <th>Скорость</th>
                                                                        <th>Расход</th>
                                                                        <th>Температура</th>
                                                                        <th>Комментарий</th>
                                                                        </thead>
                                                                        <tbody>
                                                                        @forelse($checklists_air_diffuser as $checklist_air_diffuser)
                                                                            <tr style="cursor: pointer;" onclick="window.location='{{ route('checklists_air_diffuser.show', $checklist_air_diffuser) }}';">
                                                                                <td>{{$checklist_air_diffuser->created_at}}</td>
                                                                                <td>{{$checklist_air_diffuser->measuring_point_number}}</td>
                                                                                <td>{{$checklist_air_diffuser->air_direction_type == 0 ? 'приток' : 'вытяжка'}}</td>
                                                                                <td>{{$checklist_air_diffuser->duct_cross_sectional_area}}</td>
                                                                                <td>{{$checklist_air_diffuser->air_speed}}</td>
                                                                                <td>{{$checklist_air_diffuser->air_flow_rate}}</td>
                                                                                <td>{{$checklist_air_diffuser->air_temperature}}</td>
                                                                                <td>{{$checklist_air_diffuser->comment}}</td>
                                                                            </tr>
                                                                        @empty
                                                                            <span>нет данных ...</span>
                                                                        @endforelse
                                                                        </tbody>
                                                                    </table>
                                                                </div>
                                                            @endif
                                                        @endif
                                                        @if(auth()->user()->can('checklist_conditioner create')
                                                         || auth()->user()->hasRole('sadmin'))
                                                            @if('Кондиционирование' == $trk_equipment->system->name)
                                                                <br>
                                                            <a href="{{route('checklists_conditioner.create_from_trk_equipment', $trk_equipment)}}"
                                                               class="btn btn-outline-success btn-sm mt-2">Добавить чеклист кондиционера</a>
                                                            @endif
                                                        @endif
                                                        @if(auth()->user()->can('checklist_fancoil create')
                                                             || auth()->user()->hasRole('sadmin'))
                                                            @if('Кондиционирование' == $trk_equipment->system->name)
                                                                <br>
                                                                <a href="{{route('checklists_fancoil.create_from_trk_equipment', $trk_equipment)}}"
                                                                   class="btn btn-outline-success btn-sm mt-2">Добавить чеклист фанкойла</a>
                                                            @endif
                                                        @endif
                                                        @if(auth()->user()->can('checklist_balk create')
                                                                 || auth()->user()->hasRole('sadmin'))
                                                            @if('Кондиционирование' == $trk_equipment->system->name)
                                                                <br>
                                                                <a href="{{route('checklists_balk.create_from_trk_equipment', $trk_equipment)}}"
                                                                   class="btn btn-outline-success btn-sm mt-2">Добавить чеклист балки</a>
                                                            @endif
                                                        @endif
                                                        @if(auth()->user()->can('checklist_air_supply create')
                                                                    || auth()->user()->hasRole('sadmin'))
                                                            @if('Вентиляция' == $trk_equipment->system->name)
                                                                <br>
                                                                <a href="{{route('checklists_air_supply.create_from_trk_equipment', $trk_equipment)}}"
                                                                   class="btn btn-outline-success btn-sm mt-2">Добавить чеклист притока</a>
                                                            @endif
                                                        @endif
                                                        @if(auth()->user()->can('checklist_air_extract create')
                                                                    || auth()->user()->hasRole('sadmin'))
                                                            @if('Вентиляция' == $trk_equipment->system->name)
                                                                <br>
                                                                <a href="{{route('checklists_air_extract.create_from_trk_equipment', $trk_equipment)}}"
                                                                   class="btn btn-outline-success btn-sm mt-2">Добавить чеклист вытяжки</a>
                                                            @endif
                                                        @endif
                                                        @if(auth()->user()->can('checklist_air_duct create')
                                                                        || auth()->user()->hasRole('sadmin'))
                                                            @if('Вентиляция' == $trk_equipment->system->name)
                                                                <br>
                                                                <a href="{{route('checklists_air_duct.create_from_trk_equipment', $trk_equipment)}}"
                                                                   class="btn btn-outline-success btn-sm mt-2">Добавить чеклист воздуховода</a>
                                                            @endif
                                                        @endif
                                                        @if(auth()->user()->can('checklist_air_diffuser create')
                                                                            || auth()->user()->hasRole('sadmin'))
                                                            @if('Вентиляция' == $trk_equipment->system->name)
                                                                <br>
                                                                <a href="{{route('checklists_air_diffuser.create_from_trk_equipment', $trk_equipment)}}"
                                                                   class="btn btn-outline-success btn-sm mt-2">Добавить чеклист диффузоров</a>
                                                            @endif
                                                        @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="flush-headingOne15">
                                                <button class="accordion-button collapsed" type="button"
                                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne15"
                                                        aria-expanded="false" aria-controls="flush-collapseOne15">
                                                    Эксплуатационная ответственность:
                                                </button>
                                            </h2>
                                            <div id="flush-collapseOne15" class="accordion-collapse collapse"
                                                 aria-labelledby="flush-headingOne15"
                                                 data-bs-parent="#accordionFlushExample">
                                                <div class="accordion-body">
                                                    <span>{{$trk_equipment->responsible_division->name ?? 'нет данных'}}</span>
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
                                    @if(auth()->user()->can('trk_equipment edit') || auth()->user()->hasRole('sadmin'))
                                        <a href="{{route('trk_equipments.edit', $trk_equipment)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('trk_equipment delete') || auth()->user()->hasRole('sadmin'))
                                        <form action="{{route('trk_equipments.destroy', $trk_equipment)}}"
                                              method="post">
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
        @if(auth()->user()->hasRole('sadmin'))
        <!-- Modal set repairs to another equipment -->
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2"
             aria-hidden="true">
            <form action="{{route('trk_repairs.set_repairs_from_this_to_another_equipment', $trk_equipment)}}" method="post">
                @csrf
                @method('patch')
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel2">Прикрепление ремонта к другому оборудованию</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="system_id" class="form-label form-label-sm">Система <span class="text-danger"><b>*</b></span></label>
                                    <select required name="system_id" class="form-select form-select-sm">
                                        @forelse($systems as $system)
                                            <option value="{{$system->id}}" {{$system->id == $trk_equipment->system->id ? 'selected' : null}}>{{$system->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    @error('system_id')
                                    <div class="text-danger">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="building_id" class="form-label form-label-sm">Блок/Зона <span class="text-danger"><b>*</b></span></label>
                                    <select required name="building_id" class="form-select form-select-sm">
                                        @forelse($buildings as $building)
                                            <option value="{{$building->id}}" {{$building->id == $trk_equipment->trk_room->building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                            <option value="{{$floor->id}}" {{$floor->id == $trk_equipment->trk_room->floor->id ? 'selected' : null}}>{{$floor->name}}</option>
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
                                            <option value="{{$room->id}}" {{$room->id == $trk_equipment->trk_room->room->id ? 'selected' : null}}>{{$room->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    @error('room_id')
                                    <div class="text-danger">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="equipment_name" class="form-label form-label-sm">Оборудование <span class="text-danger"><b>*</b></span></label>
                                    <select required name="equipment_name_id" class="form-select form-select-sm"
                                            id="equipment_name_id">
                                        @forelse($equipments as $equipment)
                                            <option value="{{$equipment->id}}" {{$equipment->id == $trk_equipment->equipment_name_id ? 'selected' : null}}>{{$equipment->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    @error('equipment_name_id')
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
        <!-- Modal set avrs to another equipment -->
        <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel3"
             aria-hidden="true">
            <form action="{{route('avrs.set_avrs_from_this_to_another_equipment', $trk_equipment)}}" method="post">
                @csrf
                @method('patch')
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel3">Прикрепление АВР к другому оборудованию</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="system_id" class="form-label form-label-sm">Система <span class="text-danger"><b>*</b></span></label>
                                    <select required name="system_id" class="form-select form-select-sm">
                                        @forelse($systems as $system)
                                            <option value="{{$system->id}}" {{$system->id == $trk_equipment->system->id ? 'selected' : null}}>{{$system->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    @error('system_id')
                                    <div class="text-danger">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="building_id" class="form-label form-label-sm">Блок/Зона <span class="text-danger"><b>*</b></span></label>
                                    <select required name="building_id" class="form-select form-select-sm">
                                        @forelse($buildings as $building)
                                            <option value="{{$building->id}}" {{$building->id == $trk_equipment->trk_room->building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                            <option value="{{$floor->id}}" {{$floor->id == $trk_equipment->trk_room->floor->id ? 'selected' : null}}>{{$floor->name}}</option>
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
                                            <option value="{{$room->id}}" {{$room->id == $trk_equipment->trk_room->room->id ? 'selected' : null}}>{{$room->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    @error('room_id')
                                    <div class="text-danger">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="equipment_name" class="form-label form-label-sm">Оборудование <span class="text-danger"><b>*</b></span></label>
                                    <select required name="equipment_name_id" class="form-select form-select-sm"
                                            id="equipment_name_id">
                                        @forelse($equipments as $equipment)
                                            <option value="{{$equipment->id}}" {{$equipment->id == $trk_equipment->equipment_name_id ? 'selected' : null}}>{{$equipment->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    @error('equipment_name_id')
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
        <!-- Modal set spare parts to another equipment -->
        <div class="modal fade" id="exampleModal4" tabindex="-1" aria-labelledby="exampleModalLabel4"
             aria-hidden="true">
            <form action="{{route('equipment_spare_parts.set_spare_parts_from_this_to_another_equipment', $trk_equipment)}}" method="post">
                @csrf
                @method('patch')
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel3">Прикрепление запчастей к другому оборудованию</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="system_id" class="form-label form-label-sm">Система <span class="text-danger"><b>*</b></span></label>
                                    <select required name="system_id" class="form-select form-select-sm">
                                        @forelse($systems as $system)
                                            <option value="{{$system->id}}" {{$system->id == $trk_equipment->system->id ? 'selected' : null}}>{{$system->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    @error('system_id')
                                    <div class="text-danger">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="building_id" class="form-label form-label-sm">Блок/Зона <span class="text-danger"><b>*</b></span></label>
                                    <select required name="building_id" class="form-select form-select-sm">
                                        @forelse($buildings as $building)
                                            <option value="{{$building->id}}" {{$building->id == $trk_equipment->trk_room->building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                            <option value="{{$floor->id}}" {{$floor->id == $trk_equipment->trk_room->floor->id ? 'selected' : null}}>{{$floor->name}}</option>
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
                                            <option value="{{$room->id}}" {{$room->id == $trk_equipment->trk_room->room->id ? 'selected' : null}}>{{$room->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    @error('room_id')
                                    <div class="text-danger">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="equipment_name" class="form-label form-label-sm">Оборудование <span class="text-danger"><b>*</b></span></label>
                                    <select required name="equipment_name_id" class="form-select form-select-sm"
                                            id="equipment_name_id">
                                        @forelse($equipments as $equipment)
                                            <option value="{{$equipment->id}}" {{$equipment->id == $trk_equipment->equipment_name_id ? 'selected' : null}}>{{$equipment->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    @error('equipment_name_id')
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
        <!-- Modal set parameters to another equipment -->
        <div class="modal fade" id="exampleModal5" tabindex="-1" aria-labelledby="exampleModalLabel5"
             aria-hidden="true">
            <form action="{{route('equipment_parameters.set_parameters_from_this_to_another_equipment', $trk_equipment)}}" method="post">
                @csrf
                @method('patch')
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel3">Прикрепление параметров к другому оборудованию</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="building_id" class="form-label form-label-sm">Блок/Зона <span class="text-danger"><b>*</b></span></label>
                                    <select required name="building_id" class="form-select form-select-sm">
                                        @forelse($buildings as $building)
                                            <option value="{{$building->id}}" {{$building->id == $trk_equipment->trk_room->building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                            <option value="{{$floor->id}}" {{$floor->id == $trk_equipment->trk_room->floor->id ? 'selected' : null}}>{{$floor->name}}</option>
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
                                            <option value="{{$room->id}}" {{$room->id == $trk_equipment->trk_room->room->id ? 'selected' : null}}>{{$room->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    @error('room_id')
                                    <div class="text-danger">{{$message}}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label for="equipment_name" class="form-label form-label-sm">Оборудование <span class="text-danger"><b>*</b></span></label>
                                    <select required name="equipment_name_id" class="form-select form-select-sm"
                                            id="equipment_name_id">
                                        @forelse($equipments as $equipment)
                                            <option value="{{$equipment->id}}" {{$equipment->id == $trk_equipment->equipment_name_id ? 'selected' : null}}>{{$equipment->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    @error('equipment_name_id')
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
            <!-- Modal set checklists supply air to another equipment -->
            <div class="modal fade" id="exampleModal6" tabindex="-1" aria-labelledby="exampleModalLabel6"
                 aria-hidden="true">
                <form action="{{route('checklists_air_supply.set_checklists_air_supply_from_this_to_another_equipment', $trk_equipment)}}" method="post">
                    @csrf
                    @method('patch')
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel3">Прикрепление чеклистов притока к другому оборудованию</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="building_id" class="form-label form-label-sm">Блок/Зона <span class="text-danger"><b>*</b></span></label>
                                        <select required name="building_id" class="form-select form-select-sm">
                                            @forelse($buildings as $building)
                                                <option value="{{$building->id}}" {{$building->id == $trk_equipment->trk_room->building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                                <option value="{{$floor->id}}" {{$floor->id == $trk_equipment->trk_room->floor->id ? 'selected' : null}}>{{$floor->name}}</option>
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
                                                <option value="{{$room->id}}" {{$room->id == $trk_equipment->trk_room->room->id ? 'selected' : null}}>{{$room->name}}</option>
                                            @empty
                                                <option value="">нет данных ...</option>
                                            @endforelse
                                        </select>
                                        @error('room_id')
                                        <div class="text-danger">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="equipment_name" class="form-label form-label-sm">Оборудование <span class="text-danger"><b>*</b></span></label>
                                        <select required name="equipment_name_id" class="form-select form-select-sm"
                                                id="equipment_name_id">
                                            @forelse($equipments as $equipment)
                                                <option value="{{$equipment->id}}" {{$equipment->id == $trk_equipment->equipment_name_id ? 'selected' : null}}>{{$equipment->name}}</option>
                                            @empty
                                                <option value="">нет данных ...</option>
                                            @endforelse
                                        </select>
                                        @error('equipment_name_id')
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
            <!-- Modal set checklists extract air to another equipment -->
            <div class="modal fade" id="exampleModal7" tabindex="-1" aria-labelledby="exampleModalLabel7"
                 aria-hidden="true">
                <form action="{{route('checklists_air_extract.set_checklists_air_extract_from_this_to_another_equipment', $trk_equipment)}}" method="post">
                    @csrf
                    @method('patch')
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel7">Прикрепление чеклистов вытяжки к другому оборудованию</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="building_id" class="form-label form-label-sm">Блок/Зона <span class="text-danger"><b>*</b></span></label>
                                        <select required name="building_id" class="form-select form-select-sm">
                                            @forelse($buildings as $building)
                                                <option value="{{$building->id}}" {{$building->id == $trk_equipment->trk_room->building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                                <option value="{{$floor->id}}" {{$floor->id == $trk_equipment->trk_room->floor->id ? 'selected' : null}}>{{$floor->name}}</option>
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
                                                <option value="{{$room->id}}" {{$room->id == $trk_equipment->trk_room->room->id ? 'selected' : null}}>{{$room->name}}</option>
                                            @empty
                                                <option value="">нет данных ...</option>
                                            @endforelse
                                        </select>
                                        @error('room_id')
                                        <div class="text-danger">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="equipment_name" class="form-label form-label-sm">Оборудование <span class="text-danger"><b>*</b></span></label>
                                        <select required name="equipment_name_id" class="form-select form-select-sm"
                                                id="equipment_name_id">
                                            @forelse($equipments as $equipment)
                                                <option value="{{$equipment->id}}" {{$equipment->id == $trk_equipment->equipment_name_id ? 'selected' : null}}>{{$equipment->name}}</option>
                                            @empty
                                                <option value="">нет данных ...</option>
                                            @endforelse
                                        </select>
                                        @error('equipment_name_id')
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
            <!-- Modal set equipment users to another equipment -->
            <div class="modal fade" id="exampleModal8" tabindex="-1" aria-labelledby="exampleModalLabel8"
                 aria-hidden="true">
                <form action="{{route('equipment_users.set_equipment_users_from_this_to_another_equipment', $trk_equipment)}}" method="post">
                    @csrf
                    @method('patch')
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel8">Прикрепление потребителей к другому оборудованию</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="building_id" class="form-label form-label-sm">Блок/Зона <span class="text-danger"><b>*</b></span></label>
                                        <select required name="building_id" class="form-select form-select-sm">
                                            @forelse($buildings as $building)
                                                <option value="{{$building->id}}" {{$building->id == $trk_equipment->trk_room->building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                                <option value="{{$floor->id}}" {{$floor->id == $trk_equipment->trk_room->floor->id ? 'selected' : null}}>{{$floor->name}}</option>
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
                                                <option value="{{$room->id}}" {{$room->id == $trk_equipment->trk_room->room->id ? 'selected' : null}}>{{$room->name}}</option>
                                            @empty
                                                <option value="">нет данных ...</option>
                                            @endforelse
                                        </select>
                                        @error('room_id')
                                        <div class="text-danger">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="equipment_name" class="form-label form-label-sm">Оборудование <span class="text-danger"><b>*</b></span></label>
                                        <select required name="equipment_name_id" class="form-select form-select-sm"
                                                id="equipment_name_id">
                                            @forelse($equipments as $equipment)
                                                <option value="{{$equipment->id}}" {{$equipment->id == $trk_equipment->equipment_name_id ? 'selected' : null}}>{{$equipment->name}}</option>
                                            @empty
                                                <option value="">нет данных ...</option>
                                            @endforelse
                                        </select>
                                        @error('equipment_name_id')
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
        @endif
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
