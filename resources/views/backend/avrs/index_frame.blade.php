<!-- Bootstrap Css -->
<link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>

            <div class="p-3">
                <form action="{{route('avrs.index_frame')}}" method="get">
                    @csrf
                    <input hidden name="trk_room_id" value="{{$old_filters['trk_room_id']}}">
                    <div class="row">
                        <div class="col-12 mb-3">
                            <td class="d-none d-md-table-cell">
                                <select name="division_id" class="form-select form-select-sm" id="division_id"
                                        onchange="this.form.submit();">
                                    <option value="">Все подразделения</option>
                                    @forelse($all_divisions as $division)
                                        <option
                                            value="{{$division->id}}" {{isset($old_filters['division_id']) && $old_filters['division_id'] === $division->id ? 'selected' : null}}>{{$division->name}}</option>
                                    @empty
                                        <option value="">нет данных ...</option>
                                    @endforelse
                                </select>
                            </td>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped shadow table-bordered">
                            <thead>
                            <tr>
                                <th>Дата</th>
                                <th>Система</th>
                                <th>Оборудование</th>
                                <th>Работы</th>
                                <th>Кто делал</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>
                                    <input class="form-control form-control-sm"
                                           type="search" id="date" placeholder="Поиск ..."
                                           onchange="this.form.submit();" name="date"
                                           value="{{$old_filters['date'] ?? null}}">
                                </td>
                                <td>
                                    <select name="system_id" class="form-select form-select-sm"
                                            id="system_id" onchange="this.form.submit();">
                                        <option value="">Все</option>
                                        @forelse($all_systems as $system)
                                            <option
                                                value="{{$system->id}}" {{isset($old_filters['system_id']) && $old_filters['system_id'] === $system->id ? 'selected' : null}}>{{$system->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </td>
                                <td>
                                    <input class="form-control form-control-sm"
                                           list="equipment_name_data_list" type="search"
                                           id="equipment_name_id" placeholder="Поиск ..."
                                           onchange="this.form.submit();" name="equipment_name_id"
                                           value="{{$old_filters['equipment_name_id'] ?? null}}">
                                    <datalist id="equipment_name_data_list">
                                        @forelse($all_equipment_names as $equipment)
                                            <option value="{{$equipment->name}}">
                                        @empty
                                            <option value="нет данных ...">
                                        @endforelse
                                    </datalist>
                                </td>
                                <td>
                                    <input class="form-control form-control-sm" list="work_name_data_list"
                                            type="search" id="work_name" placeholder="Поиск по типу работ ..."
                                            onchange="this.form.submit();" name="work_name"
                                            value="{{$old_filters['work_name'] ?? null}}">
                                    <datalist id="work_name_data_list">
                                        @forelse($all_work_names as $work_name)
                                            <option value="{{$work_name->name}}">
                                        @empty
                                            <option value="нет данных ...">
                                        @endforelse
                                    </datalist>
                                </td>
                                <td>
                                    <input class="form-control form-control-sm" list="executor_name_data_list"
                                           type="search" id="executor_name" placeholder="Поиск по исполнителю ..."
                                           onchange="this.form.submit();" name="executor_name"
                                           value="{{$old_filters['executor_name'] ?? null}}">
                                    <datalist id="executor_name_data_list">
                                        @forelse($all_executors as $executor)
                                            <option value="{{$executor->name}}">
                                        @empty
                                            <option value="нет данных ...">
                                        @endforelse
                                    </datalist>
                                </td>
                            </tr>
                </form>
                @forelse($avrs as $avr)
                    @if(auth()->user()->can('read'))
                        <tr>
                    @else
                        <tr>
                            @endif
                            <td class="text-nowrap">{{$avr->date}}</td>
                            <td class="text-nowrap">{{$avr->system->name}}</td>
                            <td>@forelse($avr->avr_equipments as $equipment)
                                    {{$equipment->trk_equipment->equipment_name->name}}<br>
                                @empty
                                @endforelse
                            </td>
                            <td>@forelse($avr->avr_works as $work)
                                    {{$work->work_name->name}}<br>
                                @if($work->description)
                                    <span>{{$work->description}}</span><br>
                                    @endif
                                @empty
                                @endforelse
                            </td>
                            <td>@forelse($avr->avr_executors as $executor)
                                    {{$executor->executor_name->name}}<br>
                                @empty
                                @endforelse
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="4">нет данных ...</td>
                            </tr>
                            @endforelse
                            </tbody>
                            </table>
            </div>
            {{$avrs->withQueryString()->links()}}
        </div>



