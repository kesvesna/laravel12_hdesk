@extends('layouts.backend.main')

@section('title', 'Главная | АВР создание по тех. акту')

@section('content')
    @csrf
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">АВР создание по техническому акту</h4>
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
                            <form action="{{route('avrs.store_from_tech_act', $tech_act)}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col col-md-3 mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">Дата</label>
                                            <input autofocus required class="form-control form-control-sm" name="date"
                                                   type="date" value="{{date('Y-m-d')}}">
                                            @error('date')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-4">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input hidden required name="trk_id" id="trk_id" value="{{$tech_act->trk_id}}">
                                            <select disabled class="form-select form-select-sm">
                                                    <option value="{{$tech_act->trk_id}}">{{$tech_act->trk->name}}</option>
                                            </select>
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="building_id" class="form-label form-label-sm">Блок/Зона <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="building_id" class="form-select form-select-sm"
                                                    id="building_id">
                                                @forelse($buildings as $building)
                                                    <option value="{{$building->id}}" {{old('building_id') == $building->id ? 'selected' : null}}>{{$building->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('building_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="floor_id" class="form-label form-label-sm">Этаж <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="floor_id" class="form-select form-select-sm"
                                                    id="floor_id">
                                                @forelse($floors as $floor)
                                                    <option value="{{$floor->id}}" {{old('floor_id') == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('floor_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-4">
                                            <label for="room_id" class="form-label form-label-sm">Помещение <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="room_id" class="form-select form-select-sm"
                                                    id="room_id">
                                                @forelse($rooms as $room)
                                                    <option value="{{$room->id}}">{{$room->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('room_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                        <div class="col col-md-3 mb-3">
                                            <label for="system_id" class="form-label form-label-sm">Система <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="system_id" class="form-select form-select-sm">
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
                                    <div class="equipments-add-parent-div mb-3">
                                        <div class="equipment-add-div p-2 my-3 rounded"
                                             style="background-color: rgba(145, 135, 255, 0.2)">
                                            <div class="row row-cols-1">
                                                <div class="col-12 col-md-4">
                                                    <label for="basic-url" class="form-label form-label-sm">Оборудование
                                                        <span class="text-danger"><b>*</b></span></label>
                                                    <div class="input-group input-group-sm mb-3">
                                                        <span class="input-group-text equipment-add-button"><img
                                                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                alt="add" title="Добавить" height="20"></span>
                                                        <span class="input-group-text equipment-delete-button"><img
                                                                src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                alt="delete" title="Удалить" height="20"></span>
                                                        <input required type="text" list="equipment_names_list"
                                                               class="form-control form-control-sm equipment-name-input"
                                                               placeholder="Начните писать ..."
                                                               name="equipment[]" data-equipment-id="0">
                                                        <datalist id="equipment_names_list">
                                                            @forelse($equipment_names as $equipment)
                                                                <option data-equipment_key="{{$equipment->id}}"
                                                                        value="{{$equipment->name}}">
                                                            @empty
                                                                <option data-equipment_key="" value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </div>
                                                    @error('equipment_names.*')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="works-add-parent-div p-2 mt-3 mb-1 rounded"
                                                 style="background-color: rgba(218, 117, 255, 0.2)">
                                                <label for="basic-url" class="form-label form-label-sm">Тех. мероприятия
                                                    <span class="text-danger"><b>*</b></span></label>
                                                <div class="work-add-div mb-1 mb-md-0">
                                                    <div class="row row-cols-1">
                                                        <div class="col-12 col-md-5">
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-text work-add-button"><img
                                                                        src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                        alt="add" title="Добавить" height="20"></span>
                                                                <span class="input-group-text work-delete-button"><img
                                                                        src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                        alt="delete" title="Удалить" height="20"></span>
                                                                <input required type="text" list="works_list"
                                                                       class="form-control form-control-sm work-type-input"
                                                                       placeholder="Начните писать ..."
                                                                       name="equipment[0][works][0][name]"
                                                                       data-work-type-id="0">
                                                                <datalist id="works_list">
                                                                    @forelse($works as $work)
                                                                        <option data-work_key="{{$work->id}}"
                                                                                value="{{$work->name}}">
                                                                    @empty
                                                                        <option data-work_key="" value="нет данных ...">
                                                                    @endforelse
                                                                </datalist>
                                                            </div>
                                                            @error('works.*')
                                                            <div class="text-danger"
                                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-12 col-md-7 mb-1">
                                                            <textarea name="equipment[0][works][0][comment]"
                                                                      class="form-control form-control-sm mt-1 mt-md-0 work-comment-textarea"
                                                                      rows="1"
                                                                      placeholder="Комментарии ... (необязательно)"
                                                                      data-work-comment-id="0"></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="spare-parts-add-parent-div p-2 mt-3 mb-1 rounded"
                                                 style="background-color: rgba(255, 159, 117, 0.2)">
                                                <label for="basic-url" class="form-label form-label-sm">Запчасти</label>
                                                <div class="spare-part-add-div mb-1 mb-md-0">
                                                    <div class="row row-cols-1">
                                                        <div class="col-12 col-md-5">
                                                            <div class="input-group input-group-sm">
                                                                <span
                                                                    class="input-group-text spare-part-add-button"><img
                                                                        src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                        alt="add" title="Добавить" height="20"></span>
                                                                <span class="input-group-text spare-part-delete-button"><img
                                                                        src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                        alt="delete" title="Удалить" height="20"></span>
                                                                <input type="text" list="spare_parts_list"
                                                                       class="form-control form-control-sm spare-part-name-input"
                                                                       placeholder="Начните писать ..."
                                                                       name="equipment[0][spare_parts][0][name]"
                                                                       data-spare-part-name-id="0">
                                                                <datalist id="spare_parts_list">
                                                                    @forelse($spare_parts as $spare_part)
                                                                        <option
                                                                            data-spare_part_key="{{$spare_part->id}}"
                                                                            value="{{$spare_part->name}}">
                                                                    @empty
                                                                        <option data-spare_part_key=""
                                                                                value="нет данных ...">
                                                                    @endforelse
                                                                </datalist>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-md-7 mb-1">
                                                            <div class="input-group input-group-sm mt-1 mt-md-0">
                                                                <input type="text"
                                                                       class="form-control form-control-sm spare-part-model-input"
                                                                       placeholder="Модель"
                                                                       name="equipment[0][spare_parts][0][model]"
                                                                       data-spare-part-model-id="0">
                                                                <input type="number" step="0.1"
                                                                       class="form-control form-control-sm spare-part-value-input"
                                                                       placeholder="Количество"
                                                                       name="equipment[0][spare_parts][0][value]"
                                                                       data-spare-part-value-id="0">
                                                            </div>
                                                        </div>
                                                        @error('spare_parts.*')
                                                        <div class="text-danger"
                                                             style="margin-top: -1rem !important;">{{$message}}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="executors-add-parent-div">
                                        <label class="form-label form-label-sm">Исполнители <span
                                                class="text-danger"><b>*</b></span></label>
                                        <div class="executor-add-div">
                                            <div class="row row-cols-1">
                                                <div class="col-12 col-md-4">
                                                    <div class="input-group input-group-sm mb-1">
                                                        <span class="input-group-text executor-add-button"><img
                                                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                alt="add" title="Добавить" height="20"></span>
                                                        <span class="input-group-text executor-delete-button"><img
                                                                src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                alt="delete" title="Удалить" height="20"></span>
                                                        <input value="{{Auth::user()->name}}" required type="text"
                                                               list="executors_list"
                                                               class="form-control form-control-sm"
                                                               placeholder="Начните писать ..."
                                                               name="executors[]">
                                                        <datalist id="executors_list">
                                                            @forelse($executors as $executor)
                                                                <option data-equipment_key="{{$executor->id}}"
                                                                        value="{{$executor->name}}">
                                                            @empty
                                                                <option data-equipment_key="" value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </div>
                                                    @error('executors.*')
                                                    <div class="text-danger"
                                                         style="margin-top: -1rem !important;">{{$message}}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row row-cols-1 row-cols-md-3">
                                        <div class="col mt-4">
                                            <div class="input-group input-group-sm">
                                                <a href="{{route('avrs.index')}}"
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
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
        <script src="{{asset('assets/js/avrs/add_equipment.js')}}"></script>
        <script src="{{asset('assets/js/avrs/delete_equipment.js')}}"></script>
        <script src="{{asset('assets/js/avrs/delete_work.js')}}"></script>
        <script src="{{asset('assets/js/avrs/delete_spare_part.js')}}"></script>
        <script src="{{asset('assets/js/avrs/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/avrs/delete_executor.js')}}"></script>
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
