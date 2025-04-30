@extends('layouts.backend.main')

@section('title', 'Просмотр | Климат/Помещение')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Климат/Помещение</h4>
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
                                    <li class="list-group-item"><b>ТРК: </b>{{$trk_room_climate->trk_room->trk->name}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Помещение: </b><a href="{{route('trk_room.show', $trk_room_climate->trk_room->id)}}">{{$trk_room_climate->trk_room->room->name}}</a></li>
                                    <li class="list-group-item"><b>Арендатор юр.
                                            лицо: </b>{{$trk_room_climate->trk_room->renter->organization->name ?? 'отсутствует'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Бренд: </b>{{$trk_room_climate->trk_room->renter->brand->name ?? 'отсутствует'}}
                                    </li>
                                    <li class="list-group-item"><b>Т на улице: </b>{{$trk_room_climate->t_outside}}</li>
                                    <li class="list-group-item"><b>Т в помещении: </b>{{$trk_room_climate->t_inside}}
                                    </li>
                                    <li class="list-group-item"><b>Т на притоке: </b>{{$trk_room_climate->t_supply_air}}
                                    </li>
                                    <li class="list-group-item"><b>Т на
                                            вытяжке: </b>{{$trk_room_climate->t_extract_air}}</li>
                                    <li class="list-group-item"><b>Влажность в
                                            помещении: </b>{{$trk_room_climate->h_inside}}</li>
                                    <li class="list-group-item"><b>Расход на
                                            притоке: </b>{{$trk_room_climate->q_supply_air_total}}</li>
                                    <li class="list-group-item"><b>Расход на
                                            вытяжке: </b>{{$trk_room_climate->q_extract_air_total}}</li>
                                    <li class="list-group-item"><b>Комментарий: </b>{{$trk_room_climate->comment}}</li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back()"
                                       class="btn btn-outline-success rounded col-4 col-md-2 me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('trk_room_climate edit')
                                        || auth()->user()->hasRole('sadmin')
                                        || $trk_room_climate->author->id == auth()->id())
                                        <a href="{{route('trk_room_climates.edit', $trk_room_climate)}}"
                                           class="btn btn-outline-warning rounded col-4 col-md-2 me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('trk_room_climates.destroy', $trk_room_climate)}}"
                                              method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn rounded btn-outline-danger"><img
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
