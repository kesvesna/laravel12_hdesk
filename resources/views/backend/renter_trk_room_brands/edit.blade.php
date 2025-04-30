@extends('layouts.backend.main')

@section('title', 'Главная | Арендатор/Помещение редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Арендатор/Помещение редактирование</h4>
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
                            <form action="{{route('renter_trk_room_brands.update', $renter_trk_room_brand)}}"
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
{{--                                            <select name="trk_id" id="trk_id" class="form-select form-select-sm">--}}
{{--                                                @forelse($trks as $trk)--}}
{{--                                                    <option--}}
{{--                                                        value="{{$trk->id}}" {{old('trk_id', $renter_trk_room_brand->trk_room->trk->id) === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>--}}
{{--                                                @empty--}}
{{--                                                    <option value="">нет данных ...</option>--}}
{{--                                                @endforelse--}}
{{--                                            </select>--}}
                                            <input name="trk_id" hidden readonly required value="{{$renter_trk_room_brand->trk_room->trk->id}}">
                                            <br>
                                            <span>{{$renter_trk_room_brand->trk_room->trk->name}}</span>
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="room_id" class="form-label form-label-sm">Помещение<span
                                                    class="text-danger"><b> *</b></span></label>
{{--                                            <select name="room_id" id="room_id" class="form-select form-select-sm">--}}
{{--                                                @forelse($rooms as $room)--}}
{{--                                                    <option--}}
{{--                                                        value="{{$room->id}}" {{old('room_id', $renter_trk_room_brand->trk_room->room->id) === $room->id ? 'selected' : null}}>{{$room->name}}</option>--}}
{{--                                                @empty--}}
{{--                                                    <option value="">нет данных ...</option>--}}
{{--                                                @endforelse--}}
{{--                                            </select>--}}
                                            <input name="room_id" hidden readonly required value="{{$renter_trk_room_brand->trk_room->room->id}}">
                                            <br>
                                            <span>{{$renter_trk_room_brand->trk_room->room->name}}</span>
                                            @error('room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @role('sadmin')
                                    <div class="row row-cols-1 row-cols-md-4">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="trk_id">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="trk_id" id="trk_id"
                                                    class="form-select form-select-sm">
                                                @forelse($trks as $trk)
                                                    <option
                                                        value="{{$trk->id}}" {{old('trk_id', $renter_trk_room_brand->trk_room->trk->id) == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="building_id">Блок/Здание <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="building_id" id="building_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_buildings as $building)
                                                    <option
                                                        value="{{$building->id}}" {{old('building_id', $renter_trk_room_brand->trk_room->building->id) == $building->id ? 'selected' : null}}>{{$building->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('building_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="floor_id">Этаж <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="floor_id" id="floor_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_floors as $floor)
                                                    <option
                                                        value="{{$floor->id}}" {{old('floor_id', $renter_trk_room_brand->trk_room->floor->id) == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('floor_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="room_id">Помещение <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="room_id" id="room_id"
                                                    class="form-select form-select-sm">
                                                @forelse($rooms as $room)
                                                    <option
                                                        value="{{$room->id}}" {{old('room_id', $renter_trk_room_brand->trk_room->room->id) == $room->id ? 'selected' : null}}>{{$room->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @endrole
                                    @include('components.backend.data-lists.edit.organization_name')
                                    @include('components.backend.data-lists.edit.brand_name')
                                    <div class="input-group mb-3 input-group-sm">
                                        <a href="{{route('renter_trk_room_brands.show', $renter_trk_room_brand)}}"
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
