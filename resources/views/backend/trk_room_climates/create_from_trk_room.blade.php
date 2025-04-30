@extends('layouts.backend.main')

@section('title', 'Главная | Климат создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Климат создание</h4>
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
                            <form action="{{route('trk_room_climates.store_from_trk_room')}}" method="post">
                                @csrf
                                <div class="card shadow p-3">
                                    <div class="row row-cols-1 row-cols-md-4">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="trk_room_id">ТРК</label>
                                            <input hidden required readonly value="{{$trk_room->id}}"
                                                   name="trk_room_id">
                                            <input readonly disabled class="form-control form-control-sm"
                                                   value="{{$trk_room->trk->name}}">
                                            @error('trk_room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm">Блок/Зона</label>
                                            <input readonly disabled class="form-control form-control-sm"
                                                   value="{{$trk_room->building->name}}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm">Этаж/Отметка</label>
                                            <input readonly disabled class="form-control form-control-sm"
                                                   value="{{$trk_room->floor->name}}">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm">Помещение</label>
                                            <input readonly disabled class="form-control form-control-sm"
                                                   value="{{$trk_room->room->name}}">
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-4">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_inside">Т в помещении <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required class="form-control form-control-sm" type="number"
                                                   step="0.1" name="t_inside" placeholder="21.5">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_outside">Т на улице</label>
                                            <input class="form-control form-control-sm" type="number" step="0.1"
                                                   name="t_outside" placeholder="-5.5">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_supply_air">Т притока</label>
                                            <input class="form-control form-control-sm" type="number" step="0.1"
                                                   name="t_supply_air" placeholder="22.3">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="t_extract_air">Т
                                                вытяжки</label>
                                            <input class="form-control form-control-sm" type="number" step="0.1"
                                                   name="t_extract_air" placeholder="25.4">
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-4">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="h_inside">Влажность в
                                                помещении</label>
                                            <input class="form-control form-control-sm" type="number" step="1"
                                                   name="h_inside" placeholder="55">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="q_supply_air_total">Расход
                                                притока общий</label>
                                            <input class="form-control form-control-sm" type="number" step="1"
                                                   name="q_supply_air_total" placeholder="120">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="q_extract_air_total">Расход
                                                вытяжки общий</label>
                                            <input class="form-control form-control-sm" type="number" step="1"
                                                   name="q_extract_air_total" placeholder="100">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="comment">Комментарий (255 символов)</label>
                                            <input class="form-control form-control-sm" type="text" name="comment">
                                            @error('comment')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="javascript:history.back()"
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
