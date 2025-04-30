@extends('layouts.backend.main')

@section('title', 'Главная | Поздраделение/Должность')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Поздраделение/Должность</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('user_division_functions.create')}}"><img
                                    src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                    title="Добавить" height="30"></a>
                        @endif()
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
                                    <table class="table table-striped table-hover shadow table-bordered">
                                        <thead>
                                        <tr>
                                            <th class="d-none d-md-table-cell">Подразделение</th>
                                            <th class="d-none d-md-table-cell">Должность</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('user_division_functions.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td class="d-none d-md-table-cell">
                                                    <select name="user_division_id" class="form-select form-select-sm" id="user_division_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_divisions as $division)
                                                            <option
                                                                value="{{$division->id}}" {{isset($old_filters['user_division_id']) && $old_filters['user_division_id'] == $division->id ? 'selected' : null}}>{{$division->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <select name="user_function_id" class="form-select form-select-sm"
                                                            id="user_function_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_functions as $function)
                                                            <option
                                                                value="{{$function->id}}" {{isset($old_filters['user_function_id']) && $old_filters['user_function_id'] == $function->id ? 'selected' : null}}>{{$function->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                            </tr>
                                        </form>
                                        @forelse($user_division_functions as $user_division_function)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('user_division_functions.show', $user_division_function->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="d-none d-md-table-cell">{{$user_division_function->division->name}}</td>
                                                    <td class="d-none d-md-table-cell">{{$user_division_function->function->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$user_division_functions->withQueryString()->links()}}
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
