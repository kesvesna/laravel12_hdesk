@extends('layouts.backend.main')

@section('title', 'Просмотр | Заявка в эксплуатацию')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Заявка в эксплуатацию</h4>
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
                                        @if(count($operation_application->executors) > 0)
                                            <li class="list-group-item" style="background-color:rgba(0, 255, 26, 0.05);"><b>Исполнители: </b>
                                                <ul class="list-group mt-1" style="background-color:rgba(0, 255, 26, 0.02);">
                                                    @forelse($operation_application->executors as $executor)
                                                        <li class="list-group-item" style="background-color: transparent;">{{$executor->name}}</li>
                                                    @empty
                                                        <li class="list-group-item">нет данных ...</li>
                                                    @endforelse
                                                </ul>
                                            </li>
                                        @endif
                                    @if(!empty($operation_application->done_at))
                                    <li class="list-group-item"
                                        style="background-color:rgba(0, 255, 26, 0.05);">{{$operation_application->done_at}}</li>
                                        @endif

                                    @endif
                                </ul>
                                <div class="accordion accordion-flush" id="accordionFlushExample">
                                    @if(count($operation_application->tech_acts) > 0)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingOne">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                                Технические акты
                                            </button>
                                        </h2>
                                        <div id="flush-collapseOne" class="accordion-collapse collapse" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <ul class="list-group">
                                                    @forelse($operation_application->tech_acts as $tech_act)
                                                        <li class="list-group-item"><a href="{{route('tech_acts.show', $tech_act->id)}}">{{$tech_act->created_at}}</a></li>
                                                    @empty
                                                        <li class="list-group-item">нет данных ...</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                    @if(count($operation_application->avrs) > 0)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                                Акты выполненных работ
                                            </button>
                                        </h2>
                                        <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <ul class="list-group">
                                                    @forelse($operation_application->avrs as $avr)
                                                        <li class="list-group-item"><a href="{{route('avrs.show', $avr->id)}}">{{$avr->date}}</a></li>
                                                    @empty
                                                        <li class="list-group-item">нет данных ...</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                        @endif
                                        @if(count($operation_application->repairs) > 0)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                                Ремонт
                                            </button>
                                        </h2>
                                        <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <ul class="list-group">
                                                    @forelse($operation_application->repairs as $repair)
                                                        <li class="list-group-item"><a href="{{route('trk_repairs.show', $repair->id)}}">{{$repair->created_at}}</a></li>
                                                    @empty
                                                        <li class="list-group-item">нет данных ...</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                        @endif
                                        @if(count($operation_application->logs) > 1)
                                        <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingThree2">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree2" aria-expanded="false" aria-controls="flush-collapseThree2">
                                                История
                                            </button>
                                        </h2>
                                        <div id="flush-collapseThree2" class="accordion-collapse collapse" aria-labelledby="flush-headingThree2" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <ul class="list-group">
                                                    @forelse($operation_application->logs as $log)
                                                        <li class="list-group-item">{{date('d-m-Y H:i', strtotime($log->updated_at)) . ', '}}{{$log->done_percents . '%, '}}{{$log->division->name . ', '}}{{$log->result_description . ', '}}{{$log->last_editor->name}}</li>
                                                    @empty
                                                        <li class="list-group-item">нет данных ...</li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                            @endif
                                    </div>
                                <div class="btn-group btn-group-sm col-12 col-md-6 mt-3" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back();"
                                       class="btn btn-outline-success rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('operation_application done_progress_update') || Auth::id() == $operation_application->author_id)
                                    @if($operation_application->division_id == auth()->user()->user_division_id)
                                        @if($operation_application->done_percents < 100)
                                        <a href="{{route('operation_applications.done_progress', $operation_application)}}"
                                           class="btn btn-outline-success rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/check2-all.svg')}}"
                                                alt="done_progress" title="Выполнение"></a>
                                        @endif
                                    @endif
                                    @endif
                                    @if(auth()->user()->can('operation_application update') || Auth::id() == $operation_application->author_id)
                                        <a href="{{route('operation_applications.edit', $operation_application)}}"
                                           class="btn btn-outline-warning rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form
                                            action="{{route('operation_applications.destroy', $operation_application)}}"
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
