@extends('layouts.backend.main')

@section('title', 'Просмотр | ТРК/Склад')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">ТРК/Склад</h4>
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
                                    <li class="list-group-item"><b>ТРК: </b>{{$trk_store_house->trk->name}}</li>
                                    <li class="list-group-item">
                                        <b>Склад: </b>{{$trk_store_house->store_house_name->name}}</li>
                                    <li class="list-group-item">
                                        <b>Запчасть: </b>{{$trk_store_house->spare_part_name->name}}</li>
                                    <li class="list-group-item"><b>Модель: </b>{{$trk_store_house->spare_part_model}}
                                    </li>
                                    <li class="list-group-item"><b>Количество: </b>{{$trk_store_house->value}}</li>
                                    <li class="list-group-item"><b>Необходимый
                                            минимум: </b>{{$trk_store_house->min_required_value}}</li>
                                    <li class="list-group-item">
                                        <b>Комментарий: </b>{{$trk_store_house->comment ?? 'отсутствует'}}</li>
                                    <li class="list-group-item">
                                        <div class="table-responsive">
                                            <?php $counter = 1; ?>
                                            <table class="table table-sm table-striped table-hover shadow rounded caption-top">
                                                <caption>Где используется</caption>
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Трк</th>
                                                        <th>Блок</th>
                                                        <th>Этаж</th>
                                                        <th>Помещение</th>
                                                        <th>Оборудование</th>
                                                        <th>Комментарий</th>
                                                        <th>Потребители</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                @forelse($trk_equipments_use_this as $spare_part_user)
                                                    <tr style="cursor: pointer;" onclick="window.location='{{ route('trk_equipments.show', $spare_part_user->trk_equipment->id) }}';">
                                                        <td>{{$counter++}}</td>
                                                        <td class="text-nowrap">{{$spare_part_user->trk_equipment->trk_room->trk->name}}</td>
                                                        <td class="text-nowrap">{{$spare_part_user->trk_equipment->trk_room->building->name}}</td>
                                                        <td class="text-nowrap">{{$spare_part_user->trk_equipment->trk_room->floor->name}}</td>
                                                        <td class="text-nowrap">{{$spare_part_user->trk_equipment->trk_room->room->name}}</td>
                                                        <td class="text-nowrap">{{$spare_part_user->trk_equipment->equipment_name->name}}</td>
                                                        <td>{{$spare_part_user->comment}}</td>
                                                        <td>
                                                            @forelse($spare_part_user->trk_equipment->users as $equipment_user)
                                                                @if(isset($equipment_user->trk_room->renter))
                                                                    {!! $equipment_user->trk_room->renter->brand->name . '<br>' !!}
                                                                @endif
                                                            @empty
                                                            @endforelse
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <td colspan="8">нет совпадений ...</td>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </li>
                                    <li class="list-group-item"><b>Создан: </b>{{$trk_store_house->created_at . ', ' . $trk_store_house->author->name}}</li>
                                    <li class="list-group-item"><b>Исправлен: </b>{{$trk_store_house->updated_at . ', ' . $trk_store_house->last_editor->name}}</li>
                                    </li>

                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back()"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('trk_store_house update')
                                        || auth()->user()->can('all')
                                        || $trk_store_house->author->id == auth()->id())
                                        <a href="{{route('trk_store_houses.edit', $trk_store_house)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('trk_store_house delete')
                                        || auth()->user()->can('all')
                                        || $trk_store_house->author->id == auth()->id())
                                        <form action="{{route('trk_store_houses.destroy', $trk_store_house)}}"
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
