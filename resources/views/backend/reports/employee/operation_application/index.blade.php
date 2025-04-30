@extends('layouts.backend.main')

@section('title', 'Главная | Отчет по сотруднику')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Отчет по заявкам сотрудника</h4>
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
                                            <form action="{{route('employee_reports.operation_application.report')}}" method="post">
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
                                                            <span>Сотрудник:</span>
                                                            <input value="{{Auth::user()->name}}" required type="text"
                                                                   list="executors_list"
                                                                   class="form-control form-control-sm"
                                                                   placeholder="Начните писать ..."
                                                                   name="user">
                                                            <datalist id="executors_list">
                                                                @forelse($users as $user)
                                                                    <option value="{{$user->name}}">
                                                                @empty
                                                                    <option value="нет данных ...">
                                                                @endforelse
                                                            </datalist>
                                                        </td>
                                                        <td>
                                                            <span>Подразделение:</span>
                                                            <select required name="user_division_id" class="form-select form-select-sm" id="user_division_id">
                                                                @forelse($divisions as $division)
                                                                    <option value="{{$division->id}}" {{old('user_division_id', auth()->user()->user_division_id) == $division->id ? 'selected' : null}}>{{$division->name}}</option>
                                                                @empty
                                                                    <option value="">нет данных ...</option>
                                                                @endforelse
                                                            </select>
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
                                                    <select hidden required name="axis_x_type" class="form-select form-select-sm" id="user_division_id">
                                                        <option value="linear" {{old('axis_x_type') == 'linear' ? 'selected' : null}}>Обычная</option>
                                                        <option value="logarithmic" {{old('axis_x_type') == 'logarithmic' ? 'selected' : null}}>Логарифмическая</option>
                                                    </select>
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
