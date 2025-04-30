@extends('layouts.backend.main')

@section('title', 'Главная | Помещения')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Помещения</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('parameter_names.create')}}"><img
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
                                                <form action="{{route('parameter_names.index')}}" method="get">
                                                    @csrf
                                                    <input class="form-control form-control-sm" list="datalistOptions"
                                                           id="exampleDataList" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="name"
                                                           value="{{$old_filters['name'] ?? null}}">
                                                    <datalist id="datalistOptions">
                                                        @forelse($all_parameter_names as $parameter_name)
                                                            <option value="{{$parameter_name->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </form>
                                            </td>
                                        </tr>
                                        @forelse($parameter_names as $parameter_name)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('parameter_names.show', $parameter_name->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>{{$parameter_name->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td>нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$parameter_names->withQueryString()->links()}}
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
