@extends('layouts.backend.main')

@section('title', 'Главная | ТРК/Оборудование создание')

@section('content')
    @csrf
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">ТРК/Оборудование создание</h4>
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
                            <form action="{{route('trk_equipments.store_from_trk_room')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    {{--                                <div class="col mb-3">--}}
                                    {{--                                    <!-- Button trigger modal responsibility -->--}}
                                    {{--                                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#exampleModal2">Как создать?</button>--}}
                                    {{--                                </div>--}}
                                    <div class="row row-cols-1 row-cols-md-4">
                                        <div class="col mb-3">
                                            <label for="trk_room_id" class="form-label form-label-sm">ТРК <span class="text-danger"><b>*</b></span></label>
                                            <input hidden readonly name="trk_room_id" value="{{$trk_room->id}}">
                                            <input readonly disabled value="{{$trk_room->trk->name}}"
                                                   class="form-control form-control-sm">
                                            @error('trk_room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>

                                        <div class="col mb-3">
                                            <label for="building_id" class="form-label form-label-sm">Блок/Зона <span class="text-danger"><b>*</b></span></label>
                                            <input readonly disabled value="{{$trk_room->building->name}}"
                                                   class="form-control form-control-sm">
                                        </div>

                                        <div class="col mb-3">
                                            <label for="floor_id" class="form-label form-label-sm">Этаж/Отметка <span class="text-danger"><b>*</b></span></label>
                                            <input readonly disabled value="{{$trk_room->floor->name}}"
                                                   class="form-control form-control-sm">
                                        </div>

                                        <div class="col mb-3">
                                            <label for="room_id" class="form-label form-label-sm">Помещение <span class="text-danger"><b>*</b></span></label>
                                            <input readonly disabled value="{{$trk_room->room->name}}"
                                                   class="form-control form-control-sm">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="system_id" class="form-label form-label-sm">Система <span class="text-danger"><b>*</b></span></label>
                                            <select required name="system_id" class="form-select form-select-sm"
                                                    autofocus>
                                                @forelse($systems as $system)
                                                    <option value="{{$system->id}}">{{$system->name}}</option>
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
                                            <label for="equipment_status_id"
                                                   class="form-label form-label-sm">Статус <span class="text-danger"><b>*</b></span></label>
                                            <select required name="equipment_status_id"
                                                    class="form-select form-select-sm">
                                                @forelse($equipment_statuses as $equipment_status)
                                                    <option
                                                        value="{{$equipment_status->id}}">{{$equipment_status->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('equipment_status_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="responsible_division_id"
                                                   class="form-label form-label-sm">Кто обслуживает <span class="text-danger"><b>*</b></span></label>
                                            <select required name="responsible_division_id"
                                                    class="form-select form-select-sm">
                                                @forelse($divisions as $division)
                                                    <option
                                                        value="{{$division->id}}">{{$division->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('responsible_division_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="comment"
                                                   class="form-label form-label-sm">Комментарий</label>
                                            <input name="comment" class="form-control form-control-sm" placeholder="Комментарий" type="text">
                                            @error('comment')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1">
                                        <div class="col-12 col-md-4 equipments-add-parent-div mb-2">
                                            <label for="basic-url" class="form-label form-label-sm">Оборудование <span class="text-danger"><b>*</b></span></label>
                                            <div class="input-group mb-2 equipment-add-div">
                                                <input required type="text" list="1"
                                                       class="form-control form-control-sm" id="basic-url"
                                                       aria-describedby="basic-addon3" placeholder="Начните писать ..."
                                                       name="equipment_names[]">
                                                <datalist id="1">
                                                    @forelse($equipment_names as $equipment)
                                                        <option data-room_key="{{$equipment->id}}"
                                                                value="{{$equipment->name}}">
                                                    @empty
                                                        <option data-room_key="" value="нет данных ...">
                                                    @endforelse
                                                </datalist>
                                                <span class="input-group-text equipment-add-button"
                                                      id="basic-addon3"><img
                                                        src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                        alt="add" title="Добавить" height="20"></span>
                                                <span class="input-group-text equipment-delete-button"
                                                      id="basic-addon3"><img
                                                        src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                        alt="delete" title="Удалить" height="20"></span>
                                            </div>
                                            @error('equipment_names.*')
                                            <div class="text-danger"
                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-3 mt-2">
                                        <div class="col">
                                            <div class="input-group input-group-sm">
                                                <a href="javascript:history.back();"
                                                   class="btn btn-sm btn-outline-success col-6"><img
                                                        src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                        alt="back" title="Назад"></a>
                                                <button type="submit" class="btn btn-sm btn-outline-danger col-6"><img
                                                        src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                        title="Сохранить"></button>
                                            </div>
                                        </div>
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
                        <h1 class="modal-title fs-5" id="exampleModalLabel2">Создание ТРК/Оборудования</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Если в списках нет Вашего ТРК, помещения.</p>
                        <p>создайте их через раздел Архитектура - Трк/Помещения.</p>
                        <p>С названиями систем и оборудования тоже самое.</p>
                        <p>Оборудование заполняется по одному помещению.</p>
                        <p>Не надо проверять на совпадения названий, за Вас это сделает сервер.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
        <script src="{{asset('assets/js/trk_equipments/add_equipment.js')}}" defer></script>
        <script src="{{asset('assets/js/trk_equipments/delete_equipment.js')}}" defer></script>
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
