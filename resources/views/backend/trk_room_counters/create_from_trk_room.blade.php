@extends('layouts.backend.main')

@section('title', 'Главная | Счетчик создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Счетчик создание</h4>
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
                            <form action="{{route('trk_room_counters.store_from_trk_room')}}" method="post">
                                @csrf
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="trk_room_id">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input hidden required readonly value="{{$trk_room->id}}"
                                                   name="trk_room_id">
                                            <input readonly disabled class="form-control form-control-sm"
                                                   value="{{$trk_room->trk->name}}">
                                            @error('trk_room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="brand_id">Бренд <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input readonly disabled class="form-control form-control-sm"
                                                   value="{{$trk_room->renter->brand->name}}">
                                            @error('brand_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="organization_id">Организация <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input readonly disabled class="form-control form-control-sm"
                                                   value="{{$trk_room->renter->organization->name}}">
                                            @error('organization_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="floor_id">Этаж <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="floor_id" id="floor_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_floors as $floor)
                                                    <option
                                                        value="{{$floor->id}}" {{$trk_room->floor->id == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('floor_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                                <div class="row">
                                                    <div class="col mb-3">
                                                        <label class="form-label form-label-sm" for="number">№ счетчика
                                                            <span class="text-danger"><b>*</b></span></label>
                                                        <input autofocus class="form-control form-control-sm"
                                                               name="number" id="number" placeholder="Номер счетчика"
                                                               required type="text">
                                                        @error('number')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="last_count_day">Последние показания (день) <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required class="form-control form-control-sm"
                                                   type="number" id="last_count_day" name="last_count_day" step="1">
                                            @error('last_count_day')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="last_count_night">Последние показания (ночь)</label>
                                            <input class="form-control form-control-sm"
                                                   type="number" id="last_count_night" name="last_count_night" step="1">
                                            @error('last_count_night')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                                <div class="row">
                                                    <div class="col mb-3">
                                                        <label class="form-label form-label-sm" for="counter_type_id">Тип
                                                            <span class="text-danger"><b>*</b></span></label>
                                                        <select name="counter_type_id" id="counter_type_id"
                                                                class="form-select form-select-sm">
                                                            @forelse($all_counter_types as $counter_type)
                                                                <option
                                                                    value="{{$counter_type->id}}">{{$counter_type->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                        @error('counter_type_id')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="coefficient">Коэффициент
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input autofocus class="form-control form-control-sm"
                                                   name="coefficient" id="coefficient" value="1"
                                                   required  type="number" step="1">
                                            @error('coefficient')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                                <div class="row">
                                                    <div class="col mb-4">
                                                        <label class="form-label form-label-sm" for="tariff_name_id">Тариф
                                                            <span class="text-danger"><b>*</b></span></label>
                                                        <select name="tariff_name_id" id="tariff_name_id"
                                                                class="form-select form-select-sm">
                                                            @forelse($all_counter_tariffs as $counter_tariff)
                                                                <option
                                                                    value="{{$counter_tariff->id}}">{{$counter_tariff->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                        @error('tariff_name_id')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col mb-4">
                                                        <label class="form-label form-label-sm" for="mounted_at">Дата
                                                            установки</label>
                                                        <input class="form-control form-control-sm" type="date"
                                                               id="mounted_at" placeholder="Поиск ..." name="mounted_at"
                                                               value="{{old('mounted_at') ?? null}}">
                                                        @error('mounted_at')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col mb-4">
                                                        <label class="form-label form-label-sm" for="using_purpose">Где
                                                            используется (наружная реклама, и т.д.)</label>
                                                        <input class="form-control form-control-sm" type="text"
                                                               id="using_purpose" placeholder="Где используется"
                                                               name="using_purpose"
                                                               value="{{old('using_purpose') ?? null}}">
                                                        @error('using_purpose')
                                                        <div class="text-danger">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="comment">Комментарий</label>
                                            <input class="form-control form-control-sm" type="text" id="comment"
                                                   placeholder="Любой комментарий" name="comment"
                                                   value="{{old('comment') ?? null}}">
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
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger col-6 col-md-2"><img
                                                            src="{{asset('assets/images/backend/svg/save.svg')}}"
                                                            alt="save" title="Сохранить"></button>
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
