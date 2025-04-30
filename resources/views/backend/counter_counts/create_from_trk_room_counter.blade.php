@extends('layouts.backend.main')

@section('title', 'Главная | Заполнение показаний')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Заполнение показаний</h4>
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
                    <div class="col">
                        @include('components.backend.message')
                    </div>
                    <div class="row">
                        <div class="col">
                            <form action="{{route('counter_counts.store_from_trk_room_counter', $trk_room_counter)}}" method="post">
                                @csrf
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="trk_id">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input class="form-control form-control-sm" disabled readonly value="{{$trk_room_counter->trk->name}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="floor_name">Этаж <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input class="form-control form-control-sm" disabled readonly value="{{$trk_room_counter->floor->name}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="brand_name">Бренд <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input class="form-control form-control-sm" disabled readonly
                                                   value="{{$trk_room_counter->brand->name ?? 'отсутствует'}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="organization_name">Организация <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input class="form-control form-control-sm" disabled readonly
                                                   value="{{$trk_room_counter->organization->name ?? 'отсутствует'}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="number">Номер счетчика <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input class="form-control form-control-sm" disabled readonly value="{{$trk_room_counter->number}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="counter_type_id">Тип счетчика
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input class="form-control form-control-sm" disabled readonly value="{{$trk_room_counter->counter_type->name}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="tariff">Тариф
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input class="form-control form-control-sm" disabled readonly value="{{$trk_room_counter->counter_tariff->name}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="last_counts">Предыдущие показания (день)
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input class="form-control form-control-sm" disabled readonly value="{{$trk_room_counter->day_counts->last()->count}}">
                                        </div>
                                    </div>
                                    @if($trk_room_counter->counter_tariff->value == 0)
                                        <div class="row">
                                            <div class="col mb-3">
                                                <label class="form-label form-label-sm" for="last_counts">Предыдущие показания (ночь)
                                                    <span class="text-danger"><b>*</b></span></label>
                                                <input class="form-control form-control-sm" disabled readonly value="{{$trk_room_counter->night_counts->last()->count}}">
                                            </div>
                                        </div>
                                    @endif
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="current_count_day">Текущие показания <span
                                                    class="text-danger"><b>*</b></span> <span
                                                    class="text-muted">(день)</span></label>
                                            <input autofocus required step="1" class="form-control form-control-sm" name="current_count_day"
                                                   type="number">
                                        </div>
                                    </div>
                                    @if($trk_room_counter->counter_tariff->value == 0)
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="current_count_night">Текущие показания <span
                                                    class="text-danger"><b>*</b></span> <span
                                                    class="text-muted">(ночь)</span></label>
                                            <input required class="form-control form-control-sm" name="current_count_night"
                                                   type="number" step="1">
                                        </div>
                                    </div>
                                    @endif
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="comment">Комментарий</label>
                                            <input class="form-control form-control-sm" name="comment"
                                                   type="text">
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <button onclick="history.back()" type="button" class="btn btn-sm btn-outline-success col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                alt="back" title="Назад"></button>
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
