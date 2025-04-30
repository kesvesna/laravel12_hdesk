@if($repairs['show'] ?? false)
    <div class="col">
        <div class="card shadow">
            <div class="card-header">
                <span class="fs-4 me-2">Ремонт</span>
                @if(auth()->user()->can('trk_repair create') || auth()->user()->can('all'))
                    <a href="{{route('trk_repairs.create')}}"><img class="pb-1"
                                                                   src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                   alt="Add" title="Добавить"
                                                                   height="30"></a>
                @endif
            </div>
            <div class="card-body">
                <div class="accordion accordion-flush" id="accordionFlushExample987">
                    @if(!empty($repairs['new']) && count($repairs['new']) > 0)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingThree987">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseThree987"
                                        aria-expanded="false" aria-controls="flush-collapseThree987">
                                    Новый<span
                                        class="ms-2 badge text-bg-warning">{{count($repairs['new'])}}</span>
                                </button>
                            </h2>
                            <div id="flush-collapseThree987" class="accordion-collapse collapse"
                                 aria-labelledby="flush-headingThree987"
                                 data-bs-parent="#accordionFlushExample987">
                                <div class="accordion-body">
                                    <ol class="list-group list-group-numbered list-group-flush">
                                        @forelse($repairs['new'] as $new_repair)
                                            <li class="list-group-item"><a
                                                    href="{{route('trk_repairs.show', $new_repair->id)}}">
                                                    {{$new_repair->trk_equipment->trk_room->trk->name . ', '}}
                                                    {{$new_repair->trk_equipment->equipment_name->name . ', '}}
                                                </a>
                                                <br>
                                                {{$new_repair->description}}
                                            </li>
                                        @empty
                                            <li class="list-group-item">Нового ремонта нет</li>
                                        @endforelse
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if(!empty($repairs['in_progress']) && count($repairs['in_progress']) > 0)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-heading654">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#flush-collapse654"
                                        aria-expanded="false" aria-controls="flush-collapse654">
                                    Выполняется<span
                                        class="ms-2 badge text-bg-success">{{count($repairs['in_progress'])}}</span>
                                </button>
                            </h2>
                            <div id="flush-collapse654" class="accordion-collapse collapse"
                                 aria-labelledby="flush-heading654"
                                 data-bs-parent="#accordionFlushExample987">
                                <div class="accordion-body">
                                    <ol class="list-group list-group-numbered list-group-flush">
                                        @forelse($repairs['in_progress'] as $in_progress_repair)
                                            <li class="list-group-item"><a
                                                    href="{{route('trk_repairs.show', $in_progress_repair->id)}}">
                                                    {{$in_progress_repair->trk_equipment->trk_room->trk->name . ', '}}
                                                    {{$in_progress_repair->trk_equipment->equipment_name->name . ', '}}
                                                </a>
                                                {{$in_progress_repair->description . ', '}}
                                                {{$in_progress_repair->done_progress . '%'}}
                                            </li>
                                        @empty
                                            <li class="list-group-item">Ремонта в процессе нет</li>
                                        @endforelse
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endif
