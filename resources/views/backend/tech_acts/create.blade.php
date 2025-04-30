@extends('layouts.backend.main')

@section('title', 'Главная | Технические акты создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Технические акты создание</h4>
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
                            <form action="{{route('tech_acts.store')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="write_at">Дата составления
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input required type="date" name="write_at"
                                                   class="form-control form-control-sm" value="{{date('Y-m-d')}}">
                                            @error('write_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="trk_id">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="trk_id" id="trk_id"
                                                    class="form-select form-select-sm">
                                                @forelse($trks as $trk)
                                                    <option
                                                        value="{{$trk->id}}" {{old('trk_id') == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="executors-add-parent-div">
                                        <label class="form-label form-label-sm">Комиссия в составе: <span
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
                                                               name="users[]">
                                                        <datalist id="executors_list">
                                                            @forelse($users as $user)
                                                                <option data-equipment_key="{{$user->id}}"
                                                                        value="{{$user->name}}">
                                                            @empty
                                                                <option data-equipment_key="" value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </div>
                                                    @error('users.*')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mt-5">
                                            <label class="form-label form-label-sm"
                                                   for="inspection_at"><b>Установила: </b></label><br>
                                            <label class="form-label form-label-sm mt-2" for="inspection_at">Дата: <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required type="date" name="inspection_at"
                                                   class="form-control form-control-sm" value="{{date('Y-m-d')}}">
                                            @error('inspection_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mt-3">
                                            <label class="form-label form-label-sm" for="room_name">Месторасположение:
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input required type="text" name="room_name"
                                                   class="form-control form-control-sm" value="{{old('room_name')}}">
                                            @error('room_name')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mt-3">
                                            <label class="form-label form-label-sm" for="equipment_name">Оборудование (в
                                                котором поломка): <span class="text-danger"><b>*</b></span></label>
                                            <input required type="text" name="equipment_name"
                                                   class="form-control form-control-sm"
                                                   value="{{old('equipment_name')}}">
                                            @error('equipment_name')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mt-3">
                                            <label class="form-label form-label-sm" for="trouble_description">Что
                                                сломалось: <span class="text-danger"><b>*</b></span></label>
                                            <textarea required name="trouble_description"
                                                      class="form-control form-control-sm">{{old('trouble_description')}}</textarea>
                                            @error('trouble_description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mt-3">
                                            <label class="form-label form-label-sm" for="reason_description">Причина:
                                                <span class="text-danger"><b>*</b></span></label>
                                            <textarea required name="reason_description" rows="2"
                                                      class="form-control form-control-sm">{{old('reason_description')}}</textarea>
                                            @error('reason_description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mt-3 mb-3">
                                            <label class="form-label form-label-sm" for="recovery_method_description">Способ
                                                восстановления: <span class="text-danger"><b>*</b></span></label>
                                            <textarea required name="recovery_method_description" rows="2"
                                                      class="form-control form-control-sm">{{old('recovery_method_description')}}</textarea>
                                            @error('recovery_method_description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="tech-act-spare-parts-add-parent-div">
                                        <label class="form-label form-label-sm">Ориентировочная стоимость
                                            восстановления: <span
                                                class="text-danger"><b>*</b></span></label>
                                        <div class="tech-act-spare-part-add-div shadow-sm p-1 p-md-2">
                                            <div class="row row-cols-1">
                                                <div class="col-12 col-md-10">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <span
                                                            class="input-group-text tech-act-spare-part-add-button"><img
                                                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                alt="add" title="Добавить" height="20"></span>
                                                        <span
                                                            class="input-group-text tech-act-spare-part-delete-button"><img
                                                                src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                alt="delete" title="Удалить" height="20"></span>
                                                        <input required type="text"
                                                               list="spare_list"
                                                               class="form-control form-control-sm"
                                                               placeholder="Запчасть и количество"
                                                               name="spare_parts[]">
                                                    </div>

                                                    @error('spare_parts.*')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-6 col-md-2">
                                                    <input required type="number"
                                                           step="0.1"
                                                           class="form-control form-control-sm"
                                                           placeholder="Цена"
                                                           name="prices[]">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tech-act-resumes-add-parent-div mt-4">
                                        <label class="form-label form-label-sm">Комиссия решила: <span
                                                class="text-danger"><b>*</b></span></label>
                                        <div class="tech-act-resume-add-div">
                                            <div class="row row-cols-1">
                                                <div class="col-12">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <span class="input-group-text tech-act-resume-add-button"><img
                                                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                alt="add" title="Добавить" height="20"></span>
                                                        <span
                                                            class="input-group-text tech-act-resume-delete-button"><img
                                                                src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                alt="delete" title="Удалить" height="20"></span>
                                                        <input required type="text"
                                                               list="resumes_list"
                                                               class="form-control form-control-sm"
                                                               placeholder="Начните писать ..."
                                                               name="resumes[]">
                                                        <datalist id="resumes_list">
                                                            @forelse($resumes as $resume)
                                                                <option data-equipment_key="{{$resume->id}}"
                                                                        value="{{$resume->name}}">
                                                            @empty
                                                                <option data-equipment_key="" value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </div>
                                                    @error('resumes.*')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col my-4">
                                            <div class="btn-group-vertical btn-group-sm col-12 col-md-4" role="group" aria-label="Vertical radio toggle button group">
                                                <input type="radio" class="btn-check" name="operation_type" value="just_save" id="vbtn-radio1" autocomplete="off" checked>
                                                <label class="btn btn-outline-success" for="vbtn-radio1">Просто сохранить этот акт</label>
                                                <input type="radio" class="btn-check" name="operation_type" value="save_and_create_repair" id="vbtn-radio2" autocomplete="off">
                                                <label class="btn btn-outline-success" for="vbtn-radio2">Запланировать ремонт</label>
                                                <input type="radio" class="btn-check" name="operation_type" value="save_and_create_avr" id="vbtn-radio4" autocomplete="off">
                                                <label class="btn btn-outline-success" for="vbtn-radio4">Заполнить акт выполненных работ</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('tech_acts.index')}}"
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
        <script src="{{asset('assets/js/tech_acts/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/tech_acts/delete_executor.js')}}"></script>
        <script src="{{asset('assets/js/tech_acts/add_spare_part.js')}}"></script>
        <script src="{{asset('assets/js/tech_acts/delete_spare_part.js')}}"></script>
        <script src="{{asset('assets/js/tech_acts/add_resume.js')}}"></script>
        <script src="{{asset('assets/js/tech_acts/delete_resume.js')}}"></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
@endsection
