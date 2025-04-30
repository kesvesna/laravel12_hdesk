@extends('layouts.backend.main')

@section('title', 'Просмотр | Баг')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Баг</h4>
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
                            <div class="card shadow p-3">
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <ul class="list-group mb-3">
                                    <li class="list-group-item">
                                        <b>Дата: </b>{{$user_bug_report->created_at ?? 'не выбрано'}}</li>
                                    <li class="list-group-item"><b>Описание
                                            бага: </b>{{$user_bug_report->trouble_description?? 'не выбрано'}}</li>
                                    <li class="list-group-item"><b>Что
                                            сделано: </b>{{$user_bug_report->result_description ?? 'пока ничего'}}</li>
                                    <li class="list-group-item">
                                        <hr>
                                    </li>
                                    <li class="list-group-item"><b>Создана: </b>{{$user_bug_report->created_at}}</li>
                                    <li class="list-group-item"><b>Автор: </b>{{$user_bug_report->author->name}}</li>
                                    <li class="list-group-item"><b>Исправлена: </b>{{$user_bug_report->updated_at}}</li>
                                    <li class="list-group-item"><b>Редактор: </b>{{$user_bug_report->last_editor->name}}
                                    </li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('user_bug_reports.index')}}"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit'))
                                        <a href="{{route('user_bug_reports.edit', $user_bug_report)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/check2-all.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('user_bug_reports.destroy', $user_bug_report)}}"
                                              method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-outline-danger"><img
                                                    src="{{asset('assets/images/backend/svg/trash3.svg')}}" alt="delete"
                                                    title="Удалить"></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- profile init js -->
            <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
