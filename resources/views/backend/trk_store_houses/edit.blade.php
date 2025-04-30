@extends('layouts.backend.main')

@section('title', 'ТРК/Склад | Редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Редактирование ТРК/Склад</h4>
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
                            <form action="{{route('trk_store_houses.update', $trk_store_house)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select name="trk_id" class="form-select form-select-sm" autofocus>
                                                @forelse($trks as $trk)
                                                    <option
                                                        value="{{$trk->id}}" {{$trk_store_house->trk->id === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
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
                                            <label for="store_house_name_id" class="form-label form-label-sm">Склад
                                                <span class="text-danger"><b>*</b></span></label>
                                            <select name="store_house_name_id" class="form-select form-select-sm">
                                                @forelse($store_houses as $store_house)
                                                    <option
                                                        value="{{$store_house->id}}" {{$trk_store_house->store_house_name->id === $store_house->id ? 'selected' : null}}>{{$store_house->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('store_house_name_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="spare_part_name_id" class="form-label form-label-sm">Запчасть
                                                <span class="text-danger"><b>*</b></span></label>
                                            <select name="spare_part_name_id" class="form-select form-select-sm">
                                                @forelse($spare_parts as $spare_part)
                                                    <option
                                                        value="{{$spare_part->id}}" {{$trk_store_house->spare_part_name->id === $spare_part->id ? 'selected' : null}}>{{$spare_part->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('spare_part_name_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="spare_part_model"
                                                   class="form-label form-label-sm">Модель/Тип</label>
                                            <input type="text" name="spare_part_model"
                                                   class="form-control form-control-sm"
                                                   value="{{$trk_store_house->spare_part_model}}" placeholder="Модель">
                                            @error('spare_part_model')
                                            <div class="text-danger px-3 pb-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="value" class="form-label form-label-sm">Количество</label>
                                            <input name="value" type="number" step=".01"
                                                   class="form-control form-control-sm"
                                                   value="{{$trk_store_house->value}}" placeholder="Количество">
                                            @error('value')
                                            <div class="text-danger px-3 pb-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="min_required_value" class="form-label form-label-sm">Необходимый
                                                минимум</label>
                                            <input name="min_required_value" type="number" step=".01"
                                                   class="form-control form-control-sm"
                                                   value="{{$trk_store_house->min_required_value}}"
                                                   placeholder="Необходимый минимум">
                                            @error('min_required_value')
                                            <div class="text-danger px-3 pb-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="comment" class="form-label form-label-sm">Комментарий</label>
                                            <input name="comment" type="text" class="form-control form-control-sm"
                                                   value="{{$trk_store_house->comment}}" placeholder="Комментарий">
                                            @error('comment')
                                            <div class="text-danger px-3 pb-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 input-group-sm">
                                        <a href="{{route('trk_store_houses.index')}}"
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
