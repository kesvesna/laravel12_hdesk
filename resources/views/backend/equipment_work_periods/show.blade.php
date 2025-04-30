@extends('layouts.backend.main')

@section('title', 'Просмотр | Периодические работы оборудования')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Периодические работы оборудования</h4>
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
                                <ul class="list-group mb-3">
                                    <li class="list-group-item">
                                        <b>ТРК: </b>{{$equipment_work_period->trk_equipment->trk_room->trk->name}}</li>
                                    <li class="list-group-item">
                                        <b>Помещение: </b><a href="{{route('trk_room.show', $equipment_work_period->trk_equipment->trk_room->id)}}">{{$equipment_work_period->trk_equipment->trk_room->room->name}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Оборудование: </b><a href="{{route('trk_equipments.show', $equipment_work_period->trk_equipment->id)}}">{{$equipment_work_period->trk_equipment->equipment_name->name}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Работа: </b>{{$equipment_work_period->work_name->name}}</li>
                                    <li class="list-group-item"><b>Период выполнения по плану </b>{{$equipment_work_period->repeat_days ?? 'отсутствует'}} дн.</li>
                                    <li class="list-group-item"><b>Период выполнения по факту </b>{{$period_days == 0 ? 'пока неизвестно' : $period_days . ' дн.'}}</li>
                                    <li class="list-group-item"><b>Последнее </b>{{$equipment_work_period->last_was_at}}</li>
                                    <li class="list-group-item"><b>Следующее </b>{{$equipment_work_period->next_to_be_at}}</li>
                                    <li class="list-group-item">
                                        <b>Комментарий: </b>{{$equipment_work_period->comment ?? 'отсутствует'}}</li>
                                    <li class="list-group-item">
                                        <b>Акты с этой работой: {{count($avr_works) ?? 0}}{{' шт.'}}</b>
                                    @forelse($avr_works as $avr_work)
                                        <br>
                                           <a href="{{route('avrs.show', $avr_work->avr_id)}}">{{$avr_work->avr->date}}</a>
                                    @empty
                                        отсутствуют ...
                                    @endforelse
                                    </li>
                                </ul>
                                <ul class="list-group mb-3">
                                    <li class="list-group-item">
                                        <b>Автор: </b>{{$equipment_work_period->created_at . ', '}}{{$equipment_work_period->author->name}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Редактор: </b>{{$equipment_work_period->updated_at . ', '}}{{$equipment_work_period->last_editor->name}}
                                    </li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back();"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit'))
                                        <a href="{{route('equipment_work_periods.edit', $equipment_work_period)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form
                                            action="{{route('equipment_work_periods.destroy', $equipment_work_period)}}"
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
            <!-- profile init js -->
            <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
