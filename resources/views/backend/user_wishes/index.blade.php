@extends('layouts.backend.main')

@section('title', 'Главная | Пожелания по функционалу сайта')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Пожелания по функционалу сайта</h4>
                            <a href="{{route('user_wishes.create')}}"><img
                                    src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                    title="Добавить" height="30"></a>
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
                                            <th>Описание</th>
                                            <th>Решено</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('user_wishes.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td></td>
                                                <td>
                                                    <input class="form-control form-control-sm" type="search"
                                                           id="wish_description" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="wish_description"
                                                           value="{{$old_filters['wish_description'] ?? null}}">
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" type="search"
                                                           id="resolution_description" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="resolution_description"
                                                           value="{{$old_filters['resolution_description'] ?? null}}">
                                                </td>
                                            </tr>
                                        </form>
                                        @forelse($user_wishes as $user_wishe)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('user_wishes.show', $user_wishe->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>{{$user_wishe->created_at}}</td>
                                                    <td>{{$user_wishe->wish_description}}</td>
                                                    <td>{{$user_wishe->resolution_description}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="3">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$user_wishes->withQueryString()->links()}}
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
