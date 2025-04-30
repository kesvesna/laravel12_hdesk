@extends('layouts.backend.main')

@section('title', 'Потребители | Создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Потребители</h4>
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
                            <form action="{{route('equipment_users.store_from_equipment')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="equipment_id" class="form-label form-label-sm">Оборудование источник
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input required readonly hidden name="equipment_id"
                                                   value="{{$trk_equipment->id}}">
                                            <input readonly class="form-control form-control-sm"
                                                   value="{{$trk_equipment->equipment_name->name}}">
                                            @error('equipment_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!-- todo возможность добавлять много потребителей с выбором их типа (помещение, оборудование) -->
                                    <!-- два типа потребителей: Помещение и Оборудование (для электрощита например другое оборудование является потребителем) -->
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="trk_room_id" class="form-label form-label-sm">Потребитель <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select name="trk_room_id" class="form-select form-select-sm">
                                                @forelse($trk_rooms as $trk_room)
                                                    <option value="{{$trk_room->id}}">{{$trk_room->room->name . ' (' . $trk_room->floor->name . ')'}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('trk_room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label for="comment" class="form-label form-label-sm">Комментарий</label>
                                            <input name="comment" type="text" class="form-control form-control-sm"
                                                   value="{{old('comment')}}" placeholder="Комментарий">
                                            @error('comment')
                                            <div class="text-danger px-3 pb-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('trk_equipments.show', $trk_equipment)}}"
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
    </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
    {{--        <script src="{{asset('assets/js/equipment_users/add_user.js')}}" defer></script>--}}
    {{--        <script src="{{asset('assets/js/equipment_users/delete_user.js')}}" defer></script>--}}
@endsection
