@extends('layouts.backend.main')

@section('title', 'Главная | Счетчик редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Счетчик редактирование</h4>
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
                            <form action="{{route('counters.update', $counter)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select name="trk_id" id="trk_id" class="form-select form-select-sm">
                                                @forelse($trks as $trk)
                                                    <option
                                                        value="{{$trk->id}}" {{isset($counter->trk->id) && $counter->trk->id == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
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
                                            <label for="number" class="form-label form-label-sm">№ счетчика <span
                                                    class="text-danger"><b> *</b></span></label>
                                            <input value="{{old('number', $counter->number)}}" type="text"
                                                   class="form-control form-control-sm" id="number" name="number">
                                            @error('number')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label for="counter_type_id" class="form-label form-label-sm">Тип<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select name="counter_type_id" id="counter_type_id"
                                                    class="form-select form-select-sm">
                                                @forelse($counter_types as $counter_type)
                                                    <option
                                                        value="{{$counter_type->id}}" {{isset($counter->type->id) && $counter->type->id === $counter_type->id ? 'selected' : null}}>{{$counter_type->name}}</option>
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
                                        <div class="col mb-4">
                                            <label for="tariff_name_id" class="form-label form-label-sm">Тариф<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select name="tariff_name_id" id="tariff_name_id"
                                                    class="form-select form-select-sm">
                                                @forelse($counter_tariffs as $counter_tariff)
                                                    <option
                                                        value="{{$counter_tariff->id}}" {{isset($counter->type->id) && $counter->type->id === $counter_tariff->id ? 'selected' : null}}>{{$counter_tariff->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('tariff_name_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 input-group-sm">
                                        <a href="{{route('counters.show', $counter)}}"
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
