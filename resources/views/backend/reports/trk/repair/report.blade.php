@extends('layouts.backend.main')

@section('title', 'Главная | Отчет ТРК по ремонту')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Отчет ТРК по ремонту</h4>
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
                                            array_sum($trk_report['closed_repairs_count']) > 0
                                          || array_sum($trk_report['new_repairs_count']) > 0
                                          || array_sum($trk_report['in_progress_repairs_count']) > 0
                                        )
                                        <div class="row row-cols-1 row-cols-md-2">
                                            <div class="col-12 col-md-6 text-center mb-4">
                                                <h6 class="text-center">{{'Отчет по ремонту'}}</h6>
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
                                                <h6>Нет ремонта ...</h6>
                                            </div>
                                        </div>
                                    @endif
                                    @if(count($trk_report['motionless_repairs']) > 0)
                                        <div class="table-responsive">
                                            <h6>Ремонт без движения {{count($trk_report['motionless_repairs']) . 'шт.'}}</h6>
                                            <table class="table table-sm table-striped table-bordered table-hover">
                                                <thead>
                                                <tr>
                                                    <th>Создан</th>
                                                    <th>Последнее движение</th>
                                                    <th>Выполнено</th>
                                                    <th>Помещение</th>
                                                    <th>Оборудование</th>
                                                    <th>Подразделение</th>
                                                    <th>Задача</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($trk_report['motionless_repairs'] as $key => $value)
                                                    <tr style="cursor: pointer;" onclick="window.location='{{route('trk_repairs.show', $value->id)}}'">
                                                        <td class="text-nowrap">{{$value->created_at}}</td>
                                                        <td class="text-nowrap">{{$value->updated_at}}</td>
                                                        <td>{{$value->done_progress . '%'}}</td>
                                                        <td class="text-nowrap">{{$value->trk_room->room->name}}</td>
                                                        <td class="text-nowrap">{{$value->trk_equipment->equipment_name->name}}</td>
                                                        <td>{{$value->user_division->name}}</td>
                                                        <td>{{$value->description}}</td>
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
        const closed_repairs_count = @json($trk_report['closed_repairs_count']);
        const new_repairs_count = @json($trk_report['new_repairs_count']);
        const in_progress_repairs_count = @json($trk_report['in_progress_repairs_count']);
        const motionless_repairs_count = @json($trk_report['motionless_repairs_count']);

        const data = {
            labels: divisions,
            datasets: [
                {
                    label: 'Новый',
                    data: new_repairs_count,
                    borderColor: 'rgb(252, 124, 192)',
                    backgroundColor: 'rgba(252, 124, 192, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Выполняется',
                    data: in_progress_repairs_count,
                    borderColor: 'rgb(253, 245, 0)',
                    backgroundColor: 'rgba(253, 245, 0, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Закрытый',
                    data: closed_repairs_count,
                    borderColor: 'rgb(0, 252, 23)',
                    backgroundColor: 'rgba(0, 252, 23, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Без движения',
                    data: motionless_repairs_count,
                    borderColor: 'rgb(250, 0, 0)',
                    backgroundColor: 'rgba(250, 0, 0, 0.3)',
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
