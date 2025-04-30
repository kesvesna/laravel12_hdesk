@extends('layouts.backend.main')

@section('title', 'Главная | Статистика ТРК по запчасти')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Статистика ТРК по запчасти</h4>
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
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="javascript:history.back()"
                                               class="btn btn-sm btn-outline-success col-4 col-md-2 rounded me-2"><img
                                                    src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                                    title="Назад"></a>
                                        </div>
                                    </div>
                                    <h6 class="mt-4"><b>{{$trk->name}}</b></h6>
                                    <span>Где используется {{$spare_part_name->name . ' - ' . $model}}</span>
                                <div class="table-responsive mt-3">
                                    <table class="table table-sm table-striped table-hover table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Помещение</th>
                                                <th>Оборудование</th>
                                                <th>Запчасть</th>
                                                <th>Модель</th>
                                                <th>Количество</th>
                                                <th>Комментарий</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $counter = 1; ?>
                                        @foreach($equipment_spare_parts as $equipment_spare_part)
                                            <tr  onclick="window.location='{{ route('trk_equipments.show', $equipment_spare_part->trk_equipment->id) }}';" style="cursor: pointer;">
                                                <td>{{$counter++}}</td>
                                                <td class="text-nowrap">{{$equipment_spare_part->trk_equipment->trk_room->room->name}}</td>
                                                <td class="text-nowrap">{{$equipment_spare_part->trk_equipment->equipment_name->name}}</td>
                                                <td class="text-nowrap">{{$equipment_spare_part->part_name->name}}</td>
                                                <td class="text-nowrap">{{$equipment_spare_part->model}}</td>
                                                <td>{{$equipment_spare_part->value}}</td>
                                                <td>{{$equipment_spare_part->comment}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                    <div class="row">
                                        <div class="col-12 mt-2">
                                            <a href="javascript:history.back()"
                                               class="btn btn-sm btn-outline-success col-4 col-md-2 rounded me-2"><img
                                                    src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                                    title="Назад"></a>
{{--                                            <form action="" method="post">--}}
{{--                                                @csrf--}}
{{--                                                @method('post')--}}
{{--                                                <button type="submit" class="btn btn-sm btn-outline-success col-4 col-md-2"><img--}}
{{--                                                        src="{{asset('assets/images/backend/svg/printer.svg')}}" alt="delete"--}}
{{--                                                        title="На печать"></button>--}}
{{--                                            </form>--}}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
