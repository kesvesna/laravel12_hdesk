@extends('layouts.backend.main')

@section('title', 'Главная | АВР')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">АВР</h4><a href="{{route('avrs.create')}}"><img
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
                                    <li class="list-group-item"><b>Дата: </b>{{$avr->date ?? 'не выбрано'}}</li>
                                    @if(!empty($avr->operation_applications[0]->id))
                                        <li class="list-group-item"><b>По заявке: </b><a href="{{route('operation_applications.show', $avr->operation_applications[0]->id)}}">{{$avr->operation_applications[0]->created_at}}</a></li>
                                    @endif
                                    <li class="list-group-item">
                                        <b>ТРК: </b>{{$avr->trk_room->trk->name ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Блок/Зона: </b>{{$avr->trk_room->building->name ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Этаж: </b>{{$avr->trk_room->floor->name ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Помещение: </b><a href="{{route('trk_room.show', $avr->trk_room->id)}}">{{$avr->trk_room->room->name ?? 'не выбрано'}}</a></li>
                                    <li class="list-group-item"><b>Система: </b>{{$avr->system->name ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item"><b>Акт заполнил: </b>{{$avr->author->name}}
                                    </li>
                                    <div class="accordion accordion-flush" id="accordionFlushExample">
                                        @foreach($avr->avr_equipments as $equipment)
                                            <div class="accordion-item">
                                                <h2 class="accordion-header"
                                                    id="flush-heading{{$equipment->trk_equipment->equipment_name->id}}">
                                                    <button class="accordion-button collapsed" type="button"
                                                            data-bs-toggle="collapse"
                                                            data-bs-target="#flush-collapse{{$equipment->trk_equipment->equipment_name->id}}"
                                                            aria-expanded="false"
                                                            aria-controls="flush-collapse{{$equipment->trk_equipment->equipment_name->id}}">
                                                        {{$equipment->trk_equipment->equipment_name->name}}
                                                    </button>
                                                </h2>
                                                <div
                                                    id="flush-collapse{{$equipment->trk_equipment->equipment_name->id}}"
                                                    class="accordion-collapse collapse"
                                                    aria-labelledby="flush-heading{{$equipment->trk_equipment->equipment_name->id}}"
                                                    data-bs-parent="#accordionFlushExample">
                                                    <div class="accordion-body">
                                                        <div class="accordion accordion-flush"
                                                             id="accordionFlushExample222">
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="flush-headingOne222">
                                                                    <button class="accordion-button collapsed"
                                                                            type="button" data-bs-toggle="collapse"
                                                                            data-bs-target="#flush-collapseOne222"
                                                                            aria-expanded="false"
                                                                            aria-controls="flush-collapseOne222">
                                                                        Выполненные работы
                                                                    </button>
                                                                </h2>
                                                                <div id="flush-collapseOne222"
                                                                     class="accordion-collapse collapse"
                                                                     aria-labelledby="flush-headingOne"
                                                                     data-bs-parent="#accordionFlushExample222">
                                                                    <div class="accordion-body">
                                                                        <ul class="list-group mt-2">
                                                                            @foreach($avr->avr_works as $work)
                                                                                @if($work->trk_equipment->equipment_name->id == $equipment->trk_equipment->equipment_name->id)
                                                                                    <li class="list-group-item">{{$work->work_name->name}}
                                                                                        <br>{{$work->description}}</li>
                                                                                @endif
                                                                            @endforeach
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="flush-headingOne555">
                                                                    <button class="accordion-button collapsed"
                                                                            type="button" data-bs-toggle="collapse"
                                                                            data-bs-target="#flush-collapseOne555"
                                                                            aria-expanded="false"
                                                                            aria-controls="flush-collapseOne555">
                                                                        Использованные запчасти
                                                                    </button>
                                                                </h2>
                                                                <div id="flush-collapseOne555"
                                                                     class="accordion-collapse collapse"
                                                                     aria-labelledby="flush-headingOne555"
                                                                     data-bs-parent="#accordionFlushExample222">
                                                                    <div class="accordion-body">
                                                                        <ul class="list-group mt-2">
                                                                            <ul class="list-group mt-2">
                                                                                @foreach($avr->avr_spare_parts as $spare_part)
                                                                                    @if($spare_part->trk_equipment->equipment_name->id == $equipment->trk_equipment->equipment_name->id)
                                                                                        <li class="list-group-item">{{$spare_part->spare_part_name->name}}
                                                                                            <br>{{$spare_part->spare_part_model}}{{"  " . $spare_part->value}}
                                                                                        </li>
                                                                                    @endif
                                                                                @endforeach
                                                                            </ul>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="accordion-item">
                                                                <h2 class="accordion-header" id="flush-headingOne777">
                                                                    <button class="accordion-button collapsed"
                                                                            type="button" data-bs-toggle="collapse"
                                                                            data-bs-target="#flush-collapseOne777"
                                                                            aria-expanded="false"
                                                                            aria-controls="flush-collapseOne777">
                                                                        Кто выполнял
                                                                    </button>
                                                                </h2>
                                                                <div id="flush-collapseOne777"
                                                                     class="accordion-collapse collapse"
                                                                     aria-labelledby="flush-headingOne777"
                                                                     data-bs-parent="#accordionFlushExample222">
                                                                    <div class="accordion-body">
                                                                        <ul class="list-group mt-2">
                                                                            <ul class="list-group mt-2">
                                                                                @foreach($avr->avr_executors as $executor)
                                                                                    <li class="list-group-item">{{$executor->executor_name->name}}</li>
                                                                                @endforeach
                                                                            </ul>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </ul>
                                <div class="row">
                                    <div class="col mb-3">
                                        <ul class="list-group mb-4">
                                            @if(count($avr->repairs) > 0)
                                            <li class="list-group-item"><b>Ремонт: </b>
                                                @forelse($avr->repairs as $repair)
                                                    <a href="{{route('trk_repairs.show', $repair->id)}}">{{$repair->executed_at}}</a>
                                                @empty
                                                    отсутствует ...
                                                @endforelse</li>
                                            @endif
                                            @if(count($avr->tech_acts) > 0)
                                            <li class="list-group-item"><b>Тех. акт: </b>
                                                @forelse($avr->tech_acts as $tech_act)
                                                    <a href="{{route('tech_acts.show', $tech_act->id)}}">{{$tech_act->write_at}}</a>
                                                @empty
                                                    отсутствует ...
                                                @endforelse
                                            </li>
                                                @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <ul class="list-group mb-4">
                                            @if(count($avr->conditioner_checklists) > 0)
                                            <li class="list-group-item"><b>Чеклисты кондиционеров: </b>
                                                @forelse($avr->conditioner_checklists as $conditioner_checklist)
                                                    <br><a href="{{route('checklists_conditioner.show', $conditioner_checklist->id)}}">{{$conditioner_checklist->created_at . ', ' . $conditioner_checklist->trk_equipment->equipment_name->name}}</a>
                                                @empty
                                                    отсутствует ...
                                                @endforelse</li>
                                            @endif
                                            @if(count($avr->fancoil_checklists) > 0)
                                            <li class="list-group-item"><b>Чеклисты фанкойлов: </b>
                                                @forelse($avr->fancoil_checklists as $fancoil_checklist)
                                                    <br><a href="{{route('checklists_fancoil.show', $fancoil_checklist->id)}}">{{$fancoil_checklist->created_at . ', ' . $fancoil_checklist->trk_equipment->equipment_name->name}}</a>
                                                @empty
                                                    отсутствует ...
                                                @endforelse
                                            </li>
                                                @endif
                                                @if(count($avr->balk_checklists) > 0)
                                            <li class="list-group-item"><b>Чеклисты балок: </b>
                                                @forelse($avr->balk_checklists as $balk_checklist)
                                                   <br><a href="{{route('checklists_balk.show', $balk_checklist->id)}}">{{$balk_checklist->created_at . ', ' . $balk_checklist->trk_equipment->equipment_name->name}}</a>
                                                @empty
                                                    отсутствует ...
                                                @endforelse</li>
                                                @endif
                                                @if(count($avr->air_diffuser_checklists) > 0)
                                            <li class="list-group-item"><b>Чеклисты диффузоров: </b>
                                                <br><span>{{$avr->created_at}}</span>
                                                @forelse($avr->air_diffuser_checklists as $air_diffuser_checklist)
                                                    <br><a href="{{route('checklists_air_diffuser.show', $air_diffuser_checklist->id)}}">{{$air_diffuser_checklist->trk_equipment->equipment_name->name . ', точка ' . $air_diffuser_checklist->measuring_point_number . ', '}}{{$air_diffuser_checklist->air_direction_type == 0 ? 'приток' : 'вытяжка'}}</a>
                                                @empty
                                                    отсутствует ...
                                                @endforelse</li>
                                                @endif
                                                @if(count($avr->air_duct_checklists) > 0)
                                            <li class="list-group-item"><b>Чеклисты воздуховодов: </b>
                                                <br><span>{{$avr->created_at}}</span>
                                                @forelse($avr->air_duct_checklists as $air_duct_checklist)
                                                    <br><a href="{{route('checklists_air_duct.show', $air_duct_checklist->id)}}">{{$air_duct_checklist->trk_equipment->equipment_name->name}}</a>
                                                @empty
                                                    отсутствует ...
                                                @endforelse</li>
                                                @endif
                                                @if(count($avr->air_supply_checklists) > 0)
                                            <li class="list-group-item"><b>Чеклисты приточек: </b>
                                                <br><span>{{$avr->created_at}}</span>
                                                @forelse($avr->air_supply_checklists as $air_supply_checklist)
                                                    <br><a href="{{route('checklists_air_supply.show', $air_supply_checklist->id)}}">{{$air_supply_checklist->trk_equipment->equipment_name->name}}</a>
                                                @empty
                                                    отсутствует ...
                                                @endforelse</li>
                                                @endif
                                                @if(count($avr->air_extract_checklists) > 0)
                                            <li class="list-group-item"><b>Чеклисты вытяжек: </b>
                                                <br><span>{{$avr->created_at}}</span>
                                            @forelse($avr->air_extract_checklists as $air_extract_checklist)
                                                    <br><a href="{{route('checklists_air_extract.show', $air_extract_checklist->id)}}">{{$air_extract_checklist->trk_equipment->equipment_name->name}}</a>
                                                @empty
                                                    отсутствует ...
                                                @endforelse</li>
                                                @endif
                                        </ul>
                                    </div>
                                </div>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back()"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('avr update'))
                                        <a href="{{route('avrs.edit', $avr)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    <form action="{{route('avrs.print_one', $avr)}}" method="post">
                                        @csrf
                                        @method('post')
                                        <button type="submit" class="btn btn-outline-success me-2"><img
                                                src="{{asset('assets/images/backend/svg/printer.svg')}}" alt="delete"
                                                title="На печать"></button>
                                    </form>
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('avrs.destroy', $avr)}}" method="post">
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
