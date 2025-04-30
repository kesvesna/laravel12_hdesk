@extends('layouts.backend.main')

@section('title', 'Главная | Отчеты по сотрудникам')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Отчет по сотруднику общий</h4>
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
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <h6 class="text-center">{{'С ' . $data['start_date'] . ' по ' . $data['finish_date'] . ', ' . $user->division->name . ', ' . $user->name}}</h6>
                                        </div>
                                    </div>
                                    <form action="{{route('employee_reports.general_report.report')}}" method="post">
                                        @csrf
                                        @method('post')
                                        <div class="row row-cols-1 row-cols-md-4">
                                            <div class="col-1 col-md-4">
                                            </div>
                                                <div class="col-12 col-md-2 mb-3">
                                                        <input hidden name="start_date" value="{{$data['start_date']}}">
                                                        <input hidden name="finish_date" value="{{$data['finish_date']}}">
                                                        <input hidden name="user" value="{{$data['user']}}">
                                                        <input hidden name="user_division_id" value="{{$data['user_division_id']}}">
                                                        <select onchange="this.form.submit();" name="trk_id" class="form-select form-select-sm" id="trk_id">
                                                            <option value="">Все ТРК</option>
                                                            @forelse($trks as $trk)
                                                                <option value="{{$trk->id}}" {{$data['trk_id'] == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                </div>
                                        <div class="col-12 col-md-2 mb-3">
                                                        <select  onchange="this.form.submit();" required name="axis_x_type" class="form-select form-select-sm" id="user_division_id">
                                                        <option value="linear" {{$data['axis_x_type'] == 'linear' ? 'selected' : null}}>Ось Х линейная</option>
                                                        <option value="logarithmic" {{$data['axis_x_type'] == 'logarithmic' ? 'selected' : null}}>Ось Х логарифм</option>
                                                    </select>
                                                </div>
                                        <div class="col-1 col-md-4">
                                        </div>
                                    </div>
                                    </form>
                                    <div class="row row-cols-1 row-cols-md-2 mb-4 mt-2">
                                        <div class="col-12 col-md-6 text-center mb-4">
                                            <div class="chart-container pe-4" style="height:85vh; width:80vw">
                                                <canvas id="totalReport"></canvas>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <a href="javascript:history.back()"
                                               class="btn btn-sm btn-outline-success col-4 col-md-2 rounded me-2"><img
                                                    src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                                    title="Назад"></a>
{{--                                            <form action="" method="post">--}}
{{--                                                @csrf--}}
{{--                                                @method('post')--}}
{{--                                                <button type="submit" class="btn btn-sm btn-outline-success col-4 col-md-2"><img--}}
{{--                                                        src="{{asset('assets/images/backend/svg/printer.svg')}}" alt="delete"--}}
{{--                                                        title="На печать"></button>--}}
{{--                                            </form>--}}
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
        <script src="{{asset('assets/js/chart.js')}}"></script>
    <script>

        const trks = @json($trk_names);
        const start_date = @json($data['start_date']);
        const finish_date = @json($data['finish_date']);
        const avrs_count = @json($user_report['avrs_count']);
        const closed_applications_count = @json($user_report['closed_applications_count']);
        const closed_repairs_count = @json($user_report['closed_repairs_count']);
        const air_condition_checklists_count = @json($user_report['air_condition_checklists_count']);
        const air_recycle_checklists_count = @json($user_report['air_recycle_checklists_count']);
        const period_works_count = @json($user_report['period_works_count']);
        const counter_counts = @json($user_report['counter_counts']);
        const axis_x_type = @json($data['axis_x_type']);

        const data = {
            labels: trks,
            datasets: [
                {
                    label: 'АВР',
                    data: avrs_count,
                    borderColor: 'rgb(255, 3, 3)',
                    backgroundColor: 'rgba(255, 3, 3, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Заявки закрытые',
                    data: closed_applications_count,
                    borderColor: 'rgb(255, 108, 3)',
                    backgroundColor: 'rgba(255, 108, 3, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Ремонт',
                    data: closed_repairs_count,
                    borderColor: 'rgb(251, 255, 3)',
                    backgroundColor: 'rgba(251, 255, 3, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Чек листы кондиционеров',
                    data: air_condition_checklists_count,
                    borderColor: 'rgb(24, 255, 3)',
                    backgroundColor: 'rgba(24, 255, 3, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Чек листы вентиляции',
                    data: air_recycle_checklists_count,
                    borderColor: 'rgb(3, 209, 255)',
                    backgroundColor: 'rgba(3, 209, 255, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Тех.мероприятия',
                    data: period_works_count,
                    borderColor: 'rgb(24, 3, 255)',
                    backgroundColor: 'rgba(24, 3, 255, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Показания счетчиков',
                    data: counter_counts,
                    borderColor: 'rgb(255, 3, 230)',
                    backgroundColor: 'rgba(255, 3, 230, 0.3)',
                    borderWidth: 1,
                },
            ]
        };

        let totalReport = document.getElementById('totalReport');

        new Chart(totalReport, {
            type: 'bar',
            data: data,
            options: {
                maintainAspectRatio: false,
                indexAxis: 'y',
                elements: {
                    bar: {
                        borderWidth: 2,
                    }
                },
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false,
                        text: 'Chart.js Horizontal Bar Chart'
                    }
                },
                scales: {
                    x: {
                        display: true,
                        type: axis_x_type,
                    },
                    y: {
                        display: true,
                    }
                }
                // scales: {
                //     x: {
                //         ticks: {
                //             font: {
                //                 size: 16,
                //             }
                //         }
                //     },
                //     y: {
                //         ticks: {
                //             font: {
                //                 size: 16,
                //             }
                //         }
                //     }
                // }
            },
        });

    </script>
@endsection
