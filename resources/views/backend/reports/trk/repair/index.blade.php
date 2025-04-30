@extends('layouts.backend.main')

@section('title', 'Главная | Отчет по ремонту ТРК')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Отчет по ремонту ТРК</h4>
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
                                        <div class="col-12">
                                            <form action="{{route('trk_reports.repair.report')}}" method="post">
                                                @csrf
                                                @method('post')
                                                <div class="table-responsive">
                                            <table class="table table-striped table-hover shadow">
                                                <tbody style="cursor: pointer;">
                                                    <tr>
                                                        <td>
                                                            <span>Начало:</span>
                                                            <input required class="form-control form-control-sm"
                                                                   type="date"
                                                                   id="start_date" name="start_date"
                                                                    value="{{date('Y-m') . '-01'}}">
                                                        </td>
                                                        <td>
                                                            <span>Конец:</span>
                                                            <input required class="form-control form-control-sm"
                                                                   type="date" id="finish_date"
                                                                   name="finish_date"
                                                                   value="{{date('Y-m-d')}}">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <span>ТРК:</span>
                                                            <select required name="trk_id" class="form-select form-select-sm" id="trk_id">
                                                                @forelse($trks as $trk)
                                                                    <option value="{{$trk->id}}" {{old('trk_id') == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                                @empty
                                                                    <option value="">нет данных ...</option>
                                                                @endforelse
                                                            </select>
                                                        </td>
                                                        <td>
                                                        </td>
                                                    </tr>
                                                        <tr>
                                                            <td colspan="2">
                                                                <button class="btn btn-sm btn-outline-success" type="submit">Получить отчет</button>
                                                            </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                                </div>
                                            </form>
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
