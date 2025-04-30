@extends('layouts.backend.main')

@section('title', 'Главная | Отчет ТРК по заявкам')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Отчет ТРК по заявкам</h4>
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
                                            <h6 class="text-center">{{'С ' . $start_date . ' по ' . $finish_date . ', ' .  $trk->name}}</h6>
                                        </div>
                                    </div>
                                    @if(
                                            array_sum($trk_report['closed_applications_count']) > 0
                                          || array_sum($trk_report['created_applications_count']) > 0
                                          || array_sum($trk_report['new_applications_count']) > 0
                                          || array_sum($trk_report['in_progress_applications_count']) > 0
                                        )
                                        <div class="row row-cols-1 row-cols-md-2">
                                            <div class="col-12 col-md-6 text-center mb-4">
                                                <h6 class="text-center">{{'Отчет по заявкам'}}</h6>
                                                <div class="chart-container pe-4" style="height:45vh; width:80vw">
                                                    <canvas id="totalReport"></canvas>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-6 text-center mb-4">
                                          </div>
                                        </div>
                                    @else
                                        <div class="row row-cols-1 mb-4 mt-3">
                                            <div class="col text-center">
                                                <h6>Нет заявок ...</h6>
                                            </div>
                                        </div>
                                    @endif
                                    @if(count($trk_report['motionless_applications']) > 0)
                                        <div class="table-responsive">
                                            <h6>Заявки без движения {{count($trk_report['motionless_applications']) . 'шт.'}}</h6>
                                            <table class="table table-sm table-striped table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th>Создана</th>
                                                    <th>Последнее движение</th>
                                                    <th>Выполнено</th>
                                                    <th>Подразделение</th>
                                                    <th>Проблема</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($trk_report['motionless_applications'] as $key => $value)
                                                    <tr style="cursor: pointer;" onclick="window.location='{{route('operation_applications.show', $value->id)}}'">
                                                        <td class="text-nowrap">{{$value->created_at}}</td>
                                                        <td class="text-nowrap">{{$value->updated_at}}</td>
                                                        <td>{{$value->done_percents . '%'}}</td>
                                                        <td>{{$value->division->name}}</td>
                                                        <td>{{$value->trouble_description}}</td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
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

        const divisions = @json($division_names);
        const start_date = @json($start_date);
        const finish_date = @json($finish_date);
        const closed_applications_count = @json($trk_report['closed_applications_count']);
        const new_applications_count = @json($trk_report['new_applications_count']);
        const created_applications_count = @json($trk_report['created_applications_count']);
        const in_progress_applications_count = @json($trk_report['in_progress_applications_count']);
        const motionless_applications_count = @json($trk_report['motionless_applications_count']);

        const data = {
            labels: divisions,
            datasets: [
                {
                    label: 'Создано',
                    data: created_applications_count,
                    borderColor: 'rgb(255, 0, 0)',
                    backgroundColor: 'rgba(255, 0, 0, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'В ожидании',
                    data: new_applications_count,
                    borderColor: 'rgb(255, 213, 0)',
                    backgroundColor: 'rgba(255, 213, 0)',
                    borderWidth: 1,
                },
                {
                    label: 'Выполняются',
                    data: in_progress_applications_count,
                    borderColor: 'rgb(251, 255, 0)',
                    backgroundColor: 'rgba(251, 255, 0, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Закрытые',
                    data: closed_applications_count,
                    borderColor: 'rgb(0, 252, 23)',
                    backgroundColor: 'rgba(0, 252, 23, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Без движения',
                    data: motionless_applications_count,
                    borderColor: 'rgb(51, 0, 255)',
                    backgroundColor: 'rgba(51, 0, 255, 0.3)',
                    borderWidth: 1,
                },
                // {
                //     label: 'ТО',
                //     data: period_works_count,
                //     borderColor: 'rgb(255, 166, 246)',
                //     backgroundColor: 'rgba(255, 166, 246, 0.3)',
                //     borderWidth: 1,
                // }
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
