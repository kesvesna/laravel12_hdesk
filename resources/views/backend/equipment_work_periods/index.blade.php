@extends('layouts.backend.main')

@section('title', 'Главная | Периодические работы оборудования')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Периодические работы оборудования</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('equipment_work_periods.create')}}"><img
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
                                <div class="card-title ps-3 pt-3">
                                    <div class="col">
                                        <button type="button" class="btn btn-outline-success btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#equipment_work_periods">Выгрузка
                                            тех. мероприятий
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-hover shadow">
                                        <thead>
                                        <tr>
                                            <th>ТРК</th>
                                            <th>Тип</th>
                                            <th>Помещение</th>
                                            <th>Оборудование</th>
                                            <th>Тех. мероприятие</th>
                                            <th>Период</th>
                                            <th>Последнее</th>
                                            <th>Следующее</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        @if(auth()->user()->can('read'))
                                            <form action="{{route('equipment_work_periods.index')}}" method="get">
                                                @csrf
                                                <tr>
                                                    <td>
                                                        <select class="form-select form-select-sm" id="trk_id"
                                                                onchange="this.form.submit();" name="trk_id">
                                                            <option value="">Все</option>
                                                            @forelse($trks as $trk)
                                                                <option
                                                                    value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select class="form-select form-select-sm" id="system_id"
                                                                onchange="this.form.submit();" name="system_id">
                                                            <option value="">Все</option>
                                                            @forelse($systems as $system)
                                                                <option
                                                                    value="{{$system->id}}" {{isset($old_filters['system_id']) && $old_filters['system_id'] == $system->id ? 'selected' : null}}>{{$system->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm"
                                                                 list="room_name_data_list" type="search"
                                                                 id="room_name" placeholder="Поиск ..."
                                                                 onchange="this.form.submit();" name="room_name"
                                                                 value="{{$old_filters['room_name'] ?? null}}">
                                                        <datalist id="room_name_data_list">
                                                            @forelse($room_names as $room_name)
                                                                <option value="{{$room_name->name}}">
                                                            @empty
                                                                <option value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm"
                                                               list="equipment_name_data_list" type="search"
                                                               id="equipment_name" placeholder="Поиск ..."
                                                               onchange="this.form.submit();" name="equipment_name"
                                                               value="{{$old_filters['equipment_name'] ?? null}}">
                                                        <datalist id="equipment_name_data_list">
                                                            @forelse($equipment_names as $equipment_name)
                                                                <option value="{{$equipment_name->name}}">
                                                            @empty
                                                                <option value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm"
                                                               list="work_name_data_list" type="search"
                                                               id="work_name" placeholder="Поиск ..."
                                                               onchange="this.form.submit();" name="work_name"
                                                               value="{{$old_filters['work_name'] ?? null}}">
                                                        <datalist id="work_name_data_list">
                                                            @forelse($works as $work)
                                                                <option value="{{$work->name}}">
                                                            @empty
                                                                <option value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </td>
                                                    <td></td>
                                                    <td>
                                                        <input class="form-control form-control-sm"
                                                               type="text"
                                                               id="last_was_at" placeholder="2023, 2023-04"
                                                               onchange="this.form.submit();" name="last_was_at"
                                                               value="{{$old_filters['last_was_at'] ?? null}}">
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm"
                                                               type="text"
                                                               id="next_to_be_at" placeholder="2023-12, 2023-05-01"
                                                               onchange="this.form.submit();" name="next_to_be_at"
                                                               value="{{$old_filters['next_to_be_at'] ?? null}}">
                                                    </td>
                                                </tr>
                                            </form>
                                            @forelse($equipment_work_periods as $equipment_work_period)
                                                <tr onclick="window.location='{{ route('equipment_work_periods.show', $equipment_work_period->id) }}';">
                                                    <td class="text-nowrap">{{$equipment_work_period->trk_equipment->trk_room->trk->name}}</td>
                                                    <td class="text-nowrap">{{$equipment_work_period->trk_equipment->system->name}}</td>
                                                    <td class="text-nowrap">{{$equipment_work_period->trk_equipment->trk_room->room->name}}</td>
                                                    <td>{{$equipment_work_period->trk_equipment->equipment_name->name}}</td>
                                                    <td class="text-nowrap">{{$equipment_work_period->work_name->name}}</td>
                                                    <td class="text-nowrap">{{$equipment_work_period->repeat_days}}</td>
                                                    <td class="text-nowrap">{{$equipment_work_period->last_was_at}}</td>
                                                    <td class="text-nowrap">{{$equipment_work_period->next_to_be_at}}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5">нет данных ...</td>
                                                </tr>
                                            @endforelse
                                        @endif
                                        </tbody>
                                    </table>
                                    </div>
                                    {{$equipment_work_periods->withQueryString()->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal for equipment work periods -->
        <div class="modal fade" id="equipment_work_periods" tabindex="-1"
             aria-labelledby="equipment_work_periods" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel5">Выгрузка
                            тех. мероприятий</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('equipment_work_periods.export')}}" method="post">
                            @csrf
                            @method('post')
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="work_type">Выбрать мероприятия</label>
                                    <select required name="work_type"
                                            class="form-select form-select-sm" id="work_type">
                                        <option value="last_was_at">По дате последнего выполнения</option>
                                        <option value="next_to_be_at">По дате следующего выполнения</option>
                                        <option value="created_at">По дате создания записи в базе</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="trk_id_2">Трк</label>
                                    <select required name="trk_id"
                                            class="form-select form-select-sm" id="trk_id_2">
                                        @forelse($trks as $trk)
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
                                    <label class="form-label form-label-sm" for="system_id_2">Тип оборудования</label>
                                    <select required name="system_id"
                                            class="form-select form-select-sm" id="system_id_2">
                                        @forelse($systems as $system)
                                            <option
                                                value="{{$system->id}}" {{isset($old_filters['system_id']) && $old_filters['system_id'] == $system->id ? 'selected' : null}}>{{$system->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="equipment_name_id_2">Оборудование (несколько через ctrl)</label>
                                    <select name="equipment_name_ids[]" multiple size="10"
                                            class="form-select form-select-sm" id="equipment_name_id_2">
                                        @forelse($equipment_names as $equipment_name)
                                            <option
                                                value="{{$equipment_name->id}}" {{isset($old_filters['equipment_name']) && $old_filters['equipment_name'] == $equipment_name->name ? 'selected' : null}}>{{$equipment_name->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="work_name_id_2">Тех. мероприятия (несколько через ctrl)</label>
                                    <select required name="work_name_ids[]" multiple size="10"
                                            class="form-select form-select-sm" id="work_name_id_2">
                                        @forelse($works as $work)
                                            <option
                                                value="{{$work->id}}" {{isset($old_filters['work_name']) && $old_filters['work_name'] == $work->name ? 'selected' : null}}>{{$work->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="start_date">Дата начала</label>
                                    <input required class="form-control form-control-sm" type="date" id="start_date" name="start_date"
                                           value="{{date('Y-m-d')}}"
                                           min="2011-01-01" max="2040-12-31">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="start_date">Дата окончания</label>
                                    <input required class="form-control form-control-sm" type="date" id="finish_date" name="finish_date"
                                           value="{{date('Y-m-d')}}"
                                           min="2011-01-01" max="2040-12-31">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="file_type">Тип
                                        файла</label>
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
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
