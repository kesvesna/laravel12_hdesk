<!-- Bootstrap Css -->
<link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>

<div class="card-body">
    <div class="table-responsive">
        <table class="table table-striped table-hover shadow table-bordered">
            <thead>
            <tr>
                <th>Система</th>
                <th>Оборудование</th>
                <th>Оси</th>
            </tr>
            </thead>
            <tbody style="cursor: pointer;">
            <form action="{{route('trk_equipments.index_frame')}}" method="get">
                @csrf
                <input hidden name="trk_room_id" value="{{$old_filters['trk_room_id']}}">
                <tr>
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
                    <td></td>
                </tr>
            </form>
            @forelse($trk_equipments as $trk_equipment)
                @if(auth()->user()->can('read'))
                    <tr onclick="window.location='{{ route('trk_equipments.show', $trk_equipment->id) }}';">
                @else
                    <tr>
                        @endif
                        <td class="text-nowrap">{{$trk_equipment->system->name}}</td>
                        <td class="text-nowrap">{{$trk_equipment->equipment_name->name}}</td>
                        <td class="text-nowrap">{{$trk_equipment->axe->name ?? ''}}</td>
                    </tr>
                    @empty
                        <tr>
                            <td colspan="7">нет данных ...</td>
                        </tr>
                    @endforelse
            </tbody>
        </table>
    </div>
    {{$trk_equipments->withQueryString()->links()}}
</div>



