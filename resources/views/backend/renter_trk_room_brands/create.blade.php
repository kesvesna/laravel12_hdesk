@extends('layouts.backend.main')

@section('title', 'Главная | Арендатор - помещение создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Арендатор - помещение создание</h4>
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
                            <form action="{{route('renter_trk_room_brands.store')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="col mb-3">
                                        <!-- Button trigger modal responsibility -->
                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal2">Как создать?
                                        </button>
                                    </div>
                                    @include('components.backend.selects.create.trk_id')
                                    <div class="col mb-3">
                                        <label class="form-label form-label-sm" for="building_id">Блок/Здание <span
                                                class="text-danger"><b>*</b></span></label>
                                        <select required name="building_id" id="building_id"
                                                class="form-select form-select-sm">
                                            @forelse($all_buildings as $building)
                                                <option
                                                    value="{{$building->id}}" {{old('building_id') == $building->id ? 'selected' : null}}>{{$building->name}}</option>
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
                                                    value="{{$floor->id}}" {{old('floor_id') == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                            @empty
                                                <option value="">нет данных ...</option>
                                            @endforelse
                                        </select>
                                        @error('floor_id')
                                        <div class="text-danger">{{$message}}</div>
                                        @enderror
                                    </div>
                                    @include('components.backend.selects.create.room_id')
                                    @include('components.backend.data-lists.create.organization_name')
                                    @include('components.backend.data-lists.create.brand_name')
                                    <div class="input-group input-group-sm mt-2">
                                        <a href="{{route('renter_trk_room_brands.index')}}"
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
        <!-- Modal alert -->
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel2">Создание Арендатор/Помещение</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Если в списках чего-то нет, попросите админа добавить</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
