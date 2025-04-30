@extends('layouts.backend.main')

@section('title', 'Главная | Цена за килограмм')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Цена за килограмм</h4>
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
                                    <table class="table table-striped table-hover shadow table-sm">
                                        <thead>
                                        <tr>
                                            <th>Цена, руб.</th>
                                            <th>Вес, грамм.</th>
                                            <th>Цена за килограмм</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-price" placeholder="Цена ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-weight" placeholder="Вес в граммах ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" readonly class="form-control form-control-sm one-kilo-price" value="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-price" placeholder="Цена ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-weight" placeholder="Вес в граммах ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" readonly class="form-control form-control-sm one-kilo-price" value="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-price" placeholder="Цена ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-weight" placeholder="Вес в граммах ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" readonly class="form-control form-control-sm one-kilo-price" value="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-price" placeholder="Цена ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-weight" placeholder="Вес в граммах ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" readonly class="form-control form-control-sm one-kilo-price" value="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-price" placeholder="Цена ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-weight" placeholder="Вес в граммах ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" readonly class="form-control form-control-sm one-kilo-price" value="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-price" placeholder="Цена ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-weight" placeholder="Вес в граммах ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" readonly class="form-control form-control-sm one-kilo-price" value="">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-price" placeholder="Цена ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control form-control-sm total-weight" placeholder="Вес в граммах ..." value="">
                                                </td>
                                                <td>
                                                    <input type="number" readonly class="form-control form-control-sm one-kilo-price" value="">
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>
        </div>
    <script src="{{asset('assets/js/jquery.min.js')}}"></script>
    <script>
        $(document).ready(function(){

            $(".total-price").keyup(function(){
                let price = $(this).val();
                let weight = $(this).closest('tr').find('.total-weight').val();
                let one_kilo_price = (price / weight * 1000).toFixed(2);
                $(this).closest('tr').find('.one-kilo-price').attr('value', one_kilo_price);

                let all_prices = $(".one-kilo-price").map(function() {

                    if(this.value != 0.00)
                    {
                        return this.value;
                    }

                }).get();

                const max_price = Math.max.apply(Math,all_prices);
                const min_price = Math.min.apply(Math,all_prices);

                $(".one-kilo-price").map(function() {

                    $(this).removeClass().addClass('form-control form-control-sm one-kilo-price');

                    if(max_price == this.value)
                   {
                       $(this).removeClass().addClass('form-control form-control-sm one-kilo-price text-danger fw-bold');
                   }

                    if(this.value != 0.00 && min_price >= this.value)
                    {
                        $(this).removeClass().addClass('form-control form-control-sm one-kilo-price text-success fw-bold');
                    }

                });
            });

            $(".total-weight").keyup(function(){
                let weight = $(this).val();
                let price = $(this).closest('tr').find('.total-price').val();
                let one_kilo_price = (price / weight * 1000).toFixed(2);
                $(this).closest('tr').find('.one-kilo-price').attr('value', one_kilo_price);
            });

        });

    </script>
@endsection
