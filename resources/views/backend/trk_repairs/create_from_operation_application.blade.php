@extends('layouts.backend.main')

@section('title', 'Главная | Новый ремонт по заявке')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Новый ремонт по заявке</h4>
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
                            <form action="{{route('trk_repairs.store_from_operation_application', $operation_application)}}" method="post">
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
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="trk_id">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input hidden required name="trk_id"
                                                   value="{{$operation_application->trk->id}}">
                                            <input class="form-control form-control-sm" readonly disabled
                                                   value="{{$operation_application->trk->name}}">
                                            {{--                                        <select required name="trk_id" id="trk_id" class="form-select form-select-sm">--}}
                                            {{--                                            @forelse($all_trks as $trk)--}}
                                            {{--                                                <option value="{{$trk->id}}" {{old('trk_id') == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>--}}
                                            {{--                                            @empty--}}
                                            {{--                                                <option value="">нет данных ...</option>--}}
                                            {{--                                            @endforelse--}}
                                            {{--                                        </select>--}}
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
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
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="room_id">Помещение <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="room_id" id="room_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_rooms as $room)
                                                    <option
                                                        value="{{$room->id}}" {{old('room_id') == $room->id ? 'selected' : null}}>{{$room->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="system_id">Тип оборудования <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="system_id" id="system_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_systems as $system)
                                                    <option
                                                        value="{{$system->id}}" {{old('system_id') == $system->id ? 'selected' : null}}>{{$system->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('system_id')
                                                <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="equipment_id">Оборудование
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input required class="form-control form-control-sm"
                                                   list="equipment_name_data_list" type="search"
                                                   id="equipment_id" placeholder="Начните набирать ..."
                                                   name="equipment_id"
                                                   value="{{$old_filters['equipment_id'] ?? null}}">
                                            <datalist id="equipment_name_data_list">
                                                @forelse($all_equipments as $equipment)
                                                    <option value="{{$equipment->name}}">
                                                @empty
                                                    <option value="нет данных ...">
                                                @endforelse
                                            </datalist>
                                            @error('equipment_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="description"> Что нужно сделать
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input required class="form-control form-control-sm" type="text"
                                                   name="description" placeholder="Описание ремонта" value="{{old('description')}}">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="deadline_at">Выполнить до <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required type="datetime-local" name="deadline_at"
                                                   class="form-control form-control-sm" value="{{old('deadline_at')}}">
                                            @error('deadline_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-md-6 mb-4">
                                            <div class="btn-group-vertical btn-group-sm col-12" role="group" aria-label="Vertical radio toggle button group">
                                                <input type="radio" class="btn-check" name="operation_type" value="just_save" id="vbtn-radio1" autocomplete="off" checked>
                                                <label class="btn btn-outline-success" for="vbtn-radio1">Просто создать новый ремонт</label>
                                                <input type="radio" class="btn-check" name="operation_type" value="save_and_create_spare_part_order" id="vbtn-radio2" autocomplete="off">
                                                <label class="btn btn-outline-success" for="vbtn-radio2">Заказать запчасти после создания</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('trk_repairs.index')}}"
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
                        <h1 class="modal-title fs-5" id="exampleModalLabel2">Новый плановый ремонт</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Если в списках нет Вашего ТРК, помещения или оборудования.</p>
                        <p>Создайте их сначала.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
        <script>
            $(document).ready(function () {

                $('#trk_id').on('change', function () {
                    var idTrk = this.value;
                    $("#room_id").html('');
                    $.ajax({
                        url: "{{url('api/fetch-rooms')}}",
                        type: "POST",
                        data: {
                            trk_id: idTrk,
                            _token: '{{csrf_token()}}',
                        },
                        dataType: 'json',
                        success: function (result) {
                            $('#room_id').html('');
                            $.each(result.rooms, function (key, value) {
                                $("#room_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.rooms.length === 0) {
                                $("#room_id").append('<option value="">нет помещений ...</option>');
                            }
                        }
                    });
                });
            });
        </script>
@endsection
