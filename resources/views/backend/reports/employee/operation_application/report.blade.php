@extends('layouts.backend.main')

@section('title', 'Главная | Отчет сотрудника по заявкам')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Отчет сотрудника по заявкам</h4>
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
                                            <h6 class="text-center">{{'С ' . $start_date . ' по ' . $finish_date . ', ' . $user->division->name . ', ' . $user->name}}</h6>
                                        </div>
                                    </div>
                                    @if(count($user_report['closed_operation_applications']) > 0 || count($user_report['created_applications_by_this_user']) > 0)
                                    <div class="row row-cols-1 mb-4 mt-3">
                                        <div class="col">
                                                <canvas style="height: 100%; width: 100%;" id="totalOperationApplicationReport"></canvas>
                                        </div>
                                    </div>
                                        @if($user_report['closed_application_without_acts_count'] > 0)
                                        <div class="row row-cols-1 mb-4 mt-5">
                                            <div class="col">
                                                <h6>Закрытые заявки без актов: {{$user_report['closed_application_without_acts_count'] . 'шт.'}}</h6>
                                                <div class="table-responsive mt-3">
                                                <table class="table table-responsive table-striped table-hover">
                                                   <thead>
                                                        <tr>
                                                            <td>Создана</td>
                                                            <td>Закрыта</td>
                                                            <td>ТРК</td>
                                                            <td>Проблема</td>
                                                            <td>Результат</td>
                                                        </tr>
                                                   </thead>
                                                    <tbody>
                                                        @foreach($user_report['closed_applications_without_acts'] as $app)
                                                            @if(isset($app->operation_application->avrs) && count($app->operation_application->avrs) == 0)
                                                                <tr style="cursor: pointer;" onclick="window.location='{{ route('operation_applications.show', $app->operation_application->id) }}';">
                                                                <td>{{$app->operation_application->created_at}}</td>
                                                                <td>{{$app->operation_application->done_at}}</td>
                                                                <td>{{$app->operation_application->trk->name}}</td>
                                                                <td>{{$app->operation_application->trouble_description}}</td>
                                                                <td>{{$app->operation_application->result_description}}</td>
                                                            </tr>
                                                            @endif
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                                </div>
                                            </div>
                                        </div>
                                        @endif
                                    @else
                                        <div class="row row-cols-1 mb-4 mt-3">
                                            <div class="col text-center">
                                                <h6>Нет закрытых заявок ...</h6>
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

        const in_process_applications = @json($user_report['in_process_operation_applications']);
        const closed_applications = @json($user_report['closed_operation_applications']);
        const created_applications_by_this_user = @json($user_report['created_applications_by_this_user']);


        const data = {
            labels: @json($user_report['trks']),
            datasets: [
                // {
                //     label: 'В обработке',
                //     data: in_process_applications,
                //     borderColor: '#FF0000',
                //     backgroundColor: '#FFC5C5',
                //     borderWidth: 1,
                // },
                {
                    label: 'Создал',
                    data: created_applications_by_this_user,
                    borderColor: 'rgb(253, 245, 0)',
                    backgroundColor: 'rgba(253, 245, 0, 0.3)',
                    borderWidth: 1,
                },
                {
                    label: 'Закрыл',
                    data: closed_applications,
                    borderColor: 'rgb(0, 252, 23)',
                    backgroundColor: 'rgba(0, 252, 23, 0.3)',
                    borderWidth: 1,
                }
            ]
        };

        let totalReport = document.getElementById('totalOperationApplicationReport');

        new Chart(totalReport, {
            type: 'bar',
            data: data,
            options: {
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
