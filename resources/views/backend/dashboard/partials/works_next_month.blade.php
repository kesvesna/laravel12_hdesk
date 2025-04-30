@if(!empty($works_next_month['works']) && $works_next_month['total_count'] > 0)
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-headingThree3344">
            <button class="accordion-button collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#flush-collapseThree3344"
                    aria-expanded="false" aria-controls="flush-collapseThree3344">
                На следующий месяц<span
                    class="ms-2 badge text-bg-warning">{{$works_next_month['total_count']}}</span>
            </button>
        </h2>
        <div id="flush-collapseThree3344" class="accordion-collapse collapse"
             aria-labelledby="flush-headingThree3344"
             data-bs-parent="#accordionFlushExample3344">
            <div class="accordion-body">
                <ol class="list-group list-group-numbered list-group-flush">
                    @forelse($works_next_month['grouped'] as $key => $value)
                        <li class="list-group-item">
                            <a href="{{route('equipment_work_periods.index', ['trk_id' => $value['trk_id'], 'next_to_be_at' => $next_month])}}">{{$key}}</a>
                            <span class="badge text-bg-warning">{{$works_next_month['trk_counts'][$key]}}</span>
                            <ol class="list-group list-group-numbered list-group-flush">
                                @foreach($value['systems'] as $key2 => $value2)
                                    @foreach($value2 as $key3 => $value3)
                                        <li class="list-group-item">
                                            <a href="{{route('equipment_work_periods.index', ['trk_id' => $value['trk_id'], 'system_id' => $key2, 'next_to_be_at' => $next_month])}}">{{$key3}}</a>
                                            <span class="badge text-bg-light">{{$value3}}</span>
                                        </li>
                                    @endforeach
                                @endforeach
                            </ol>
                        </li>
                    @empty
                        <li class="list-group-item">На следующий месяц ничего нет ...</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
@endif
