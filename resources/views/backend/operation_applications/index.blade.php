@extends('layouts.backend.main')

@section('title', 'Главная | Заявки в эксплуатацию')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-0 me-sm-3">Заявки в эксплуатацию</h4>
                        @if(auth()->user()->can('operation_application create'))
                            <a href="{{route('operation_applications.create')}}"><img
                                    src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                    title="Создать через клавиатуру" height="30"></a>
                            <a href="{{route('operation_applications.create_by_microphone')}}" class="ms-1 ms-sm-3"><img
                                    src="{{asset('assets/images/backend/svg/microphone.svg')}}" alt="Add"
                                    title="Создать через микрофон" height="30"></a>
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
                                <div class="card-title ps-3 pt-3">
                                    <div class="row row-cols-1">
                                        <div class="col">
                                            <button type="button" class="btn btn-outline-success btn-sm"
                                                    data-bs-toggle="modal" data-bs-target="#operation_applications">Выгрузка заявок
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-hover shadow">
                                        <thead>
                                        <tr>
                                            <th>Создана</th>
                                            <th>Выполнение</th>
                                            @if($show_trk_column)
                                                <th>ТРК</th>
                                            @endif
                                            <th>Подразделение</th>
                                            <th>Проблема</th>
                                            <th>Выполнено, %</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('operation_applications.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td>
                                                    <input class="form-control form-control-sm"
                                                           onchange="this.form.submit();" type="date"
                                                           id="from_created_at" name="from_created_at"
                                                           value="{{$old_filters['from_created_at'] ?? null}}">
                                                    <input class="form-control form-control-sm"
                                                           onchange="this.form.submit();" type="date" id="to_created_at"
                                                           name="to_created_at"
                                                           value="{{$old_filters['to_created_at'] ?? null}}">
                                                </td>
                                                <td></td>
                                                @if($show_trk_column)
                                                <td>
                                                    <select class="form-select form-select-sm" name="trk_id" id="trk_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_trks as $trk)
                                                            <option
                                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                @endif
                                                <td>
                                                    <select class="form-select form-select-sm" name="division_id"
                                                            id="division_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_divisions as $division)
                                                            <option
                                                                value="{{$division->id}}" {{isset($old_filters['division_id']) && $old_filters['division_id'] === $division->id ? 'selected' : null}}>{{$division->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" type="search"
                                                           id="trouble_description" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="trouble_description"
                                                           value="{{$old_filters['trouble_description'] ?? null}}">
                                                </td>
                                                <td>
                                                    <select name="operation_application_status" class="form-select form-select-sm"
                                                            id="operation_application_status" onchange="this.form.submit();">
                                                        <option value="" {{isset($old_filters['operation_application_status']) && $old_filters['operation_application_status'] == '' ? 'selected' : null}}>Все</option>
                                                        <option value="new" {{isset($old_filters['operation_application_status']) && $old_filters['operation_application_status'] == 'new' ? 'selected' : null}}>Новые</option>
                                                        <option value="in_progress" {{isset($old_filters['operation_application_status']) && $old_filters['operation_application_status'] == 'in_progress' ? 'selected' : null}}>Выполняются</option>
                                                        <option value="closed" {{isset($old_filters['operation_application_status']) && $old_filters['operation_application_status'] == 'closed' ? 'selected' : null}}>Выполнены</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </form>
                                        @forelse($operation_applications as $operation_application)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('operation_applications.show', $operation_application->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td class="text-nowrap">{{date('d-m-Y H:i', strtotime($operation_application->created_at))}}</td>
                                                    <td class="text-nowrap">{{$operation_application->done_at != '' ? date('d-m-Y H:i', strtotime($operation_application->done_at)) : null}}</td>
                                                    @if($show_trk_column)
                                                        <td>{{$operation_application->trk->name}}</td>
                                                    @endif
                                                    <td>{{$operation_application->division->name}}</td>
                                                    <td>{{$operation_application->trouble_description}}</td>
                                                    <td style="color: {{$operation_application->done_percents == 0 ? 'red' : null}}{{$operation_application->done_percents == 100 ? 'green' : null}}{{$operation_application->done_percents != 100 && $operation_application->done_percents != 0 ? 'darkorange' : null}};"><b>{{$operation_application->done_percents}}</b></td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    </div>
                                    {{$operation_applications->withQueryString()->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal for operation applications -->
        <div class="modal fade" id="operation_applications" tabindex="-1"
             aria-labelledby="operation_applications" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel5">Выгрузка заявок</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('operation_applications.export')}}" method="post">
                            @csrf
                            @method('post')
                            <div class="row row-cols-1 row-cols-md-2">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="start_date">Начало
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <input required class="form-control form-control-sm" type="date" id="start_date" name="start_date"
                                           value="{{date('Y-m-d')}}"
                                           min="2011-01-01" max="2040-12-31">
                                </div>
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="start_date">Конец
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <input required class="form-control form-control-sm" type="date" id="finish_date" name="finish_date"
                                           value="{{date('Y-m-d')}}"
                                           min="2011-01-01" max="2040-12-31">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="trk_id_2">Трк
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <select required name="trk_id"
                                            class="form-select form-select-sm" id="trk_id_2">
                                        @forelse($all_trks as $trk)
                                            <option
                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="division_id_2">Подразделение
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <select required name="division_id"
                                            class="form-select form-select-sm" id="division_id_2">
                                        @forelse($all_divisions as $division)
                                            <option
                                                value="{{$division->id}}" {{isset($old_filters['division_id']) && $old_filters['division_id'] == $division->id ? 'selected' : null}}>{{$division->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="status">Заявки со статусом
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <select required name="status" class="form-select form-select-sm">
                                            <option value="new">Новые</option>
                                            <option value="in_progress">Выполняются</option>
                                            <option value="closed">Закрытые</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="file_type">Тип файла
                                        <span class="text-danger"><b>*</b></span>
                                    </label>
                                    <select required name="file_type"
                                            class="form-select form-select-sm" id="file_type">
                                        <option value=".pdf">PDF</option>
                                        <option value=".xslx">EXCEL XSLX</option>
                                        <option value=".html">HTML</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Выгрузить
                            </button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>
@endsection
