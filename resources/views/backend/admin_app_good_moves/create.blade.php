@extends('layouts.backend.main')

@section('title', 'Главная | Заявка на ввоз/вывоз создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Заявка на ввоз/вывоз создание</h4>
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
                            <form action="{{route('admin_app_good_moves.store')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="mb-3 col">
                                            <span>{{Auth::user()->organization->name}}</span>
                                            @error('organization_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3 mt-1">
                                            <label class="form-label form-label-sm" for="operation_type">Что
                                                планируете?</label>
                                            <br>
                                            <div class="btn-group-vertical btn-group-sm col-12 col-md-4" role="group"
                                                 aria-label="Vertical radio toggle button group">
                                                <input type="radio" class="btn-check" name="operation_type"
                                                       value="import" id="vbtn-radio1" autocomplete="off" checked>
                                                <label class="btn btn-outline-success"
                                                       for="vbtn-radio1"><b>Ввоз</b></label>
                                                <input type="radio" class="btn-check" name="operation_type"
                                                       value="export" id="vbtn-radio5" autocomplete="off">
                                                <label class="btn btn-outline-success"
                                                       for="vbtn-radio5"><b>Вывоз</b></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col">
                                            <label class="form-label form-label-sm" for="start_at">Начало<b><span
                                                        class="text-danger"> *</span></b></label>
                                            <input required name="start_at" type="datetime-local"
                                                   class="form-control form-control-sm" autofocus
                                                   value="{{old('start_at')}}">
                                            @error('start_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-4 col">
                                            <label class="form-label form-label-sm" for="finish_at">Конец<b><span
                                                        class="text-danger"> *</span></b></label>
                                            <input required name="finish_at" type="datetime-local"
                                                   class="form-control form-control-sm" value="{{old('finish_at')}}">
                                            @error('finish_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col">
                                            <label for="brand_name" class="form-label form-label-sm">Торговая марка
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input required value="{{old('brand_name')}}"
                                                   class="form-control form-control-sm" list="brands_list"
                                                   id="brand_name" name="brand_name" placeholder="Начните писать ...">
                                            <datalist id="brands_list">
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
                                            <label for="trk_id" class="form-label form-label-sm">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="trk_id" class="form-select form-select-sm"
                                                    id="trk_id">
                                                @forelse($trks as $trk)
                                                    <option value="{{$trk->id}}">{{$trk->name}}</option>
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
                                        <div class="col mb-4">
                                            <label for="room_id" class="form-label form-label-sm">Помещение <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="room_id" class="form-select form-select-sm"
                                                    id="room_id">
                                                @forelse($rooms as $room)
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
                                        <div class="mb-3 col">
                                            <label class="form-label form-label-sm" for="gate_number">Загрузочная зона
                                                №: <b><span
                                                        class="text-danger"> *</span></b></label>
                                            <input placeholder="Номер" required name="gate_number" step="1"
                                                   type="number" class="form-control form-control-sm"
                                                   value="{{old('gate_number')}}">
                                            @error('gate_number')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="mb-3 col">
                                            <label class="form-label form-label-sm" for="responsible_user">Материально
                                                ответственное лицо за прием/отгрузку ТМЦ (Должность, Ф.И.О., телефон):
                                                <b><span
                                                        class="text-danger"> *</span></b></label>
                                            <input placeholder="Страшный администратор Иванов И.И. +7 222 333 44 55"
                                                   required name="responsible_user" type="text"
                                                   class="form-control form-control-sm"
                                                   value="{{old('responsible_user')}}">
                                            @error('responsible_user')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="goods-add-parent-div p-2 mt-3 mb-4 rounded"
                                         style="background-color: rgba(255, 159, 117, 0.2)">
                                        <label for="basic-url" class="form-label form-label-sm">Материальные
                                            ценности <b>
                                                <span class="text-danger"> *</span></b></label>
                                        <div class="good-add-div mb-1 mb-md-0">
                                            <div class="row row-cols-1 row-cols-md-3">
                                                <div class="col-12 col-md-8">
                                                    <div class="input-group input-group-sm">
                                                                <span
                                                                    class="input-group-text good-add-button"><img
                                                                        src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                        alt="add" title="Добавить" height="20"></span>
                                                        <span class="input-group-text good-delete-button"><img
                                                                src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                alt="delete" title="Удалить" height="20"></span>
                                                        <input type="text"
                                                               class="form-control form-control-sm"
                                                               placeholder="Материальная ценность"
                                                               name="goods[name][]" required>
                                                    </div>
                                                </div>
                                                <div class="col-8 col-md-3 pe-0">
                                                    <select required name="goods[tare_type_id][]"
                                                            class="form-select form-select-sm">
                                                        @forelse($tare_types as $tare_type)
                                                            <option
                                                                value="{{$tare_type->id}}">{{$tare_type->name}}</option>
                                                        @empty
                                                            <option>нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </div>
                                                <div class="col-4 col-md-1 ps-0">
                                                    <input type="number" step="1"
                                                           class="form-control form-control-sm"
                                                           placeholder="Кол-во"
                                                           name="goods[value][]" required>
                                                </div>
                                            </div>
                                            @error('goods.*')
                                            <div class="text-danger"
                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm mt-2">
                                        <a href="{{route('admin_app_good_moves.index')}}"
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
    <script src="{{asset('assets/js/goods/add_good.js')}}"></script>
    <script src="{{asset('assets/js/goods/delete_good.js')}}"></script>
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
