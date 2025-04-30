@extends('layouts.backend.main')

@section('title', 'Главная | Табель')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Табель</h4>
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
                    <div class="row">
                        <div class="col">
                            <div class="card p-3">
                                <table class="table table-sm">
                                    <thead>
                                    <tr>
                                        <th>{{$user->name}}</th>
                                        <th class="d-none d-md-table-cell">{{'Год - ' . $year}}</th>
                                        <th class="d-none d-md-table-cell">{{'Месяц - ' . $month}}</th>
                                        <th>{{$user_result_time_sheet->result}}</th>
                                        <th>{{$user_result_time_sheet->overtime}}</th>
                                    </tr>
                                    <tr>
                                        <td colspan="5"></td>
                                    </tr>
                                    <tr>
                                        <th>Число</th>
                                        <th class="d-none d-md-table-cell">Начало</th>
                                        <th class="d-none d-md-table-cell">Конец</th>
                                        <th>Всего, ч</th>
                                        <th>Сверх, ч</th>
                                    </tr>
                                    </thead>
                                    <tbody style="cursor: pointer;">
                                    @forelse($user_time_sheets as $user_time_sheet)
                                        @if(auth()->user()->can('read'))
                                            <tr style="background-color: {{$user_time_sheet->is_holiday ? 'rgb(255, 218, 182)' : 'rgb(203, 255, 182)' }};">
                                        @else
                                            <tr>
                                                @endif
                                                <td>{{date('d', strtotime($user_time_sheet->date))}}</td>
                                                <td class="d-none d-md-table-cell">{{date('H:i', strtotime($user_time_sheet->start))}}</td>
                                                <td class="d-none d-md-table-cell">{{date('H:i', strtotime($user_time_sheet->finish))}}</td>
                                                <td>{{date('H:i', strtotime($user_time_sheet->result))}}</td>
                                                <td class="{{date('H:i', strtotime($user_time_sheet->overtime)) != '00:00' ? 'text-danger fw-bold' : null}}">{{date('H:i', strtotime($user_time_sheet->overtime)) != '00:00' ? date('H:i', strtotime($user_time_sheet->overtime)) : null}}</td>
                                            </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">нет данных ...</td>
                                                </tr>
                                            @endforelse
                                    </tbody>
                                </table>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('user_time_sheets.index')}}"
                                       class="btn btn-outline-success rounded col-4 col-md-2 me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit'))
                                        <a href="{{route('user_time_sheets.edit', ['user' => $user->id, 'year' => $year, 'month' => $month])}}"
                                           class="btn btn-outline-warning rounded col-4 col-md-2 me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form
                                            action="{{route('user_time_sheets.destroy',  ['user' => $user->id, 'year' => $year, 'month' => $month])}}"
                                            method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn rounded btn-outline-danger"><img
                                                    src="{{asset('assets/images/backend/svg/trash3.svg')}}" alt="delete"
                                                    title="Удалить"></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- profile init js -->
            <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
