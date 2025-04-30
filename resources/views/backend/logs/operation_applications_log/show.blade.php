@extends('layouts.backend.main')

@section('title', 'Просмотр | Лог заявки в эксплуатацию')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Лог заявки в эксплуатацию</h4>
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
                                    <li class="list-group-item"
                                        style="background-color:rgba(251, 255, 0, 0.05);">{{$operation_application->operation_application_id}}</li>
                                    <li class="list-group-item"
                                        style="background-color:rgba(251, 255, 0, 0.05);">{{$operation_application->comment}}</li>
                                    <li class="list-group-item"
                                        style="background-color:rgba(251, 255, 0, 0.05);">{{$operation_application->created_at}}</li>
                                    <li class="list-group-item"
                                        style="background-color:rgba(251, 255, 0, 0.05);">{{$operation_application->trk->name}}</li>
                                    <li class="list-group-item"
                                        style="background-color:rgba(251, 255, 0, 0.05);">{{$operation_application->division->name}}</li>
                                    <li class="list-group-item"
                                        style="background-color:rgba(251, 255, 0, 0.05);">{{$operation_application->trouble_description ?? 'не выбрано'}}</li>
                                    <li class="list-group-item"
                                        style="background-color:rgba(251, 255, 0, 0.05);">{{$operation_application->author->name}}</li>
                                </ul>
                                <ul class="list-group mb-3">
                                @if($operation_application->done_percents > 0)
                                    <li class="list-group-item" style="background-color:rgba(0, 255, 26, 0.05);"><b>Исполнено {{$operation_application->done_percents . '%'}}: </b>{{$operation_application->result_description ?? 'пока ничего'}}
                                    </li>
                                    @if(!empty($operation_application->done_at))
                                    <li class="list-group-item"
                                        style="background-color:rgba(0, 255, 26, 0.05);">{{$operation_application->done_at}}</li>
                                        @endif

                                    @endif
                                    <li class="list-group-item"
                                        style="background-color:rgba(0, 255, 26, 0.05);">{{$operation_application->done_author->name ?? 'null'}}</li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6 mt-3" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('operation_applications_log.index')}}"
                                       class="btn btn-outline-success rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit') || Auth::id() == $operation_application->author_id)
                                        <a href="{{route('operation_applications_log.edit', $operation_application)}}"
                                           class="btn btn-outline-warning rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form
                                            action="{{route('operation_applications_log.destroy', $operation_application)}}"
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
            </div>

@endsection
