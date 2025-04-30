@extends('layouts.backend.main')

@section('title', 'Главная | Выполнение ремонта')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Выполнение ремонта</h4>
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
                            <form action="{{route('trk_repairs.done_progress_update', $trk_repair)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select disabled id="trk_id" class="form-select form-select-sm">
                                                @forelse($trks as $trk)
                                                    <option
                                                        value="{{$trk->id}}" {{old('trk_id', $trk_repair->trk_room->trk->id) === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="room_id" class="form-label form-label-sm">Помещение<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select disabled id="room_id" class="form-select form-select-sm">
                                                @forelse($rooms as $room)
                                                    <option
                                                        value="{{$room->id}}" {{old('room_id', $trk_repair->trk_room->room->id) == $room->id ? 'selected' : null}}>{{$room->name}}</option>
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
                                            <label for="equipment_id" class="form-label form-label-sm">Оборудование<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select disabled id="equipment_id" class="form-select form-select-sm">
                                                @forelse($equipments as $equipment)
                                                    <option
                                                        value="{{$equipment->id}}" {{old('equipment_id', $trk_repair->equipment_id) == $equipment->id ? 'selected' : null}}>{{$equipment->equipment_name->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('equipment_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="description" class="form-label form-label-sm">Ремонт <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <textarea disabled readonly required name="description"
                                                      class="form-control form-control-sm"
                                                      placeholder="Что планируется">{{old('description', $trk_repair->description)}}</textarea>
                                            @error('description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="executed_result" class="form-label form-label-sm">Что
                                                сделано</label>
                                            <textarea required autofocus name="executed_result"
                                                      class="form-control form-control-sm"
                                                      placeholder="Что было сделано">{{old('executed_result', $trk_repair->executed_result)}}</textarea>
                                            @error('executed_result')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <label for="done_progress" class="form-label form-label-sm">Процент
                                                исполнения: <span
                                                    id="percents_done_progress">{{$trk_repair->done_progress . '%'}}</span></label>
                                            <input value="{{old('done_progress', $trk_repair->done_progress)}}" type="range"
                                                   class="form-range" min="0" max="100" step="10" id="done_progress"
                                                   name="done_progress">
                                            @error('done_progress')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1">
                                        <div class="col-12 col-md-4 executors-add-parent-div">
                                            <label for="basic-url" class="form-label form-label-sm">Кто выполнял</label>
                                            <div class="input-group input-group-sm mb-2 executor-add-div">
                                                <input required type="text" list="1"
                                                       class="form-control form-control-sm" id="basic-url"
                                                       aria-describedby="basic-addon3" placeholder="Начните писать ..."
                                                       name="executor_names[]"
                                                        value="{{Auth::user()->name}}">
                                                <datalist id="1">
                                                    @forelse($executor_names as $executor)
                                                        <option data-room_key="{{$executor->id}}"
                                                                value="{{$executor->name}}">
                                                    @empty
                                                        <option data-room_key="" value="нет данных ...">
                                                    @endforelse
                                                </datalist>
                                                <span class="input-group-text executor-add-button"
                                                      id="basic-addon3"><img
                                                        src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                        alt="add" title="Добавить" height="20"></span>
                                                <span class="input-group-text executor-delete-button" id="basic-addon3"><img
                                                        src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                        alt="delete" title="Удалить" height="20"></span>
                                            </div>
                                            @error('executor_names.*')
                                            <div class="text-danger"
                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mt-2">
                                            <label for="executed_at" class="form-label form-label-sm">Дата и время
                                                выполнения</label>
                                            <input required class="form-control form-control-sm" type="datetime-local"
                                                   id="executed_at"
                                                   name="executed_at"
                                                   value="{{old('executed_at', $trk_repair->executed_at)}}"
                                                   min="2019-01-07T00:00" max="2050-12-14T00:00">
                                            @error('executed_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col my-4">
                                            <div class="btn-group-vertical btn-group-sm col-12 col-md-4" role="group" aria-label="Vertical radio toggle button group">
                                                <input type="radio" class="btn-check" name="operation_type" value="just_save" id="vbtn-radio1" autocomplete="off" checked>
                                                <label class="btn btn-outline-success" for="vbtn-radio1">Просто сохранить изменения</label>
                                                <input type="radio" class="btn-check" name="operation_type" value="save_and_create_spare_part_order" id="vbtn-radio2" autocomplete="off">
                                                <label class="btn btn-outline-success" for="vbtn-radio2">Заказать запчасти</label>
                                                <input type="radio" class="btn-check" name="operation_type" value="save_and_create_avr" id="vbtn-radio4" autocomplete="off">
                                                <label class="btn btn-outline-success" for="vbtn-radio4">Заполнить акт выполненных работ</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 input-group-sm">
                                        <a href="{{route('trk_repairs.show', $trk_repair)}}"
                                           class="btn btn-sm btn-outline-success col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                alt="back" title="Назад"></a>
                                        <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                title="Сохранить"></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
        <script src="{{asset('assets/js/trk_repairs/add_executor.js')}}" defer></script>
        <script src="{{asset('assets/js/trk_repairs/delete_executor.js')}}" defer></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
        <script src="{{asset('assets/js/trk_repairs/show_percents_for_progress_bar.js')}}"></script>
@endsection
