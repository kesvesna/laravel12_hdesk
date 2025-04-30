@extends('layouts.backend.main')

@section('title', 'Главная | АВР создание')

@section('content')
    @csrf
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">АВР создание</h4>
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
                            <form action="{{route('avrs.store_from_trk_equipment', $trk_equipment)}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="col mb-3">
                                        <!-- Button trigger modal responsibility -->
                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal2">Как создать?
                                        </button>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-4">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">Дата</label>
                                            <input autofocus required class="form-control form-control-sm" name="date"
                                                   type="date" value="{{date('Y-m-d')}}">
                                            @error('date')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required readonly type="text"
                                                   class="form-control form-control-sm"
                                                   placeholder="Начните писать ..."
                                                   name="trk_id" value="{{$trk_equipment->trk_room->trk->name}}">
                                        </div>
                                        <div class="col mb-3">
                                            <label for="system_id" class="form-label form-label-sm">Система <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required readonly type="text"
                                                   class="form-control form-control-sm"
                                                   placeholder="Начните писать ..."
                                                   name="system_id" value="{{$trk_equipment->system->name}}">
                                        </div>
                                        <div class="col mb-4">
                                            <label for="room_id" class="form-label form-label-sm">Помещение <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required readonly type="text"
                                                   class="form-control form-control-sm"
                                                   placeholder="Начните писать ..."
                                                   name="room_id" value="{{$trk_equipment->trk_room->room->name}}">
                                        </div>
                                    </div>
                                    <div class="equipments-add-parent-div mb-3">
                                        <div class="equipment-add-div p-2 my-3 rounded"
                                             style="background-color: rgba(145, 135, 255, 0.2)">
                                            <div class="row row-cols-1">
                                                <div class="col-12 col-md-4">
                                                    <label for="basic-url" class="form-label form-label-sm">Оборудование
                                                        <span class="text-danger"><b>*</b></span>
                                                    </label>
                                                    <div class="input-group input-group-sm mb-3">
                                                        <input readonly required type="text"
                                                               class="form-control form-control-sm equipment-name-input"
                                                               placeholder="Начните писать ..."
                                                               name="equipment[]" data-equipment-id="0" value="{{$trk_equipment->equipment_name->name}}">
                                                    </div>
                                                    @error('equipment_names.*')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
{{--                                                    <div class="form-check">--}}
{{--                                                        <input class="form-check-input" type="checkbox" value="" id="copyToNextEquipment">--}}
{{--                                                        <label class="form-check-label" for="flexCheckDefault">--}}
{{--                                                            Копировать все в следующее оборудование--}}
{{--                                                        </label>--}}
{{--                                                    </div>--}}
                                                </div>
                                            </div>
                                            <div class="works-add-parent-div p-2 mt-3 mb-1 rounded"
                                                 style="background-color: rgba(218, 117, 255, 0.2)">
                                                <label for="basic-url" class="form-label form-label-sm">Тех. мероприятия
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
                                                                <input required type="text" list="works_list"
                                                                       class="form-control form-control-sm work-type-input"
                                                                       placeholder="Начните писать ..."
                                                                       name="equipment[0][work][0][type]"
                                                                       data-work-type-id="0">
                                                                <datalist id="works_list">
                                                                    @forelse($works as $work)
                                                                        <option data-work_key="{{$work->id}}"
                                                                                value="{{$work->name}}">
                                                                    @empty
                                                                        <option data-work_key="" value="нет данных ...">
                                                                    @endforelse
                                                                </datalist>
                                                            </div>
                                                            @error('works.*')
                                                            <div class="text-danger"
                                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-12 col-md-7 mb-1">
                                                            <textarea name="equipment[0][work][0][comment]"
                                                                      class="form-control form-control-sm mt-1 mt-md-0 work-comment-textarea"
                                                                      rows="1"
                                                                      placeholder="Комментарии ... (необязательно)"
                                                                      data-work-comment-id="0"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="spare-parts-add-parent-div p-2 mt-3 mb-1 rounded"
                                                 style="background-color: rgba(255, 159, 117, 0.2)">
                                                <label for="basic-url" class="form-label form-label-sm">Запчасти</label>
                                                <div class="spare-part-add-div mb-1 mb-md-0">
                                                    <div class="row row-cols-1">
                                                        <div class="col-12 col-md-5">
                                                            <div class="input-group input-group-sm">
                                                                <span
                                                                    class="input-group-text spare-part-add-button"><img
                                                                        src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                        alt="add" title="Добавить" height="20"></span>
                                                                <span class="input-group-text spare-part-delete-button"><img
                                                                        src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                        alt="delete" title="Удалить" height="20"></span>
                                                                <input type="text" list="spare_parts_list"
                                                                       class="form-control form-control-sm spare-part-name-input"
                                                                       placeholder="Начните писать ..."
                                                                       name="equipment[0][spare_part][0][name]"
                                                                       data-spare-part-name-id="0">
                                                                <datalist id="spare_parts_list">
                                                                    @forelse($spare_parts as $spare_part)
                                                                        <option
                                                                            data-spare_part_key="{{$spare_part->id}}"
                                                                            value="{{$spare_part->name}}">
                                                                    @empty
                                                                        <option data-spare_part_key=""
                                                                                value="нет данных ...">
                                                                    @endforelse
                                                                </datalist>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-7 mb-1">
                                                            <div class="input-group input-group-sm mt-1 mt-md-0">
                                                                <input type="text"
                                                                       class="form-control form-control-sm spare-part-model-input"
                                                                       placeholder="Модель"
                                                                       name="equipment[0][spare_part][0][model]"
                                                                       data-spare-part-model-id="0">
                                                                <input type="number" step="0.1"
                                                                       class="form-control form-control-sm spare-part-value-input"
                                                                       placeholder="Количество"
                                                                       name="equipment[0][spare_part][0][value]"
                                                                       data-spare-part-value-id="0">
                                                            </div>
                                                        </div>
                                                        @error('spare_parts.*')
                                                        <div class="text-danger"
                                                             style="margin-top: -1rem !important;">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
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
                                    <div class="row row-cols-1 row-cols-md-3">
                                        <div class="col mt-4">
                                            <div class="input-group input-group-sm">
                                                <a href="{{route('avrs.index')}}"
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
                        <h1 class="modal-title fs-5" id="exampleModalLabel2">Заполнение акта</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Если в выпадающих списках нет Ваших</p>
                        <p>ТРК, Блок/Зона, Этаж, Помещение, Оборудование,</p>
                        <p>Тех.мероприятие, Запчасти,</p>
                        <p>попросите админа их создать.</p>
                        <p>Невозможно создать акт для несуществующего оборудования или тех. мероприятия.</p>
                        <p>Все данные по мере использования сайта будут обобщены и стандартизированы.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
        <script src="{{asset('assets/js/avrs/add_equipment.js')}}"></script>
        <script src="{{asset('assets/js/avrs/delete_equipment.js')}}"></script>
        <script src="{{asset('assets/js/avrs/delete_work.js')}}"></script>
        <script src="{{asset('assets/js/avrs/delete_spare_part.js')}}"></script>
        <script src="{{asset('assets/js/avrs/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/avrs/delete_executor.js')}}"></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
@endsection
