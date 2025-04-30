@extends('layouts.backend.main')

@section('title', 'Главная | Чеклист фанкойла создание')

@section('content')
    @csrf
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Чеклист фанкойла создание</h4>
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
                            <form action="{{route('checklists_fancoil.store_from_trk_room', $trk_room)}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col-12 col-md-3 mb-3">
                                            <label for="trk_room_id" class="form-label form-label-sm">ТРК</label>
                                            <input readonly disabled value="{{$trk_room->trk->name}}"
                                                   class="form-control form-control-sm">
                                            @error('trk_room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col-12 col-md-3 mb-3">
                                            <label for="building_id" class="form-label form-label-sm">Блок/Зона</label>
                                            <input readonly disabled value="{{$trk_room->building->name}}"
                                                   class="form-control form-control-sm">
                                        </div>
                                        <div class="col-12 col-md-3 mb-3">
                                            <label for="floor_id" class="form-label form-label-sm">Этаж/Отметка</label>
                                            <input readonly disabled value="{{$trk_room->floor->name}}"
                                                   class="form-control form-control-sm">
                                        </div>
                                        <div class="col-12 col-md-3 mb-3">
                                            <label for="room_id" class="form-label form-label-sm">Помещение</label>
                                            <input readonly disabled value="{{$trk_room->room->name}}"
                                                   class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-3 mb-3">
                                            <label for="system_id" class="form-label form-label-sm">Система</label>
                                            <select required disabled name="system_id" class="form-select form-select-sm">
                                                    <option value="{{$system->id}}">{{$system->name}}</option>
                                            </select>
                                            @error('system_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 mb-2">
                                        <div class="col-12 col-md-3">
                                            <label for="basic-url" class="form-label form-label-sm">Работы (для акта)
                                                <span class="text-danger"><b>*</b></span>
                                                <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                        data-bs-target="#exampleModal8">Что выбрать?
                                                </button>
                                            </label>
                                            <div class="input-group input-group-sm mb-3">
                                                <select required multiple size="3" name="work_types[]" id="work_types"
                                                        class="form-select form-select-sm">
                                                    @forelse($work_types as $work_type)
                                                        <option
                                                            value="{{$work_type->id}}">{{$work_type->name}}</option>
                                                    @empty
                                                        <option value="">нет данных ...</option>
                                                    @endforelse
                                                </select>
                                            </div>
                                            @error('work_types.*')
                                            <div class="text-danger"
                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 mb-2">
                                        <div class="col-12">
                                            <label for="comment" class="form-label form-label-sm">Комментарии (для акта)
                                            </label>
                                            <div class="input-group input-group-sm mb-3">
                                                <textarea class="form-control form-control-sm" name="comment" placeholder="Любые комментарии (необязательно)"></textarea>
                                            </div>
                                            @error('comment')
                                            <div class="text-danger"
                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1">
                                                                        <div class="col mb-2">
                                                                            <!-- Button trigger modal responsibility -->
                                                                            <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal2">Расшифровка обозначений</button>
                                                                        </div>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-sm" id="equipment_table">
                                            <thead>
                                            <tr>
                                                <th>Фанкойл</th>
                                                <th>T_air_in</th>
                                                <th>T_air_out</th>
                                                <th>T_water_in</th>
                                                <th>T_water_out</th>
                                                <th>Комментарий</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr class="equipment-add-tr">
                                                <td>
                                                    <div style="width: 13rem;" class="input-group input-group-sm equipment-add-div">
                                                        <span class="input-group-text equipment-add-button">
                                                            <img src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="add" title="Добавить" height="20">
                                                        </span>
                                                        <span class="input-group-text equipment-delete-button">
                                                            <img src="{{asset('assets/images/backend/svg/trash3.svg')}}" alt="delete" title="Удалить" height="20">
                                                        </span>
                                                        <select required name="equipments[0][id]" class="form-select form-select-sm equipment-name-input">
                                                            @forelse($trk_room_equipments as $equipment)
                                                                <option value="{{$equipment->id}}" data-equipment-id="{{$equipment->id}}">{{$equipment->equipment_name->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                    @error('equipment_names.*')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                        @enderror
                                                </td>
                                    <td>
                                        <input required class="form-control form-control-sm air_inlet_temperature" name="equipments[0][air_inlet_temperature]" type="number" step="0.1" placeholder="23.5">
                                    </td>
                                    <td>
                                        <input required class="form-control form-control-sm air_outlet_temperature" name="equipments[0][air_outlet_temperature]" type="number" step="0.1" placeholder="16.2">
                                    </td>
                                                <td>
                                                    <input required class="form-control form-control-sm cold_water_inlet_temperature" name="equipments[0][cold_water_inlet_temperature]" type="number" step="0.1" placeholder="7.0">
                                                </td>
                                                <td>
                                                    <input required class="form-control form-control-sm cold_water_outlet_temperature" name="equipments[0][cold_water_outlet_temperature]" type="number" step="0.1" placeholder="14.5">
                                                </td>
                                    <td>
                                        <input style="width: 13rem;" class="form-control form-control-sm comment" name="equipments[0][comment]" type="text" placeholder="Любой комментарий">
                                    </td>
                                </tr>
                                            </tbody>
                                        </table>
                                        </div>
                                    </div>
                                    <div class="executors-add-parent-div">
                                        <label class="form-label form-label-sm">Исполнители <span
                                                class="text-danger"><b>*</b></span></label>
                                        <div class="executor-add-div">
                                            <div class="row row-cols-1">
                                                <div class="col-12 col-md-4">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <span class="input-group-text executor-add-button"><img
                                                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                alt="add" title="Добавить" height="20"></span>
                                                        <span class="input-group-text executor-delete-button"><img
                                                                src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                alt="delete" title="Удалить" height="20"></span>
                                                        <input value="{{Auth::user()->name}}" required type="text"
                                                               list="executors_list"
                                                               class="form-control form-control-sm"
                                                               placeholder="Начните писать ..."
                                                               name="executors[]">
                                                        <datalist id="executors_list">
                                                            @forelse($executors as $executor)
                                                                <option data-equipment_key="{{$executor->id}}"
                                                                        value="{{$executor->name}}">
                                                            @empty
                                                                <option data-equipment_key="" value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </div>
                                                    @error('executors.*')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-3 mt-4">
                                        <div class="col">
                                            <div class="input-group input-group-sm">
                                                <a href="javascript:history.back();"
                                                   class="btn btn-sm btn-outline-success col-6"><img
                                                        src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                        alt="back" title="Назад"></a>
                                                <button type="submit" class="btn btn-sm btn-outline-danger col-6"><img
                                                        src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                        title="Сохранить"></button>
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal alert -->
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel2">Параметры в таблице</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>T_air_in температура воздуха на входе в фанкойл</p>
                        <p>T_air_out температура воздуха на выходе из фанкойла</p>
                        <p>T_water_in температура воды на входе в фанкойл</p>
                        <p>T_water_out температура воды на выходе из фанкойла</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal 8 -->
        <div class="modal fade" id="exampleModal8" tabindex="-1" aria-labelledby="exampleModalLabel8"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel3">Какое ТО выбрать</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Поддерживается множественный выбор<br> при зажатой клавише ctrl</p>
                        <p>При выполнении <b>ТО 4</b>: выбираем <b>только ТО 4</b>.</p>
                        <p>При выполнении <b>ТО 5</b>: выбираем <b>ТО 5 и ТО 4</b>,<br> т.к. ТО 4 входит в ТО 5.</p>
                        <p>При выполнении <b>ТО 6</b>: выбираем <b>ТО 6, ТО 5 и ТО 4</b>,<br> т.к. ТО 4 и ТО 5 входят в ТО 6.</p>
                        <p>Какой в этом смысл?</p>
                        <p>Это будет использоваться при планировании технических мероприятий.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
        <script src="{{asset('assets/js/checklists/fancoil/add_equipment.js')}}" defer></script>
        <script src="{{asset('assets/js/checklists/fancoil/delete_equipment.js')}}" defer></script>
        <script src="{{asset('assets/js/checklists/fancoil/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/checklists/fancoil/delete_executor.js')}}"></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
@endsection
