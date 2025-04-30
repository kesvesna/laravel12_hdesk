@extends('layouts.backend.main')

@section('title', 'Главная | Арендаторы/Помещения')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Арендаторы/Помещения</h4>
                        @if(auth()->user()->can('renter create'))
                            <a href="{{route('renter_trk_room_brands.create')}}"><img
                                    src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                    title="Добавить" height="30"></a>
                        @endif
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
                            <div class="card shadow">
                                <div class="card-title ps-3 pt-3">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#renters">Выгрузка арендаторов
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-hover shadow">
                                        <thead>
                                        <tr>
                                            <th>ТРК</th>
                                            <th>Этаж</th>
                                            <th>Помещение</th>
                                            <th>Организация</th>
                                            <th>Бренд</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('renter_trk_room_brands.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td>
                                                    <select name="trk_id" class="form-select form-select-sm"
                                                            id="trk_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_trks as $trk)
                                                            <option
                                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="floor_id" class="form-select form-select-sm"
                                                            id="floor_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_floors as $floor)
                                                            <option
                                                                value="{{$floor->id}}" {{isset($old_filters['floor_id']) && $old_filters['floor_id'] === $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm"
                                                           list="building_data_list" type="search" id="building_id"
                                                           placeholder="Поиск ..." onchange="this.form.submit();"
                                                           name="room_id" value="{{$old_filters['room_id'] ?? null}}">
                                                    <datalist id="building_data_list">
                                                        @forelse($all_rooms as $room)
                                                            <option value="{{$room->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" list="floor_data_list"
                                                           type="search" id="floor_id" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="organization_id"
                                                           value="{{$old_filters['organization_id'] ?? null}}">
                                                    <datalist id="floor_data_list">
                                                        @forelse($all_organizations as $organization)
                                                            <option value="{{$organization->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" list="room_data_list"
                                                           type="search" id="room_id" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="brand_id"
                                                           value="{{$old_filters['brand_id'] ?? null}}">
                                                    <datalist id="room_data_list">
                                                        @forelse($all_brands as $brand)
                                                            <option value="{{$brand->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                            </tr>
                                        </form>
                                        @forelse($renter_trk_room_brands as $renter_trk_room_brand)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('renter_trk_room_brands.show', $renter_trk_room_brand->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="text-nowrap">{{$renter_trk_room_brand->trk_room->trk->name}}</td>
                                                    <td class="text-nowrap">{{$renter_trk_room_brand->trk_room->floor->name}}</td>
                                                    <td class="text-nowrap">{{$renter_trk_room_brand->trk_room->room->name}}</td>
                                                    <td class="text-nowrap">{{$renter_trk_room_brand->organization->name ?? 'отсутствует'}}</td>
                                                    <td class="text-nowrap">{{$renter_trk_room_brand->brand->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    </div>
                                    {{$renter_trk_room_brands->withQueryString()->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal for renters -->
        <div class="modal fade" id="renters" tabindex="-1"
             aria-labelledby="renters" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel5">Выгрузка арендаторов</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('renter_trk_room_brands.export')}}" method="post">
                            @csrf
                            @method('post')
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="trk_id_2">Трк
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <select required name="trk_id"
                                            class="form-select form-select-sm" id="trk_id_2">
                                        @forelse($all_trks as $trk)
                                            <option
                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="floor_id_2">Этаж</label>
                                    <select name="floor_id_2" class="form-select form-select-sm" id="floor_id_2">
                                        <option value="">Все</option>
                                        @forelse($all_floors as $floor)
                                            <option
                                                value="{{$floor->id}}" {{isset($old_filters['floor_id']) && $old_filters['floor_id'] == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="file_type">Тип файла
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <select required name="file_type"
                                            class="form-select form-select-sm" id="file_type">
                                        <option value=".pdf">PDF</option>
                                        <option value=".xslx">EXCEL XSLX</option>
                                        <option value=".html">HTML</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Выгрузить
                            </button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
