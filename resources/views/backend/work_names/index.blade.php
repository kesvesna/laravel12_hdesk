@extends('layouts.backend.main')

@section('title', 'Главная | Названия работ')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Названия работ</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('work_names.create')}}"><img
                                    src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                    title="Добавить" height="30"></a>
                        @endif
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
                                    <table class="table table-striped table-hover shadow">
                                        <tbody style="cursor: pointer;">
                                        <tr>
                                            <form action="{{route('work_names.index')}}" method="get">
                                                @csrf
                                            <td>
                                                    <input type="search" class="form-control form-control-sm"
                                                           list="work_name_data_list" id="work_name"
                                                           placeholder="Поиск ..." onchange="this.form.submit();"
                                                           name="name" value="{{$old_filters['name'] ?? null}}">
                                                    <datalist id="work_name_data_list">
                                                        @forelse($all_work_names as $work_name)
                                                            <option value="{{$work_name->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                            </td>
                                            <td>
                                                    <select class="form-select form-select-sm" name="visibility" onchange="this.form.submit();">
                                                        <option value="">Все варианты</option>
                                                        <option value="1" {{isset($old_filters['visibility']) && $old_filters['visibility'] == 1 ? 'selected' : null}}>Показывается</option>
                                                        <option value="2" {{isset($old_filters['visibility']) && $old_filters['visibility'] == 2 ? 'selected' : null}}>Скрывается</option>
                                                    </select>
                                            </td>
                                        </form>
                                        </tr>
                                        @forelse($work_names as $work_name)
                                            @if(auth()->user()->can('read'))
                                                <tr style="background-color: {{$work_name->visibility == 1 ? '#edffee' : '#ffeded'}};" onclick="window.location='{{ route('work_names.show', $work_name->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>{!! str_replace(' ', '_', $work_name->name) !!}</td>
                                                    <td>{{$work_name->visibility == 1 ? 'Показывается' : 'Скрывается'}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td>нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$work_names->withQueryString()->links()}}
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
