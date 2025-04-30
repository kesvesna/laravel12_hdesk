@extends('layouts.backend.main')

@section('title', 'Главная | Статусы заявок в администрацию')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Статусы заявок в администрацию</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('admin_app_statuses.create')}}"><img
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
                                        <tbody style="cursor: pointer;">
                                        <tr>
                                            <td>
                                                <form action="{{route('admin_app_statuses.index')}}" method="get">
                                                    @csrf
                                                    <input class="form-control form-control-sm" list="datalistOptions"
                                                           id="exampleDataList" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="name"
                                                           value="{{$old_filters['name'] ?? null}}">
                                                    <datalist id="datalistOptions">
                                                        @forelse($all_statuses as $admin_app_status)
                                                            <option value="{{$admin_app_status->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </form>
                                            </td>
                                        </tr>
                                        @forelse($statuses as $status)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('admin_app_statuses.show', $status->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>{{$status->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td>нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$statuses->withQueryString()->links()}}
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
