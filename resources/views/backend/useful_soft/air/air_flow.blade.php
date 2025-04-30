@extends('layouts.backend.main')

@section('title', 'Главная | Расход воздуха')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Расход воздуха</h4>
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
                            <div class="card shadow">
                                <div class="card-body">
                                    <button type="button" class="btn btn-light btn-sm mb-2" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal2">Инфо
                                    </button>
                                    <div style="background-color: #ffe0fc;" class="p-3 rounded mb-2">
                                        <h6><b>Вводные данные:</b></h6>
                                   <div class="row row-cols-1 row-cols-md-3">
                                       <div class="col mb-3">
                                           <label class="form-label form-label-sm">
                                               Длина, мм.
                                           </label>
                                           <input class="form-control form-control-sm"
                                                  id="length" type="number" placeholder="250">
                                       </div>
                                       <div class="col mb-3">
                                           <label class="form-label form-label-sm">
                                               Ширина, мм.
                                           </label>
                                           <input class="form-control form-control-sm"
                                                  id="width" type="number">
                                       </div>
                                       <div class="col mb-3">
                                           <label class="form-label form-label-sm">
                                               Скорость воздуха, м/сек.
                                           </label>
                                           <input class="form-control form-control-sm"
                                                  id="air_speed" type="number" step="0.1" placeholder="3.5">
                                       </div>
                                   </div>
                                    </div>
                                    <div style="background-color: #ccfcce;" class="p-3 rounded">
                                    <h6><b>Результат:</b></h6>
                                    <div class="row row-cols-1 row-cols-md-3">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm">
                                                Площадь поперечного сечения, кв.м.
                                            </label>
                                            <input readonly class="form-control form-control-sm"
                                                   id="duct_square" type="number">
                                        </div>
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm">
                                                Расход воздуха, куб.м. в час
                                            </label>
                                            <input readonly class="form-control form-control-sm"
                                                   id="air_flow" type="number">
                                        </div>
                                        <div class="col">
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
        </div>
    <!-- Modal info -->
    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel2">Заполнение полей</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><b>Длина</b> - если воздуховод круглый, то это будет диаметр воздуховода</p>
                    <p><b>Ширина</b> - заполняется только для прямоугольных воздуховодов, для круглых оставьте пустым</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('assets/js/jquery.min.js')}}"></script>
    <script>
        $(document).ready(function(){

            $("#air_speed").keyup(function(){

                const air_speed = $(this).val();
                const length = $('#length').val();
                const width = $('#width').val();

                let duct_square = null;
                let air_flow = null;

                if(width > 0)
                {

                    duct_square = length * width * 0.000001;

                } else {

                    let r = length / 2;
                    duct_square = r * r * 3.1415926535 * 0.000001;
                }

                air_flow = air_speed * duct_square * 3600;

                duct_square = (duct_square).toFixed(6);
                air_flow = (air_flow).toFixed(1);

                $('#duct_square').attr('value', duct_square);
                $('#air_flow').attr('value', air_flow);

            });

        });

    </script>
@endsection
