@extends('layouts.backend.main')

@section('title', 'Главная | Склады/Пользователи')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Склады/Пользователи</h4>
                        @if(auth()->user()->can('trk_store_house create'))
                            <a href="{{route('trk_store_house_users.create')}}"><img
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
                                            <th class="d-none d-md-table-cell">Склад</th>
                                            <th>Пользователь</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        @if(auth()->user()->can('read'))
                                            <form action="{{route('trk_store_house_users.index')}}" method="get">
                                                @csrf
                                                <tr>
                                                    <td>
                                                        <select class="form-select form-select-sm" id="trk_id"
                                                                onchange="this.form.submit();" name="trk_id">
                                                            <option value="">Все</option>
                                                            @forelse($trks as $trk)
                                                                <option
                                                                    value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                    <td class="d-none d-md-table-cell">
                                                        <input class="form-control form-control-sm"
                                                               list="store_house_data_list" type="search"
                                                               id="store_house_name_id" placeholder="Поиск ..."
                                                               onchange="this.form.submit();" name="store_house_name_id"
                                                               value="{{$old_filters['store_house_name_id'] ?? null}}">
                                                        <datalist id="store_house_data_list">
                                                            @forelse($store_house_names as $store_house_name)
                                                                <option value="{{$store_house_name->name}}">
                                                            @empty
                                                                <option value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm" id="user_id"
                                                                onchange="this.form.submit();" name="user_id">
                                                            <option value="">Все</option>
                                                            @forelse($users as $user)
                                                                <option
                                                                    value="{{$user->id}}" {{isset($old_filters['user_id']) && $old_filters['user_id'] == $user->id ? 'selected' : null}}>{{$user->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                </tr>
                                            </form>
                                            @forelse($trk_store_houses as $trk_store_house)
                                                <tr onclick="window.location='{{ route('trk_store_house_users.show', $trk_store_house->id) }}';">
                                                    <td>{{$trk_store_house->trk->name}}</td>
                                                    <td class="d-none d-md-table-cell">{{$trk_store_house->store_house->name}}</td>
                                                    <td>{{$trk_store_house->user->name}}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="3">нет данных ...</td>
                                                </tr>
                                            @endforelse
                                        @endif
                                        </tbody>
                                    </table>
                                    {{$trk_store_houses->withQueryString()->links()}}
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
