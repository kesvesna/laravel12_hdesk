@extends('layouts.backend.main')

@section('title', 'Главная | Выполнение заявки')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Выполнение заявки</h4>
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
                            <form
                                action="{{route('operation_applications.done_progress_update', $operation_application)}}"
                                method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК</label>
                                            <input disabled class="form-select form-select-sm"
                                                   value="{{$operation_application->trk->name}}">
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="division_id"
                                                   class="form-label form-label-sm">Подразделение</label>
                                            <input disabled class="form-select form-select-sm"
                                                   value="{{$operation_application->division->name}}">
                                            @error('division_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="trouble_description"
                                                   class="form-label form-label-sm">Проблема</label>
                                            <textarea disabled name="trouble_description"
                                                      class="form-control form-control-sm"
                                                      placeholder="Где проблема и в чем она">{{old('trouble_description', $operation_application->trouble_description)}}</textarea>
                                            @error('trouble_description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="done_description" class="form-label form-label-sm">Что сделано
                                                <span class="text-danger"><b>*</b></span></label>
                                            <textarea autofocus required name="done_description"
                                                      class="form-control form-control-sm"
                                                      placeholder="Что было сделано для решения проблемы">{{old('done_description', $operation_application->result_description)}}</textarea>
                                            @error('done_description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="done_percents" class="form-label form-label-sm">Процент
                                                выполнения: <span
                                                    id="percents_done_progress">{{$operation_application->done_percents . '%'}}</span></label>
                                            <input value="{{old('done_percents', $operation_application->done_percents)}}" type="range"
                                                   class="form-range" min="0" max="100" step="10" id="done_percents"
                                                   name="done_percents">
                                            @error('done_percents')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    @if($operation_application->done_percents < 100)
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
                                                            <input value="{{Auth::user()->name}}" required type="text"
                                                                   list="executors_list"
                                                                   class="form-control form-control-sm"
                                                                   placeholder="Начните писать ..."
                                                                   name="executors[]">
                                                            <datalist id="executors_list">
                                                                @forelse($executors as $executor)
                                                                    <option data-equipment_key="{{$executor->id}}"
                                                                            value="{{$executor->name}}">
                                                                @empty
                                                                    <option data-equipment_key=""
                                                                            value="нет данных ...">
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

                                    @else
                                        <label class="form-label form-label-sm">Исполнители:</label>
                                        <ul>
                                            @foreach($task->executors as $executor)
                                                <li>{{$executor->name}}</li>
                                            @endforeach
                                        </ul>
                                    @endif
                                    <div class="row">
                                        <div class="col mb-4 mt-3">
                                            <label for="done_at" class="form-label form-label-sm">Дата и время
                                                выполнения <span class="text-danger"><b>*</b></span></label>
                                            <input required class="form-control form-control-sm" type="datetime-local"
                                                   id="done_at"
                                                   name="done_at"
                                                   value="{{old('done_at', $operation_application->done_at) ?? date('Y-m-d H:i:00')}}"
                                                   min="2019-01-07T00:00" max="2050-12-14T00:00">
                                            @error('done_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-2">
                                    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal8">Инфо
                                    </button>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <div class="btn-group-vertical btn-group-sm col-12 col-md-4" role="group" aria-label="Vertical radio toggle button group">
                                                <input type="radio" class="btn-check" name="operation_type" value="just_save" id="vbtn-radio1" autocomplete="off" checked>
                                                <label class="btn btn-outline-success" for="vbtn-radio1">Просто сохранить выполнение</label>
                                                <input type="radio" class="btn-check" name="operation_type" value="redirect_application" id="vbtn-radio5" autocomplete="off">
                                                <label class="btn btn-outline-success" for="vbtn-radio5">Направить эту заявку в другой отдел</label>
                                                <input type="radio" class="btn-check" name="operation_type" value="save_and_create_repair" id="vbtn-radio2" autocomplete="off">
                                                <label class="btn btn-outline-success" for="vbtn-radio2">Запланировать ремонт</label>
                                                <input type="radio" class="btn-check" name="operation_type" value="save_and_create_tech_act" id="vbtn-radio3" autocomplete="off">
                                                <label class="btn btn-outline-success" for="vbtn-radio3">Заполнить технический акт</label>
                                                <input type="radio" class="btn-check" name="operation_type" value="save_and_create_avr" id="vbtn-radio4" autocomplete="off">
                                                <label class="btn btn-outline-success" for="vbtn-radio4">Заполнить акт выполненных работ</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('operation_applications.show', $operation_application)}}"
                                           class="btn btn-sm btn-outline-success col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                alt="back" title="Назад"></a>
                                        <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                title="Сохранить"></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal 8 -->
        <div class="modal fade" id="exampleModal8" tabindex="-1" aria-labelledby="exampleModalLabel8"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel3">Выбор переключателя</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p><b>Просто сохранить выполнение</b> - для заявок, которые не для конкретного оборудования, например: Все этажи, Все фанкойлы. Такого оборудования не существует, нет смысла делать для него акт выполненных работ.</p>
                        <p><b>Направить эту заявку в другой отдел</b> - после диагностики выяснилось, что заявка по сути для другого подразделения, указываем, что было выполнено, 10% и после сохранения изменений появится окно с выбором подразделения.</p>
                        <p><b>Запланировать ремонт</b> - после диагностики выяснилось, что необходимо запланировать ремонт для выполнения этой заявки, указываем, что было выполнено, 10% и после сохранения изменений появится страница с планированием ремонта.</p>
                        <p><b>Заполнить технический акт</b> - после диагностики выяснилось, что есть необходимость составить тех. акт, указываем, что было выполнено, какой процент и после сохранения изменений появится страница с заполнением тех. акта.</p>
                        <p><b>Заполнить акт выполненных работ</b> - если заявка подана с указанием конкретного оборудования, указываем, что было выполнено, какой процент и после сохранения изменений появится страница с заполнением акта выполненных работ.</p>
                        <p><b>P.S.</b> Способ принятия заявку в обработку: Что сделано - Ознакомился, принял в обработку. Процент: 10%. Просто сохранить выполнение. Сохранить.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
        <script src="{{asset('assets/js/operation_applications/add_executor.js')}}"></script>
        <script src="{{asset('assets/js/operation_applications/delete_executor.js')}}"></script>
        <script src="{{asset('assets/js/operation_applications/show_percents_for_progress_bar.js')}}"></script>
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
@endsection
