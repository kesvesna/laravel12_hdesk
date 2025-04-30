@extends('layouts.backend.main')

@section('title', 'Просмотр | Название запчасти')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Название запчасти</h4>
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
                            <div class="card shadow p-3">
                                <ul class="list-group mb-3">
                                    <li class="list-group-item"><b>Название: </b>{{$spare_part_name->name}}</li>
                                </ul>
                                @if(count($spare_part_name->store_houses) > 0)
                                    <p>Используется на складах: {{count($spare_part_name->store_houses) . 'шт.'}}</p>
                                    @foreach($spare_part_name->store_houses as $store_house)
                                        <a href="{{route('trk_store_houses.show', $store_house->id)}}">{{$store_house->trk->name . ', ' . $store_house->spare_part_model}}</a>
                                    @endforeach
                                @endif
                                @if(count($spare_part_name->equipment_spare_parts) > 0)
                                    <br>
                                    <p>Используется в оборудовании: {{count($spare_part_name->equipment_spare_parts) . 'шт.'}}</p>
                                    @foreach($spare_part_name->equipment_spare_parts as $equipment_spare_part)
                                        <a href="{{route('equipment_spare_parts.show', $equipment_spare_part->id)}}">{{$equipment_spare_part->trk_equipment->trk_room->trk->name . ', ' . $equipment_spare_part->trk_equipment->equipment_name->name . ', ' . $equipment_spare_part->model}}</a>
                                    @endforeach
                                @endif
                                @if(count($spare_part_name->avr_spare_parts) > 0)
                                    <br>
                                    <p>Используется в актах: {{count($spare_part_name->avr_spare_parts) . 'шт.'}}</p>
                                    @foreach($spare_part_name->avr_spare_parts as $avr_spare_part)
                                        <a href="{{route('avrs.show', $avr_spare_part->avr_id)}}">{{$avr_spare_part->trk_equipment->trk_room->trk->name . ', ' . $avr_spare_part->trk_equipment->equipment_name->name . ', ' . $avr_spare_part->model}}</a>
                                    @endforeach
                                @endif
                                @if(count($spare_part_name->order_spare_parts) > 0)
                                    <br>
                                    <p>Используется в заказах: {{count($spare_part_name->order_spare_parts) . 'шт.'}}</p>
                                    @foreach($spare_part_name->order_spare_parts as $order_spare_part)
                                        <a href="{{route('orders.show', $order_spare_part->order_id)}}">{{$order_spare_part->created_at . ', ' . $order_spare_part->model}}</a>
                                    @endforeach
                                @endif
                                <div class="btn-group btn-group-sm col-12 col-md-6 mt-4" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('spare_part_names.index')}}"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit'))
                                        <a href="{{route('spare_part_names.edit', $spare_part_name)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('spare_part_names.destroy', $spare_part_name)}}"
                                              method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-outline-danger"><img
                                                    src="{{asset('assets/images/backend/svg/trash3.svg')}}" alt="delete"
                                                    title="Удалить"></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- profile init js -->
            <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
