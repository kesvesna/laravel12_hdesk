@extends('layouts.backend.main')

@section('title', 'Главная | Отчеты по подразделению')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Отчет по подразделению на всех ТРК</h4>
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
                                            <h6 class="text-center">{{'С ' . $start_date . ' по ' . $finish_date . ', ' . $division->name}}</h6>
                                            <p class="text-center text-danger">Ось Х: {{$axis_x_type}}</p>
                                        </div>
                                    </div>
                                    @if(array_sum($division_report['avrs_count']) > 0 || array_sum($division_report['closed_applications_count']) > 0 || array_sum($division_report['closed_repairs_count']) > 0)
                                    <div class="row row-cols-1 row-cols-md-2">
                                        <div class="col-12 col-md-6 text-center mb-4">
                                            <div class="chart-container pe-4" style="height:75vh; width:80vw">
                                                <canvas id="totalReport"></canvas>
                                            </div>
                                        </div>
                                        <div class="col-12 col-md-6 text-center mb-4">
                                        </div>
                                    </div>
                                    @else
                                        <div class="row row-cols-1 mb-4 mt-3">
                                            <div class="col text-center">
                                                <h6>Нет АВР, заявок, ремонта ...</h6>
                                            </div>
                                        </div>
                                    @endif
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
        const start_date = @json($start_date);
        const finish_date = @json($finish_date);
        const avrs_count = @json($division_report['avrs_count']);
        const created_applications_count = @json($division_report['created_applications_count']);
        const closed_applications_count = @json($division_report['closed_applications_count']);
        const closed_repairs_count = @json($division_report['closed_repairs_count']);
        const period_works_count = @json($division_report['period_works_count']);
        const axis_x_type = @json($axis_x_type);

        const data = {
            labels: trks,
            datasets: [
                {
                    label: 'Заявки созданные',
                    data: created_applications_count,
                    borderColor: 'rgb(255, 0, 0)',
                    backgroundColor: 'rgba(255, 0, 0, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Заявки закрытые',
                    data: closed_applications_count,
                    borderColor: 'rgb(0, 255, 26)',
                    backgroundColor: 'rgba(0, 255, 26, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Ремонт',
                    data: closed_repairs_count,
                    borderColor: 'rgb(4, 0, 255)',
                    backgroundColor: 'rgba(4, 0, 255, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'АВР',
                    data: avrs_count,
                    borderColor: 'rgb(253, 245, 0)',
                    backgroundColor: 'rgba(253, 245, 0, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Тех.мероприятия',
                    data: period_works_count,
                    borderColor: 'rgb(255, 166, 246)',
                    backgroundColor: 'rgba(255, 166, 246, 0.3)',
                    borderWidth: 1,
                }
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
