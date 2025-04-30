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
                            <form action="{{route('counter_counts.store')}}" method="post">
                                @csrf
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="trk_id">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select class="form-control form-control-sm" name="trk_id" id="trk_id"
                                                    required>
                                                @forelse($trks as $trk)
                                                    <option value="{{$trk->id}}" {{$trk->id == old('trk_id') ? 'selected' : null}}>{{$trk->name}}</option>
                                                @empty
                                                    <option value="нет данных ..."></option>
                                                @endforelse
                                            </select>
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="floor_id">Этаж <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select class="form-control form-control-sm" name="floor_id" id="floor_id"
                                                    required>
                                                @forelse($floors as $floor)
                                                    <option value="{{$floor->id}}" {{$floor->id == old('floor_id') ? 'selected' : null}}>{{$floor->name}}</option>
                                                @empty
                                                    <option value="нет данных ..."></option>
                                                @endforelse
                                            </select>
                                            @error('floor_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
{{--                                    <div class="row">--}}
{{--                                        <div class="col mb-3">--}}
{{--                                            <label class="form-label form-label-sm" for="brand_id">Бренд</label>--}}
{{--                                            <select class="form-control form-control-sm" name="brand_id" id="brand_id">--}}
{{--                                                <option value="">Нет бренда</option>--}}
{{--                                            @forelse($brands as $brand)--}}
{{--                                                    <option value="{{$brand->id}}" {{$brand->id == old('brand_id') ? 'selected' : null}}>{{$brand->name}}</option>--}}
{{--                                                @empty--}}
{{--                                                    <option value="нет данных ..."></option>--}}
{{--                                                @endforelse--}}
{{--                                            </select>--}}
{{--                                            @error('brand_id')--}}
{{--                                            <div class="text-danger">{{$message}}</div>--}}
{{--                                            @enderror--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                    <div class="row">--}}
{{--                                        <div class="col mb-3">--}}
{{--                                            <label class="form-label form-label-sm" for="organization_id">Организация <span--}}
{{--                                                    class="text-danger"><b>*</b></span></label>--}}
{{--                                            <select class="form-control form-control-sm" name="organization_id" id="organization_id"--}}
{{--                                                    required>--}}
{{--                                                @forelse($organizations as $organization)--}}
{{--                                                    <option value="{{$organization->id}}" {{$organization->id == old('organization_id') ? 'selected' : null}}>{{$organization->name}}</option>--}}
{{--                                                @empty--}}
{{--                                                    <option value="нет данных ..."></option>--}}
{{--                                                @endforelse--}}
{{--                                            </select>--}}
{{--                                            @error('organization_id')--}}
{{--                                            <div class="text-danger">{{$message}}</div>--}}
{{--                                            @enderror--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="number">Номер счетчика <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required class="form-control form-control-sm" list="number_list"
                                                   type="search" id="number" placeholder="Начните писать ..."
                                                   name="number" value="{{old('number')}}">
                                            <datalist id="number_list">
                                                @forelse($counters as $counter)
                                                    <option value="{{$counter->number}}">
                                                @empty
                                                    <option value="нет данных ...">
                                                @endforelse
                                            </datalist>
                                            @error('number')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="current_count_day">Текущие
                                                показания день <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input autofocus required class="form-control form-control-sm" name="current_count_day"
                                                   type="number" value="{{old('current_count_day')}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="current_count_night">Ночь <span
                                                    class="text-muted">(если тариф день/ночь)</span></label>
                                            <input class="form-control form-control-sm" name="current_count_night"
                                                   type="number" value="{{old('current_count_night')}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="comment">Комментарий</label>
                                            <input class="form-control form-control-sm" name="comment"
                                                   type="text" value="{{old('comment')}}">
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('counter_counts.index')}}"
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
