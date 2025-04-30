@extends('layouts.backend.main')

@section('title', 'Главная | Редактирование заявки в эксплуатацию')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Редактирование заявки в эксплуатацию</h4>
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
                            <form action="{{route('operation_applications.update', $operation_application)}}"
                                  method="post">
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
                                            <select required name="trk_id" id="trk_id"
                                                    class="form-select form-select-sm">
                                                @forelse($trks as $trk)
                                                    <option
                                                        value="{{$trk->id}}" {{old('trk_id', $operation_application->trk->id) === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
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
                                            <label for="division_id" class="form-label form-label-sm">Подразделение<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select required name="division_id" id="division_id"
                                                    class="form-select form-select-sm">
                                                @forelse($divisions as $division)
                                                    <option
                                                        value="{{$division->id}}" {{old('division_id', $operation_application->division_id) === $division->id ? 'selected' : null}}>{{$division->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('division_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="trouble_description" class="form-label form-label-sm">Проблема
                                                <span class="text-danger"><b>*</b></span></label>
                                            <textarea required name="trouble_description"
                                                      class="form-control form-control-sm"
                                                      placeholder="Где проблема и в чем она">{{old('trouble_description', $operation_application->trouble_description)}}</textarea>
                                            @error('trouble_description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="done_description" class="form-label form-label-sm">Что
                                                сделано</label>
                                            <textarea required name="done_description"
                                                      class="form-control form-control-sm"
                                                      placeholder="Что было сделано для решения проблемы">{{old('result_description', $operation_application->result_description)}}</textarea>
                                            @error('done_description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="done_percents" class="form-label form-label-sm">Процент
                                                выполнения: <span
                                                    id="percents_done_progress">{{$operation_application->done_percents . '%'}}</span></label>
                                            <input value="{{old('done_percents', $operation_application->done_percents)}}" type="range"
                                                   class="form-range" min="0" max="100" step="10" id="done_percents"
                                                   name="done_percents">
                                            @error('done_percents')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
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
                                                        <input required type="text"
                                                               list="executors_list"
                                                               class="form-control form-control-sm"
                                                               placeholder="Начните писать ..."
                                                               name="executors[]"
                                                                value="{{$operation_application->done_author->name ?? auth()->user()->name}}">
                                                        <datalist id="executors_list">
                                                            @forelse($executors as $executor)
                                                                <option data-equipment_key="{{$executor->id}}"
                                                                        value="{{$executor->name}}">
                                                            @empty
                                                                <option data-equipment_key="" value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </div>
                                                    @error('equipment_names.*')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4 mt-3">
                                            <label for="done_at" class="form-label form-label-sm">Дата и время
                                                выполнения</label>
                                            <input required class="form-control form-control-sm" type="datetime-local"
                                                   id="done_at"
                                                   name="done_at"
                                                   value="{{old('done_at', $operation_application->done_at)}}"
                                                   min="2019-01-07T00:00" max="2050-12-14T00:00">
                                            @error('done_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="avr_id" class="form-label form-label-sm">Прикрепить акт к этой заявке</label>
                                            <select name="avr_id" id="avr_id"
                                                    class="form-select form-select-sm">
                                                    <option value="">не прикреплять ничего</option>
                                                @forelse($avrs as $avr)
                                                    <option
                                                        value="{{$avr->id}}">{{$avr->trk_room->trk->name . ' - ' . $avr->trk_room->building->name . ' - ' . $avr->trk_room->floor->name . ' - ' . $avr->trk_room->room->name . ' - ' . $avr->avr_equipments->first()->trk_equipment->equipment_name->name . ' - ' . $avr->author->name }}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('avr_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 input-group-sm">
                                        <a href="{{route('operation_applications.show', $operation_application)}}"
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
        <script src="{{asset('assets/js/operation_applications/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/operation_applications/delete_executor.js')}}"></script>
        <script src="{{asset('assets/js/operation_applications/show_percents_for_progress_bar.js')}}"></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
@endsection
