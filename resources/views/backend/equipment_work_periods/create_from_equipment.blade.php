@extends('layouts.backend.main')

@section('title', 'Периодические работы | Создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Периодические работы новые</h4>
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
                            <form action="{{route('equipment_work_periods.store_from_equipment')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="equipment_id" class="form-label form-label-sm">Оборудование
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input required readonly hidden name="equipment_id"
                                                   value="{{$trk_equipment->id}}">
                                            <input readonly class="form-control form-control-sm"
                                                   value="{{$trk_equipment->equipment_name->name}}">
                                            @error('equipment_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="equipments-add-parent-div mb-3">
                                            <div class="works-add-parent-div p-2 mt-3 mb-1 rounded"
                                                 style="background-color: rgba(218, 117, 255, 0.2)">
                                                <label for="basic-url" class="form-label form-label-sm">Тех. мероприятия
                                                    <span class="text-danger"><b>*</b></span></label>
                                                @forelse($trk_equipment->work_periods as $work_period)
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
                                                                           name="works[{{$work_period->id}}][work_name]"
                                                                           data-work-type-id="0"
                                                                            value="{{$work_period->work_name->name}}">
                                                                    <datalist id="works_list">
                                                                        @forelse($work_names as $work_name)
                                                                            <option data-work_key="{{$work_name->id}}"
                                                                                    value="{{$work_name->name}}" >
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
                                                            <div class="col-12 col-md-2 mb-1">
                                                                <input required name="works[{{$work_period->id}}][value]"
                                                                       class="form-control form-control-sm mt-1 mt-md-0 work-days"
                                                                       placeholder="Количество дней"
                                                                       type="number"
                                                                       data-work-comment-id="0"
                                                                        value="{{$work_period->repeat_days}}">
                                                            </div>
                                                            <div class="col-12 col-md-5 mb-1">
                                                            <textarea name="works[{{$work_period->id}}][comment]"
                                                                      class="form-control form-control-sm mt-1 mt-md-0 work-comment-textarea"
                                                                      rows="1"
                                                                      placeholder="Комментарии ... (необязательно)"
                                                                      data-work-comment-id="0">{{$work_period->comment}}</textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @empty
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
                                                                           name="works[0][work_name]"
                                                                           data-work-type-id="0">
                                                                    <datalist id="works_list">
                                                                        @forelse($work_names as $work_name)
                                                                            <option data-work_key="{{$work_name->id}}"
                                                                                    value="{{$work_name->name}}" >
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
                                                            <div class="col-12 col-md-2 mb-1">
                                                                <input required name="works[0][value]"
                                                                       class="form-control form-control-sm mt-1 mt-md-0 work-days"
                                                                       placeholder="Количество дней"
                                                                       type="number"
                                                                       data-work-comment-id="0">
                                                            </div>
                                                            <div class="col-12 col-md-5 mb-1">
                                                            <textarea name="works[0][comment]"
                                                                      class="form-control form-control-sm mt-1 mt-md-0 work-comment-textarea"
                                                                      rows="1"
                                                                      placeholder="Комментарии ... (необязательно)"
                                                                      data-work-comment-id="0"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforelse
                                            </div>
                                        </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('trk_equipments.show', $trk_equipment)}}"
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
    </div>
        <!-- profile init js -->
    <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
    <script src="{{asset('assets/js/equipment_work_periods/delete_work.js')}}"></script>
    <script src="{{asset('assets/js/equipment_work_periods/add_work.js')}}"></script>
    <script src="{{asset('assets/js/jquery.min.js')}}"></script>
@endsection
