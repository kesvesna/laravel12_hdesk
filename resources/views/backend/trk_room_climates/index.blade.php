@extends('layouts.backend.main')

@section('title', 'Главная | Климат/Помещения')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Климат/Помещения</h4>
                        @if(auth()->user()->can('trk_room_climate create') || auth()->user()->hasRole('sadmin'))
                            <a href="{{route('trk_room_climates.create')}}"><img
                                    src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                    title="Добавить" height="30"></a>
                        @endif
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
                                    <div class="table-responsive">
                                    <table class="table table-striped table-hover shadow">
                                        <thead>
                                        <tr>
                                            <th>Дата</th>
                                            <th>ТРК</th>
                                            <th>Помещение</th>
                                            <th>T внутри</th>
                                            <th>T притока</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('trk_room_climates.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td>
                                                    <input class="form-control form-control-sm"
                                                           type="search" id="created_at" placeholder="2023-09, 2023-09-20"
                                                           onchange="this.form.submit();" name="created_at"
                                                           value="{{$old_filters['created_at'] ?? null}}">
                                                </td>
                                                <td>
                                                    <select name="trk_id" class="form-select form-select-sm" id="trk_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_trks as $trk)
                                                            <option
                                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" list="room_data_list"
                                                           type="search" id="room_name" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="room_name"
                                                           value="{{$old_filters['room_name'] ?? null}}">
                                                    <datalist id="room_data_list">
                                                        @forelse($all_rooms as $room)
                                                            <option value="{{$room->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </form>
                                        @forelse($trk_room_climates as $trk_room_climate)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('trk_room_climates.show', $trk_room_climate->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="text-nowrap">{{$trk_room_climate->created_at}}</td>
                                                    <td class="text-nowrap">{{$trk_room_climate->trk_room->trk->name}}</td>
                                                    <td class="text-nowrap">{{$trk_room_climate->trk_room->room->name}}</td>
                                                    <td class="text-nowrap">{{$trk_room_climate->t_inside}}</td>
                                                    <td class="text-nowrap">{{$trk_room_climate->t_supply_air}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    </div>
                                    {{$trk_room_climates->withQueryString()->links()}}
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
