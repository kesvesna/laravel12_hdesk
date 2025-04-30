@extends('layouts.backend.main')

@section('title', 'Главная | Технические акты')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Технические акты</h4>
                        @if(auth()->user()->can('tech_act create'))
                            <a href="{{route('tech_acts.create')}}"><img
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
                                            <th class="d-none d-md-table-cell">Дата</th>
                                            <th>ТРК</th>
                                            <th class="d-none d-md-table-cell">Место</th>
                                            <th class="d-none d-md-table-cell">Оборудование</th>
                                            <th>Проблема</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        {{--                                            <tr>--}}
                                        {{--                                                <td>--}}
                                        {{--                                                    <form action="{{route('tech_acts.index')}}" method="get">--}}
                                        {{--                                                        @csrf--}}
                                        {{--                                                        <input type="search" class="form-control form-control-sm" list="tech_act_data_list" id="tech_act" placeholder="Поиск ..." onchange="this.form.submit();" name="name" value="{{$old_filters['name'] ?? null}}">--}}
                                        {{--                                                        <datalist id="tech_act_data_list">--}}
                                        {{--                                                            @forelse($all_tech_acts as $tech_act)--}}
                                        {{--                                                                <option value="{{$tech_act->name}}">--}}
                                        {{--                                                            @empty--}}
                                        {{--                                                                <option value="нет данных ...">--}}
                                        {{--                                                            @endforelse--}}
                                        {{--                                                        </datalist>--}}
                                        {{--                                                    </form>--}}
                                        {{--                                                </td>--}}
                                        {{--                                            </tr>--}}
                                        @forelse($tech_acts as $tech_act)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('tech_acts.show', $tech_act->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="d-none d-md-table-cell">{{$tech_act->write_at}}</td>
                                                    <td>{{$tech_act->trk->name}}</td>
                                                    <td class="d-none d-md-table-cell">{{$tech_act->room_name}}</td>
                                                    <td class="d-none d-md-table-cell">{{$tech_act->equipment_name}}</td>
                                                    <td>{{$tech_act->trouble_description}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$tech_acts->withQueryString()->links()}}
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
