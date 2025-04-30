@extends('layouts.backend.main')

@section('title', 'Главная | Обходы')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Обходы</h4>
                        @if(auth()->user()->can('room_check create'))
                            <a href="{{route('room_checks.create')}}"><img
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
                                    <table class="table table-striped table-hover shadow">
                                        <thead>
                                        <tr>
                                            <th>Дата</th>
                                            <th class="d-none d-md-table-cell">ТРК</th>
                                            <th>Помещение</th>
                                            <th class="d-none d-md-table-cell">Комментарий</th>
                                            <th class="d-none d-md-table-cell">Сотрудник</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        {{--                                            <tr>--}}
                                        {{--                                                <td>--}}
                                        {{--                                                    <form action="{{route('room_checks.index')}}" method="get">--}}
                                        {{--                                                        @csrf--}}
                                        {{--                                                        <input type="search" class="form-control form-control-sm" list="room_check_data_list" id="room_check" placeholder="Поиск ..." onchange="this.form.submit();" name="name" value="{{$old_filters['name'] ?? null}}">--}}
                                        {{--                                                        <datalist id="room_check_data_list">--}}
                                        {{--                                                            @forelse($all_room_checks as $room_check)--}}
                                        {{--                                                                <option value="{{$room_check->name}}">--}}
                                        {{--                                                            @empty--}}
                                        {{--                                                                <option value="нет данных ...">--}}
                                        {{--                                                            @endforelse--}}
                                        {{--                                                        </datalist>--}}
                                        {{--                                                    </form>--}}
                                        {{--                                                </td>--}}
                                        {{--                                            </tr>--}}
                                        @forelse($room_checks as $room_check)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('room_checks.show', $room_check->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>{{$room_check->created_at}}</td>
                                                    <td class="d-none d-md-table-cell">{{$room_check->trk_room->trk->name}}</td>
                                                    <td>{{$room_check->trk_room->room->name}}</td>
                                                    <td class="d-none d-md-table-cell">{{$room_check->comment}}</td>
                                                    <td class="d-none d-md-table-cell">{{$room_check->author->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$room_checks->withQueryString()->links()}}
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
