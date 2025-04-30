@extends('layouts.backend.main')

@section('title', 'Главная | Отчет по сотрудникам')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Отчет по сотрудникам</h4>
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
                                            <form action="{{route('division_reports.employees.report')}}" method="post">
                                                @csrf
                                                @method('post')
                                                <div class="table-responsive">
                                            <table class="table table-striped table-hover">
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
                                                            <span>Тип оси Х:</span>
                                                            <select required name="axis_x_type" class="form-select form-select-sm" id="user_division_id">
                                                                <option value="linear">Обычная</option>
                                                                <option value="logarithmic">Логарифмическая</option>
                                                            </select>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                                    @if(count($executors) > 0)
                                                        <div class="executors-add-parent-div">
                                                            <label class="form-label form-label-sm">Исполнители <span
                                                                    class="text-danger"><b>*</b></span></label>
                                                            @foreach($executors as $executor)
                                                                <div class="executor-add-div">
                                                                    <div class="row row-cols-1">
                                                                        <div class="col-12 col-md-4">
                                                                            <div class="input-group input-group-sm mb-1">
                                                        <span class="input-group-text executor-add-button"><img
                                                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                alt="add" title="Добавить" height="20"></span>
                                                                                <span class="input-group-text executor-delete-button"><img
                                                                                        src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                                        alt="delete" title="Удалить" height="20"></span>
                                                                                <input value="{{$executor->name}}" required type="text"
                                                                                       list="executors_list"
                                                                                       class="form-control form-control-sm"
                                                                                       placeholder="Начните писать ..."
                                                                                       name="executors[]">
                                                                                <datalist id="executors_list">
                                                                                    @forelse($users as $user)
                                                                                        <option data-equipment_key="{{$user->id}}"
                                                                                                value="{{$user->name}}">
                                                                                    @empty
                                                                                        <option data-equipment_key="" value="нет данных ...">
                                                                                    @endforelse
                                                                                </datalist>
                                                                            </div>
                                                                            @error('executors.*')
                                                                            <div class="text-danger"
                                                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @else
                                                        <div class="executors-add-parent-div">
                                                            <label class="form-label form-label-sm">Исполнители <span
                                                                    class="text-danger"><b>*</b></span></label>
                                                            <div class="executor-add-div">
                                                                <div class="row row-cols-1">
                                                                    <div class="col-12 col-md-4">
                                                                        <div class="input-group input-group-sm mb-1">
                                                        <span class="input-group-text executor-add-button"><img
                                                                src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                alt="add" title="Добавить" height="20"></span>
                                                                            <span class="input-group-text executor-delete-button"><img
                                                                                    src="{{asset('assets/images/backend/svg/trash3.svg')}}"
                                                                                    alt="delete" title="Удалить" height="20"></span>
                                                                            <input value="{{auth()->user()->name}}" required type="text"
                                                                                   list="executors_list"
                                                                                   class="form-control form-control-sm"
                                                                                   placeholder="Начните писать ..."
                                                                                   name="executors[]">
                                                                            <datalist id="executors_list">
                                                                                @forelse($users as $user)
                                                                                    <option data-equipment_key="{{$user->id}}"
                                                                                            value="{{$user->name}}">
                                                                                @empty
                                                                                    <option data-equipment_key="" value="нет данных ...">
                                                                                @endforelse
                                                                            </datalist>
                                                                        </div>
                                                                        @error('executors.*')
                                                                        <div class="text-danger"
                                                                             style="margin-top: -1rem !important;">{{$message}}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    <button class="btn btn-sm btn-outline-success mt-3 mb-3" type="submit">Получить отчет</button>
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
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
        <script src="{{asset('assets/js/reports/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/reports/delete_executor.js')}}"></script>
@endsection
