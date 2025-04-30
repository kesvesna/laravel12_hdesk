@extends('layouts.backend.main')

@section('title', 'Просмотр | Название работы')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Название работы</h4>
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
                                @include('components.backend.message')
                                <ul class="list-group mb-3">
                                    <li class="list-group-item"><b>Название: </b>{!! str_replace(' ', '_', $work_name->name) !!}</li>
                                    <li class="list-group-item"><b>Используется в актах: </b>{{count($avr_works) . 'шт.'}}</li>
                                    @if(count($avr_works) > 0)
                                        @if(count($avr_works) < 2000)
                                    @forelse($avr_works as $avr_work)
                                        <li class="list-group-item"><a href="{{route('avrs.show', $avr_work->avr_id)}}">{{$avr_work->avr->date}}</a></li>
                                    @empty
                                    @endforelse
                                        @endif
                                        @role('sadmin')
                                        <form action="{{route('avr_works.change_work_name_in_avrs', $work_name)}}" method="post">
                                            @csrf
                                            @method('patch')
                                            <div class="works-add-parent-div p-2 mt-3 mb-1 rounded"
                                                 style="background-color: rgba(218, 117, 255, 0.2)">
                                                <label for="basic-url" class="form-label form-label-sm">Во всех актах заменить на:
                                                    <span class="text-danger"><b>*</b></span></label>
                                                <div class="work-add-div mb-1 mb-md-0">
                                                    <div class="row row-cols-1">
                                                        <div class="col-12 col-md-5">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text work-add-button"><img
                                                                        src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                        alt="add" title="Добавить" height="20"></span>
                                                                <span class="input-group-text work-delete-button"><img
                                                                        src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                        alt="delete" title="Удалить" height="20"></span>
                                                                <select required name="works[]" class="form-select form-select-sm work-type-select"  data-work-type-id="0">
                                                                    <option value="">не заменять</option>
                                                                    @forelse($work_names as $work)
                                                                        <option value="{{$work->id}}">{{$work->name}}</option>
                                                                    @empty
                                                                        <option value="">нет данных ...</option>
                                                                    @endforelse
                                                                </select>
                                                            </div>
                                                            @error('works.*')
                                                            <div class="text-danger"
                                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2 mt-3 mb-3"><img
                                                    src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                    title="Сохранить">
                                            </button>
                                        </form>
                                        @endrole
                                    @endif
                                    <li class="list-group-item"><b>Используется в тех. мероприятиях на оборудовании: </b>{{count($period_works) . 'шт.'}}</li>
                                    @if(count($period_works) > 0)

                                        @if(count($period_works) < 1000)
                                        @forelse($period_works as $period_work)
                                            <li class="list-group-item"><a href="{{route('equipment_work_periods.show', $period_work->id)}}">{{$period_work->trk_equipment->equipment_name->name ?? 'нет данных ...'}}</a></li>
                                        @empty
                                        @endforelse
                                        @endif
                                        @role('sadmin')
                                        <form action="{{route('equipment_work_periods.change_work_name_in_equipment_work_periods', $work_name)}}" method="post">
                                            @csrf
                                            @method('patch')
                                            <div class="works-add-parent-div p-2 mt-3 mb-1 rounded"
                                                 style="background-color: rgba(218, 117, 255, 0.2)">
                                                <label for="basic-url" class="form-label form-label-sm">Во всех тех. мероприятиях заменить на:
                                                    <span class="text-danger"><b>*</b></span></label>
                                                <div class="work-add-div mb-1 mb-md-0">
                                                    <div class="row row-cols-1">
                                                        <div class="col-12 col-md-5">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text work-add-button"><img
                                                                        src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                        alt="add" title="Добавить" height="20"></span>
                                                                <span class="input-group-text work-delete-button"><img
                                                                        src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                        alt="delete" title="Удалить" height="20"></span>
                                                                <select required name="works[1]" class="form-select form-select-sm work-type-select"  data-work-type-id="0">
                                                                    <option value="">не заменять</option>
                                                                    @forelse($work_names as $work)
                                                                        <option value="{{$work->id}}">{{$work->name}}</option>
                                                                    @empty
                                                                        <option value="">нет данных ...</option>
                                                                    @endforelse
                                                                </select>
                                                            </div>
                                                            @error('works.*')
                                                            <div class="text-danger"
                                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-12 col-md-7 mb-1">
                                                            <input required
                                                                   name="works[1][period_days]"
                                                                   class="form-control form-control-sm mt-1 mt-md-0 work-period-day-input"
                                                                   placeholder="Период выполнения в днях"
                                                                   data-work-period-day-id="0"
                                                                   type="number"
                                                            >
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2 mt-3 mb-3"><img
                                                    src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                    title="Сохранить">
                                            </button>
                                        </form>
                                        @endrole
                                    @endif
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back()"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit'))
                                        <a href="{{route('work_names.edit', $work_name)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('work_names.destroy', $work_name)}}" method="post">
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
            <script src="{{asset('assets/js/jquery.min.js')}}"></script>
            <script src="{{asset('assets/js/work_names/add_work.js')}}"></script>
@endsection
