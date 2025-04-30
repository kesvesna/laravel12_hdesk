@extends('layouts.backend.main')

@section('title', 'Периодические работы оборудования | Редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Редактирование Периодические работы</h4>
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
                            <form action="{{route('equipment_work_periods.update', $equipment_work_period)}}"
                                  method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="equipment_id" class="form-label form-label-sm">Оборудование
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input required readonly hidden name="equipment_id"
                                                   value="{{$equipment_work_period->equipment_id}}">
                                            <input readonly disabled class="form-control form-control-sm"
                                                   value="{{$equipment_work_period->trk_equipment->equipment_name->name}}">
                                            @error('equipment_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="work_name" class="form-label form-label-sm">Параметр <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required type="text"
                                                   value="{{$equipment_work_period->work_name->name}}"
                                                   list="work_names_list" class="form-control form-control-sm"
                                                   id="basic-url" aria-describedby="basic-addon3"
                                                   placeholder="Начните писать ..." name="work_name">
                                            <datalist id="work_names_list">
                                                @forelse($work_names as $work_name)
                                                    <option value="{{$work_name->name}}">
                                                @empty
                                                    <option value="нет данных ...">
                                                @endforelse
                                            </datalist>
                                            @error('work_name')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="last_was_at" class="form-label form-label-sm">Последняя дата выполнения
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input required class="form-control form-control-sm"
                                                   value="{{$equipment_work_period->last_was_at}}"
                                            type="date" name="last_was_at">
                                            @error('last_was_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="last_avr" class="form-label form-label-sm">Последний акт с этой работой
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input readonly disabled class="form-control form-control-sm"
                                                   value="{{$last_avr->last_avr_date}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="value" class="form-label form-label-sm">Величина
                                                <span class="text-danger"><b>*</b></span>
                                            </label>
                                            <input required name="value" type="text" class="form-control form-control-sm"
                                                   value="{{old('value', $equipment_work_period->repeat_days)}}"
                                                   placeholder="Количество дней">
                                            @error('value')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label for="comment" class="form-label form-label-sm">Комментарий</label>
                                            <input name="comment" type="text" class="form-control form-control-sm"
                                                   value="{{old('comment', $equipment_work_period->comment)}}"
                                                   placeholder="Комментарий">
                                            @error('comment')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="javascript:history.back();"
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
@endsection
