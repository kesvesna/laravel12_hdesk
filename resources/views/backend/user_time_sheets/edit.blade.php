@extends('layouts.backend.main')

@section('title', 'Главная | Табель редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Табель редактирование</h4>
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
                            <form
                                action="{{route('user_time_sheets.update', ['user' => $user->id, 'year' => $year, 'month' => $month])}}"
                                method="post">
                                @csrf
                                @method('patch')
                                <input hidden name="user_result_time_sheet_id" value="{{$user_result_time_sheet->id}}">
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                        <tr>
                                            <th>Число</th>
                                            <th>Начало</th>
                                            <th>Конец</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($user_time_sheets as $user_time_sheet)
                                            <tr style="background-color: {{$user_time_sheet->is_holiday ? 'rgb(255, 218, 182)' : 'rgb(203, 255, 182)' }};">
                                                <td><input readonly class="form-control form-control-sm fw-bold"
                                                           name="user_time_sheets[{{$user_time_sheet->id}}][date]"
                                                           value="{{$user_time_sheet->date}}"></td>
                                                <td><input class="form-control form-control-sm fw-bold"
                                                           name="user_time_sheets[{{$user_time_sheet->id}}][start]"
                                                           value="{{date('H:i', strtotime($user_time_sheet->start))}}">
                                                </td>
                                                <td><input class="form-control form-control-sm fw-bold"
                                                           name="user_time_sheets[{{$user_time_sheet->id}}][finish]"
                                                           value="{{date('H:i', strtotime($user_time_sheet->finish))}}">
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3"></td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('user_time_sheets.show',  ['user' => $user->id, 'year' => $year, 'month' => $month])}}"
                                           class="btn btn-sm btn-outline-success col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                alt="back" title="Назад"></a>
                                        <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                title="Сохранить"></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
