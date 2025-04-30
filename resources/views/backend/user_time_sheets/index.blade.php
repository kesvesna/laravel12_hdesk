@extends('layouts.backend.main')

@section('title', 'Главная | Табели')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Табели</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('user_time_sheets.create')}}"><img
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
                                <div class="card-title ps-3 pt-3">
                                    <div class="col">
                                        <button type="button" class="btn btn-outline-success btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#user_time_sheets">Выгрузка
                                            табеля
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-hover shadow">
                                        <thead>
                                        <tr>
                                            <th>Сотрудник</th>
                                            <th>Год</th>
                                            <th>Месяц</th>
                                            <th class="d-none d-md-table-cell">Всего, ч</th>
                                            <th class="d-none d-md-table-cell">Сверх, ч</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('user_time_sheets.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td>
                                                    <select name="user_id" class="form-select form-select-sm"
                                                            id="user_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($users as $user)
                                                            <option
                                                                value="{{$user->id}}" {{isset($old_filters['user_id']) && $old_filters['user_id'] == $user->id ? 'selected' : null}}>{{$user->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="year" class="form-select form-select-sm" id="year"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($years as $year)
                                                            <option
                                                                value="{{$year}}" {{isset($old_filters['year']) && $old_filters['year'] == $year ? 'selected' : null}}>{{$year}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="month" class="form-control form-control-sm" id="month"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($months as $key => $value)
                                                            <option
                                                                value="{{$key}}" {{isset($old_filters['month']) && $old_filters['month'] == $key ? 'selected' : null}}>{{$value}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td class="d-none d-md-table-cell"></td>
                                                <td class="d-none d-md-table-cell"></td>
                                            </tr>
                                        </form>
                                        @forelse($user_result_time_sheets as $user_result_time_sheet)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('user_time_sheets.show', ['user' => $user_result_time_sheet->user, 'year' => $user_result_time_sheet->year, 'month' => $user_result_time_sheet->month]) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>{{$user_result_time_sheet->user->name}}</td>
                                                    <td>{{$user_result_time_sheet->year}}</td>
                                                    <td>{{$user_result_time_sheet->month}}</td>
                                                    <td class="d-none d-md-table-cell">{{$user_result_time_sheet->result}}</td>
                                                    <td class="d-none d-md-table-cell">{{$user_result_time_sheet->overtime}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$user_result_time_sheets->withQueryString()->links()}}
                                </div>
                                <!-- Modal for user_time_sheets -->
                                <div class="modal fade" id="user_time_sheets" tabindex="-1"
                                     aria-labelledby="user_time_sheets" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h1 class="modal-title fs-5" id="exampleModalLabel5">Выгрузка
                                                    табеля</h1>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="{{route('user_time_sheets.export')}}" method="post">
                                                    @csrf
                                                    @method('post')
                                                    <div class="row">
                                                        <div class="col mb-3">
                                                            <label class="form-label form-label-sm" for="user_id_2">Сотрудник</label>
                                                            <select required name="user_id"
                                                                    class="form-select form-select-sm" id="user_id_2">
                                                                @forelse($users as $user)
                                                                    <option
                                                                        value="{{$user->id}}" {{isset($old_filters['user_id']) && $old_filters['user_id'] == $user->id ? 'selected' : null}}>{{$user->name}}</option>
                                                                @empty
                                                                    <option value="">нет данных ...</option>
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col mb-3">
                                                            <label class="form-label form-label-sm"
                                                                   for="year_2">Год</label>
                                                            <select name="year" class="form-select form-select-sm"
                                                                    id="year_2">
                                                                @forelse($years as $year)
                                                                    <option
                                                                        value="{{$year}}" {{isset($old_filters['year']) && $old_filters['year'] == $year ? 'selected' : null}}>{{$year}}</option>
                                                                @empty
                                                                    <option value="">нет данных ...</option>
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col mb-3">
                                                            <label class="form-label form-label-sm"
                                                                   for="month_2">Месяц</label>
                                                            <select name="month" class="form-control form-control-sm"
                                                                    id="month_2">
                                                                @forelse($months as $key => $value)
                                                                    <option
                                                                        value="{{$key}}" {{isset($old_filters['month']) && $old_filters['month'] == $key ? 'selected' : null}}>{{$value}}</option>
                                                                @empty
                                                                    <option value="">нет данных ...</option>
                                                                @endforelse
                                                            </select></div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col mb-3">
                                                            <label class="form-label form-label-sm" for="file_type">Тип
                                                                файла</label>
                                                            <select required name="file_type"
                                                                    class="form-select form-select-sm" id="file_type">
                                                                <option value=".xslx">EXCEL XSLX</option>
                                                                <option value=".pdf">PDF</option>
                                                                <option value=".html">HTML</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <button type="submit" class="btn btn-success btn-sm">Выгрузить
                                                        табель
                                                    </button>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    Закрыть
                                                </button>
                                            </div>
                                        </div>
                                    </div>
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
