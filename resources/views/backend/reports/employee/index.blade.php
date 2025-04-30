@extends('layouts.backend.main')

@section('title', 'Главная | Отчет по сотруднику')

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
                                        <div class="col-12">
                                            <form action="{{route('employee_reports.general_report.report')}}" method="post">
                                                @csrf
                                                @method('post')
                                                <div class="row row-cols-1 row-cols-md-2">
                                                    <div class="col mb-3">
                                                        <label class="form-label form-label-sm">Начало:</label>
                                                        <input required class="form-control form-control-sm"
                                                               type="date"
                                                               id="start_date" name="start_date"
                                                               value="{{old('start_date', date('Y-m') . '-01')}}">
                                                    </div>
                                                    <div class="col mb-3">
                                                        <label class="form-label form-label-sm">Конец:</label>
                                                        <input required class="form-control form-control-sm"
                                                               type="date" id="finish_date"
                                                               name="finish_date"
                                                               value="{{old('finish_date', date('Y-m-d'))}}">
                                                    </div>
                                                </div>
                                                <div class="row row-cols-1 row-cols-md-2">
                                                    <div class="col mb-3">
                                                        <label class="form-label form-label-sm">Сотрудник:</label>
                                                        <input required type="text"
                                                               list="executors_list"
                                                               class="form-control form-control-sm"
                                                               placeholder="Начните писать ..."
                                                               name="user" value="{{old('user', Auth::user()->name)}}">
                                                        <datalist id="executors_list">
                                                            @forelse($users as $user)
                                                                <option value="{{$user->name}}">
                                                            @empty
                                                                <option value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </div>
                                                    <div class="col mb-3">
                                                        <label class="form-label form-label-sm">Подразделение:</label>
                                                        <select required name="user_division_id" class="form-select form-select-sm" id="user_division_id">
                                                            @forelse($divisions as $division)
                                                                <option value="{{$division->id}}" {{old('user_division_id', auth()->user()->user_division_id) == $division->id ? 'selected' : null}}>{{$division->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </div>
                                                </div>
                                                    <div class="row row-cols-1 row-cols-md-2">
                                                        <div class="col mb-3">
                                                            <label class="form-label form-label-sm">ТРК:</label>
                                                            <select name="trk_id" class="form-select form-select-sm" id="trk_id">
                                                                <option value="">Все</option>
                                                                @forelse($trks as $trk)
                                                                    <option value="{{$trk->id}}" {{old('trk_id') == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                                @empty
                                                                    <option value="">нет данных ...</option>
                                                                @endforelse
                                                            </select>
                                                        </div>
                                                    <div class="col mb-3">
                                                        <label class="form-label form-label-sm">Тип оси Х:</label>
                                                        <select required name="axis_x_type" class="form-select form-select-sm" id="user_division_id">
                                                            <option value="linear" {{old('axis_x_type') == 'linear' ? 'selected' : null}}>Линейная</option>
                                                            <option value="logarithmic" {{old('axis_x_type') == 'logarithmic' ? 'selected' : null}}>Логарифм</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row mt-2">
                                                    <div class="col">
                                                        <button class="btn btn-sm btn-outline-success" type="submit">Получить отчет</button>
                                                    </div>
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
