@extends('layouts.backend.main')

@section('title', 'Просмотр | Арендатор/Помещение')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Арендатор/Помещение</h4>
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
                                    <li class="list-group-item">
                                        <b>ТРК: </b>{{$renter_trk_room_brand->trk_room->trk->name ?? 'не выбрано'}}</li>
                                    <li class="list-group-item"><b>Юр.
                                            лицо: </b>{{$renter_trk_room_brand->organization->name ?? 'не выбрано'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Помещение: </b><a href="{{route('trk_room.show', $renter_trk_room_brand->trk_room->id)}}">{{$renter_trk_room_brand->trk_room->room->name ?? 'не выбрано'}}</a>
                                    </li>
                                    <li class="list-group-item">
                                        <b>Бренд: </b>{{$renter_trk_room_brand->brand->name ?? 'не выбрано'}}</li>
                                    <li class="list-group-item">
                                        <div class="table-responsive">
                                            <table class="table table-sm table-striped table-hover caption-top">
                                                <caption>Счетчики</caption>
                                                <thead>
                                                    <tr>
                                                        <th class="text-nowrap">Номер счетчика</th>
                                                        <th class="text-nowrap">Дата последних показаний</th>
                                                        <th>Показания</th>
                                                        <th>Тариф</th>
                                                    </tr>
                                                </thead>
                                                <tbody style="cursor: pointer;">
                                                @forelse($counters as $counter)
                                                    <tr onclick="window.location='{{ route('trk_room_counters.show', $counter->id) }}';">
                                                        <td class="text-nowrap">{{$counter->number}}</td>
                                                        <td class="text-nowrap">{{$counter->counts->last()->date}}</td>
                                                        <td>{{$counter->counts->last()->count}}</td>
                                                        <td>{{$counter->counter_tariff->name}}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4">отсутствуют ...</td>
                                                    </tr>
                                                @endforelse
                                                </tbody>
                                            </table>
                                        </div>
                                    </li>
                                    <li class="list-group-item"></li>
                                    <li class="list-group-item"><b>Создано: </b>{{$renter_trk_room_brand->created_at . ', '}}
                                        {{$renter_trk_room_brand->brand->author->name}}
                                    </li>
                                    <li class="list-group-item"><b>Исправлено: </b>{{$renter_trk_room_brand->updated_at . ', '}}
                                        {{$renter_trk_room_brand->last_editor->name}}
                                    </li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('renter_trk_room_brands.index')}}"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('renter update'))
                                        <a href="{{route('renter_trk_room_brands.edit', $renter_trk_room_brand)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form
                                            action="{{route('renter_trk_room_brands.destroy', $renter_trk_room_brand)}}"
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
