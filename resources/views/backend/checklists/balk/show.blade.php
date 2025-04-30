@extends('layouts.backend.main')

@section('title', 'Главная | Чеклист балки')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Чеклист балки</h4><a href="{{route('checklists_balk.create')}}"><img
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
                                        <b>ТРК: </b>{{$checklist->trk_room->trk->name ?? 'не выбрано'}}</li>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Помещение: </b><a href="{{route('trk_room.show', $checklist->trk_room->id)}}">{{$checklist->trk_room->room->name ?? 'не выбрано'}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Оборудование: </b><a href="{{route('trk_equipments.show', $checklist->trk_equipment->id)}}">{{$checklist->trk_equipment->equipment_name->name ?? 'не выбрано'}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Типоразмер балки: </b>{{$checklist->balk_size_type ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Скорость воздуха, м/сек: </b>{{$checklist->air_speed ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Расход воздуха, куб.м/час: </b>{{$checklist->air_flow_rate ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Давление воздуха, Па: </b>{{$checklist->air_pressure ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Воздух на входе, т: </b>{{$checklist->air_inlet_temperature ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Воздух на выходе, т: </b>{{$checklist->air_outlet_temperature ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Створки: </b>{{$checklist->air_flap ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Вода на входе, т: </b>{{$checklist->cold_water_inlet_temperature ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Вода на выходе, т: </b>{{$checklist->cold_water_outlet_temperature ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Перепад давления ХВС: </b>{{$checklist->cold_water_pressure_drop ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Клапан ХВС: </b>{{$checklist->cold_water_valve ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Расход ХВС: </b>{{$checklist->cold_water_rate ?? 'не выбрано'}}
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
                                        <a href="{{route('checklists_balk.edit', $checklist->id)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('checklists_balk.destroy', $checklist)}}" method="post">
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
