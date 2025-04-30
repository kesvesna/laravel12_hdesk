@extends('layouts.backend.main')

@section('title', 'Просмотр | Название оборудования')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Название оборудования</h4>
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
                                    <li class="list-group-item"><b>Название: </b>{{$equipment_name->name}}</li>
                                    <li class="list-group-item"><b>Состоит из:</b>
                                        @forelse($language as $item)
                                            {{$item . ', '}}
                                        @empty
                                            <span>нет данных ...</span>
                                        @endforelse
                                    </li>
                                    <li class="list-group-item"><b>Используется: </b>{{count($trk_equipments) . 'шт.'}}</li>
                                    @if(count($trk_equipments) > 0)
                                        @forelse($trk_equipments as $trk_equipment)
                                            <li class="list-group-item"><a href="{{route('trk_equipments.show', $trk_equipment->id)}}">{{$trk_equipment->trk_room->trk->name . ' - ' . $trk_equipment->trk_room->building->name . ' - эт. ' . $trk_equipment->trk_room->floor->name . ' - пом. ' . $trk_equipment->trk_room->room->name}}</a></li>
                                        @empty
                                        @endforelse
                                        @role('sadmin')
                                        <form action="{{route('trk_equipment.change_equipment_name_in_trk_equipments', $trk_equipment)}}" method="post">
                                            @csrf
                                            @method('patch')
                                            <label for="equipment_name_id" class="form-label form-label-sm mt-3">Везде заменить на:</label>
                                            <select required name="equipment_name_id" class="form-select form-select-sm">
                                                <option value="">не заменять</option>
                                                @forelse($equipment_names as $name)
                                                    <option value="{{$name->id}}">{{$name->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2 mt-3"><img
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
                                        <a href="{{route('equipment_names.edit', $equipment_name)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('equipment_names.destroy', $equipment_name)}}"
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
