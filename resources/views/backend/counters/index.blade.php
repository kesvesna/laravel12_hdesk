@extends('layouts.backend.main')

@section('title', 'Главная | Счетчики')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Счетчики</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('counters.create')}}"><img
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
                                            <th>№ счетчика</th>
                                            <th>Тип</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('counters.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td class="d-none d-md-table-cell">
                                                    <input class="form-control form-control-sm" list="counter_data_list"
                                                           type="search" id="number" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="number"
                                                           value="{{$old_filters['number'] ?? null}}">
                                                    <datalist id="counter_data_list">
                                                        @forelse($all_counters as $counter)
                                                            <option value="{{$counter->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <select name="counter_type_id" class="form-select form-select-sm"
                                                            id="counter_type_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_counter_types as $counter_type)
                                                            <option
                                                                value="{{$counter_type->id}}" {{isset($old_filters['counter_type_id']) && $old_filters['counter_type_id'] === $counter_type->id ? 'selected' : null}}>{{$counter_type->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                            </tr>
                                        </form>
                                        @forelse($counters as $counter)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('counters.show', $counter->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>{{$counter->number}}</td>
                                                    <td>{{$counter->type->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$counters->withQueryString()->links()}}
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
