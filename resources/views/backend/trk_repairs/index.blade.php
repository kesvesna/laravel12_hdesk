@extends('layouts.backend.main')

@section('title', 'Главная | Ремонт')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Ремонт</h4>
                        @if(auth()->user()->can('repair create'))
                            <a href="{{route('trk_repairs.create')}}"><img
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
                                            <th>ТРК</th>
                                            <th class="d-none d-md-table-cell">Подразделение</th>
                                            <th>Оборудование</th>
                                            <th class="d-none d-md-table-cell">Описание</th>
                                            <th class="d-none d-md-table-cell">Выполнить до</th>
                                            <th class="d-none d-md-table-cell">Выполнен</th>
                                            <th class="d-none d-md-table-cell">Готово, %</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('trk_repairs.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td>
                                                    <select class="form-select form-select-sm" name="trk_id" id="trk_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_trks as $trk)
                                                            <option
                                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td class="d-none d-sm-table-cell">
                                                    <select class="form-select form-select-sm" name="user_division_id"
                                                            id="user_division_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_user_divisions as $user_division)
                                                            <option
                                                                value="{{$user_division->id}}" {{isset($old_filters['user_division_id']) && $old_filters['user_division_id'] == $user_division->id ? 'selected' : null}}>{{$user_division->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm"
                                                           list="equipment_name_list" type="search" id="equipment_name"
                                                           placeholder="Поиск ..." onchange="this.form.submit();"
                                                           name="equipment_name"
                                                           value="{{$old_filters['equipment_name'] ?? null}}">
                                                    <datalist id="equipment_name_list">
                                                        @forelse($all_equipment_names as $equipment)
                                                            <option value="{{$equipment->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <input class="form-control form-control-sm" type="search"
                                                           id="description" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="description"
                                                           value="{{$old_filters['description'] ?? null}}">
                                                </td>
                                                <td class="d-none d-md-table-cell"></td>
                                                <td></td>
                                                <td class="d-none d-md-table-cell">
                                                    <input class="form-control form-control-sm" type="number"
                                                           step="10"
                                                           id="done_percents" placeholder="%"
                                                           onchange="this.form.submit();" name="done_percents"
                                                           value="{{$old_filters['done_percents'] ?? null}}">
                                                </td>
                                            </tr>
                                        </form>
                                        @forelse($trk_repairs as $trk_repair)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('trk_repairs.show', $trk_repair->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>{{$trk_repair->trk_room->trk->name}}</td>
                                                    <td class="d-none d-md-table-cell">{{$trk_repair->user_division->name}}</td>
                                                    <td>{{$trk_repair->trk_equipment->equipment_name->name}}</td>
                                                    <td class="d-none d-md-table-cell">{{$trk_repair->description}}</td>
                                                    <td class="d-none d-md-table-cell text-nowrap">{{$trk_repair->deadline_at}}</td>
                                                    <td class="d-none d-md-table-cell text-nowrap">{{$trk_repair->executed_at != '' ? date('Y-m-d', strtotime($trk_repair->executed_at)) : null}}</td>
                                                    <td class="d-none d-md-table-cell">{{$trk_repair->done_progress}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$trk_repairs->withQueryString()->links()}}
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
