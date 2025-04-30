@extends('layouts.backend.main')

@section('title', 'Просмотр | Технические акты')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Технические акты</h4>
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
                            <div class="card shadow p-3">
                                @if(!empty($tech_act->operation_applications[0]->id))
                                <div class="row">
                                    <div class="col mb-2">
                                        <label class="form-label form-label-sm" for="write_at">По заявке:
                                            <a href="{{route('operation_applications.show', $tech_act->operation_applications[0]->id)}}">
                                                {{$tech_act->operation_applications[0]->created_at}}
                                            </a>
                                        </label>
                                    </div>
                                </div>
                                @endif
                                <div class="row">
                                    <div class="col mb-3">
                                        <label class="form-label form-label-sm" for="write_at">Дата составления <span
                                                class="text-danger"><b>*</b></span></label>
                                        <input readonly required type="date" name="write_at"
                                               class="form-control form-control-sm" value="{{$tech_act->write_at}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-4">
                                        <label class="form-label form-label-sm" for="trk_id">ТРК <span
                                                class="text-danger"><b>*</b></span></label>
                                        <input readonly required type="text" name="trk_id"
                                               class="form-control form-control-sm" value="{{$tech_act->trk->name}}">
                                    </div>
                                </div>
                                <div class="executors-add-parent-div">
                                    <label class="form-label form-label-sm">Комиссия в составе: <span
                                            class="text-danger"><b>*</b></span></label>
                                    <ol>
                                        @forelse($tech_act->executors as $executor)
                                            @if(isset($executor->function->name))
                                                <li>{{$executor->function->name . ' - '}}{{$executor->name}}</li>
                                            @else
                                                <li>{{'Не заполнен профиль - ' . $executor->name}}</li>
                                            @endif
                                        @empty
                                            <li>нет данных ...</li>
                                        @endforelse
                                    </ol>
                                </div>
                                <div class="row">
                                    <div class="col mt-3">
                                        <label class="form-label form-label-sm" for="inspection_at"><b>Установила: </b></label><br>
                                        <label class="form-label form-label-sm mt-2" for="inspection_at">Дата: <span
                                                class="text-danger"><b>*</b></span></label>
                                        <input readonly required type="date" name="inspection_at"
                                               class="form-control form-control-sm"
                                               value="{{$tech_act->inspection_at}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mt-3">
                                        <label class="form-label form-label-sm" for="room_name">Месторасположение: <span
                                                class="text-danger"><b>*</b></span></label>
                                        <input readonly required type="text" name="room_name"
                                               class="form-control form-control-sm" value="{{$tech_act->room_name}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mt-3">
                                        <label class="form-label form-label-sm" for="equipment_name">Оборудование (в
                                            котором поломка): <span class="text-danger"><b>*</b></span></label>
                                        <input readonly required type="text" name="equipment_name"
                                               class="form-control form-control-sm"
                                               value="{{$tech_act->equipment_name}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mt-3">
                                        <label class="form-label form-label-sm" for="trouble_description">Что сломалось:
                                            <span class="text-danger"><b>*</b></span></label>
                                        <textarea readonly required name="trouble_description"
                                                  class="form-control form-control-sm">{{$tech_act->trouble_description}}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mt-3">
                                        <label class="form-label form-label-sm" for="reason_description">Причина: <span
                                                class="text-danger"><b>*</b></span></label>
                                        <textarea readonly required name="reason_description" rows="2"
                                                  class="form-control form-control-sm">{{$tech_act->reason_description}}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mt-3">
                                        <label class="form-label form-label-sm" for="recovery_method_description">Способ
                                            восстановления: <span class="text-danger"><b>*</b></span></label>
                                        <textarea readonly required name="recovery_method_description" rows="2"
                                                  class="form-control form-control-sm">{{$tech_act->recovery_method_description}}</textarea>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mt-4">
                                        <label class="form-label form-label-sm" for="spare_parts">Ориентировочная
                                            стоимость восстановления: <span class="text-danger"><b>*</b></span></label>
                                        <ol>
                                            @forelse($tech_act->spare_parts as $spare_part)
                                                <li>{{$spare_part->spare_part_name . ' - '}}{{$spare_part->price}}</li>
                                            @empty
                                                <li>нет данных ...</li>
                                            @endforelse
                                        </ol>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mt-3 mb-3">
                                        <label class="form-label form-label-sm" for="resumes">Комиссия решила: <span
                                                class="text-danger"><b>*</b></span></label>
                                        <ol>
                                            @forelse($tech_act->resumes as $resume)
                                                <li>{{$resume->resume_name->name}}</li>
                                            @empty
                                                <li>нет данных ...</li>
                                            @endforelse
                                        </ol>
                                    </div>
                                </div>
                                    @if(count($tech_act->avrs) > 0)
                                    <div class="row">
                                        <div class="col mb-3">
                                            <ul class="list-group mb-4">
                                                <li class="list-group-item"><b>Есть акт выполненных работ: </b>
                                                    @forelse($tech_act->avrs as $avr)
                                                        <a href="{{route('avrs.show', $avr->id)}}">{{$avr->date}}</a>
                                                    @empty
                                                        нет данных ...
                                                    @endforelse
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    @endif
                                    @if(count($tech_act->repairs) > 0)
                                        <div class="row">
                                            <div class="col mb-3">
                                                <ul class="list-group mb-4">
                                                    <li class="list-group-item"><b>Есть ремонт по этому акту: </b>
                                                        @forelse($tech_act->repairs as $repair)
                                                            <a href="{{route('trk_repairs.show', $repair->id)}}">{{$repair->created_at}}</a>
                                                        @empty
                                                            нет данных ...
                                                        @endforelse
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('tech_acts.index')}}"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    <a href="{{route('tech_acts.export', $tech_act)}}"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/printer.svg')}}" alt="print"
                                            title="На печать"></a>
                                    @if(auth()->user()->can('edit'))
                                        <a href="{{route('tech_acts.edit', $tech_act)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('tech_acts.destroy', $tech_act)}}" method="post">
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
            <!-- profile init js -->
            <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
