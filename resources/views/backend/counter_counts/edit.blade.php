@extends('layouts.backend.main')

@section('title', 'Главная | Показания редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Показания редактирование</h4>
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
                            <form action="{{route('counter_counts.update', $counter_count)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="trk_id">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select disabled class="form-control form-control-sm" name="trk_id"
                                                    id="trk_id" required>
                                                @forelse($trks as $trk)
                                                    <option
                                                        value="{{$trk->id}}" {{$counter_count->trk_id == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
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
                                            <label class="form-label form-label-sm" for="brand_name">Бренд <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required readonly class="form-control form-control-sm"
                                                   list="brand_data_list" type="search" id="brand_name"
                                                   placeholder="Начните писать ..." name="brand_name"
                                                   value="{{old('brand_name', $counter_count->trk_room_counter->brand->name)}}">
                                            <datalist id="brand_data_list">
                                                @forelse($brands as $brand)
                                                    <option value="{{$brand->name}}">
                                                @empty
                                                    <option value="нет данных ...">
                                                @endforelse
                                            </datalist>
                                            @error('brand_name')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="organization_name">Организация <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required readonly class="form-control form-control-sm"
                                                   list="organization_data_list" type="search" id="organization_name"
                                                   placeholder="Начните писать ..." name="organization_name"
                                                   value="{{old('organization_name', $counter_count->trk_room_counter->organization->name)}}">
                                            <datalist id="brand_data_list">
                                                @forelse($organizations as $organization)
                                                    <option value="{{$organization->name}}">
                                                @empty
                                                    <option value="нет данных ...">
                                                @endforelse
                                            </datalist>
                                            @error('organization_name')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="number">Номер счетчика <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required readonly class="form-control form-control-sm"
                                                   list="number_list" type="search" id="number"
                                                   placeholder="Начните писать ..." name="number"
                                                   value="{{old('number', $counter_count->trk_room_counter->number)}}">
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
                                            <label class="form-label form-label-sm" for="tariff">Тариф</label>
                                            <input readonly class="form-control form-control-sm" name="tariff"
                                                   type="text" value="{{$counter_count->tariff ? 'день' : 'ночь'}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="date">Дата</label>
                                            <input required class="form-control form-control-sm" name="date"
                                                   type="date"
                                                   value="{{old('date', $counter_count->date)}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm"
                                                   for="count">Показания</label>
                                            <input autofocus required class="form-control form-control-sm"
                                                   name="count" type="number"
                                                   value="{{old('current_count', $counter_count->count)}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="comment">Комментарий</label>
                                            <input class="form-control form-control-sm" name="comment" type="text"
                                                   value="{{old('comment', $counter_count->comment)}}">
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 input-group-sm">
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
