@extends('layouts.backend.main')

@section('title', 'Просмотр | Параметры оборудования')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Параметры оборудования</h4>
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
                                        <b>ТРК: </b>{{$equipment_parameter->trk_equipment->trk_room->trk->name}}</li>
                                    <li class="list-group-item">
                                        <b>Оборудование: </b>{{$equipment_parameter->trk_equipment->equipment_name->name}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Параметр: </b>{{$equipment_parameter->parameter_name->name}}</li>
                                    <li class="list-group-item">
                                        <b>Величина: </b>{{$equipment_parameter->value ?? 'отсутствует'}}</li>
                                    <li class="list-group-item">
                                        <b>Комментарий: </b>{{$equipment_parameter->comment ?? 'отсутствует'}}</li>
                                    <li class="list-group-item">
                                        <hr>
                                    </li>
                                    <li class="list-group-item"><b>Создан: </b>{{$equipment_parameter->created_at}}</li>
                                    <li class="list-group-item"><b>Автор: </b>{{$equipment_parameter->author->name}}
                                    </li>
                                    <li class="list-group-item"><b>Исправлен: </b>{{$equipment_parameter->updated_at}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Редактор: </b>{{$equipment_parameter->last_editor->name}}</li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back();"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit'))
                                        <a href="{{route('equipment_parameters.edit', $equipment_parameter)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('equipment_parameters.destroy', $equipment_parameter)}}"
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
