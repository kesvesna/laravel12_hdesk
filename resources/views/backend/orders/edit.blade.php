@extends('layouts.backend.main')

@section('title', 'Главная | Заказ редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Заказ редактирование</h4>
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
                            <form action="{{route('orders.update', $order)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row row-cols-1">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="trk_id" class="form-select form-select-sm"
                                                    id="trk_id">
                                                @forelse($trks as $trk)
                                                    <option value="{{$trk->id}}" {{isset($order) && $order->trk->id == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="system_id" class="form-label form-label-sm">Система <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="system_id" class="form-select form-select-sm">
                                                @forelse($systems as $system)
                                                    <option value="{{$system->id}}" {{isset($order) && $order->system->id == $system->id ? 'selected' : null}}>{{$system->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('system_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="room_name" class="form-label form-label-sm">Помещение</label>
                                            <input type="text" list="room_names_list"
                                                   class="form-control form-control-sm room-name-input"
                                                   placeholder="Начните писать ..."
                                                   name="room_name" data-equipment-id="0" value="{{isset($order->room) ? $order->room->name : null}}">
                                            <datalist id="room_names_list">
                                                @forelse($rooms as $room)
                                                    <option data-equipment_key="{{$room->id}}"
                                                            value="{{$room->name}}">
                                                @empty
                                                    <option data-equipment_key="" value="нет данных ...">
                                                @endforelse
                                            </datalist>
                                            @error('room_name')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="equipment_name" class="form-label form-label-sm">Оборудование</label>
                                            <input type="text" list="equipment_names_list"
                                                   class="form-control form-control-sm equipment-name-input"
                                                   placeholder="Начните писать ..."
                                                   name="equipment_name" data-equipment-id="0" value="{{isset($order->equipment) ? $order->equipment->name : null}}">
                                            <datalist id="equipment_names_list">
                                                @forelse($equipment_names as $equipment)
                                                    <option data-equipment_key="{{$equipment->id}}"
                                                            value="{{$equipment->name}}">
                                                @empty
                                                    <option data-equipment_key="" value="нет данных ...">
                                                @endforelse
                                            </datalist>
                                        </div>
                                        <div class="col mb-3">
                                            <label for="comment" class="form-label form-label-sm">Комментарий</label>
                                            <input type="text"
                                                   class="form-control form-control-sm equipment-name-input"
                                                   placeholder="Любой комментарий к заказу"
                                                   name="comment" value="{{$order->comment}}">
                                        </div>
                                        <div class="col mb-4">
                                            <label for="is_urgency" class="form-label form-label-sm">Срочность заказа <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="is_urgency" class="form-select form-select-sm"
                                                    id="is_urgency" {{$order->is_urgency ? 'срочно' : 'не срочно'}}>
                                                <option value="0">не срочно</option>
                                                <option value="1">срочно</option>
                                            </select>
                                            @error('is_urgency')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-4">
                                            <label for="order_status_id" class="form-label form-label-sm">Статус заказа <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="order_status_id" class="form-select form-select-sm"
                                                    id="order_status_id">
                                                @forelse($order_statuses as $order_status)
                                                    <option value="{{$order_status->id}}" {{$order->status->id == $order_status->id ? 'selected' : null}}>{{$order_status->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('order_status_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="spare-parts-add-parent-div p-2 mb-1 rounded"
                                         style="background-color: rgba(255, 159, 117, 0.2)">
                                        <label for="basic-url" class="form-label form-label-sm">Запчасти</label>
                                        @forelse($order->spare_parts as $spare_part)
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
                                                        <input required type="text" list="spare_parts_list"
                                                               class="form-control form-control-sm spare-part-name-input"
                                                               placeholder="Начните писать ..."
                                                               name="spare_parts[name][]"
                                                               data-spare-part-name-id="0" value="{{$spare_part->spare_part_name->name}}">
                                                        <datalist id="spare_parts_list">
                                                            @forelse($all_spare_parts as $one_spare_part)
                                                                <option value="{{$one_spare_part->name}}">
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
                                                               name="spare_parts[model][]"
                                                               data-spare-part-model-id="0" value="{{$spare_part->model}}">
                                                        <input required type="number" step="0.1"
                                                               class="form-control form-control-sm spare-part-value-input"
                                                               placeholder="Количество"
                                                               name="spare_parts[value][]"
                                                               data-spare-part-value-id="0" value="{{$spare_part->value}}">
                                                    </div>
                                                </div>
                                                @error('spare_parts.*')
                                                <div class="text-danger"
                                                     style="margin-top: -1rem !important;">{{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                            @empty
                                            <span>нет данных ...</span>
                                        @endforelse
                                    </div>
                                    <div class="col mb-3 mt-3">
                                        <label for="account_number" class="form-label form-label-sm">Номер счета</label>
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               placeholder="Номер счета"
                                               name="account_number" value="{{$order->account_number}}">
                                        @error('account_number')
                                        <div class="text-danger"
                                             style="margin-top: -1rem !important;">{{$message}}</div>
                                        @enderror
                                    </div>
                                    <div class="col mb-3">
                                        <label for="provider" class="form-label form-label-sm">Поставщик</label>
                                        <input type="text"
                                               class="form-control form-control-sm"
                                               placeholder="Поставщик"
                                               name="provider" value="{{$order->provider}}">
                                        @error('provider')
                                        <div class="text-danger"
                                             style="margin-top: -1rem !important;">{{$message}}</div>
                                        @enderror
                                    </div>
                                    <div class="col mb-3">
                                        <label for="delivery_at" class="form-label form-label-sm">Дата поставки</label>
                                        <input type="date"
                                               class="form-control form-control-sm"
                                               name="delivery_at" value="{{$order->delivery_at}}">
                                        @error('delivery_at')
                                        <div class="text-danger"
                                             style="margin-top: -1rem !important;">{{$message}}</div>
                                        @enderror
                                    </div>
                                    <div class="col mb-3">
                                        <label for="closed_at" class="form-label form-label-sm">Дата закрытия заказа</label>
                                        <input type="date"
                                               class="form-control form-control-sm"
                                               name="closed_at" value="{{$order->closed_at}}">
                                        @error('closed_at')
                                        <div class="text-danger"
                                             style="margin-top: -1rem !important;">{{$message}}</div>
                                        @enderror
                                    </div>
                                    <div class="input-group input-group-sm mt-3">
                                        <a href="{{route('orders.show', $order)}}"
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
        <script src="{{asset('assets/js/orders/add_spare_part.js')}}"></script>
        <script src="{{asset('assets/js/orders/delete_spare_part.js')}}"></script>
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
