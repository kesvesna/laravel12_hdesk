@if(!empty($works_this_month['works']) && $works_this_month['total_count'] > 0)
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-headingThree997">
            <button class="accordion-button collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#flush-collapseThree997"
                    aria-expanded="false" aria-controls="flush-collapseThree997">
                На этот месяц<span
                    class="ms-2 badge text-bg-success">{{$works_this_month['total_count']}}</span>
            </button>
        </h2>
        <div id="flush-collapseThree997" class="accordion-collapse collapse"
             aria-labelledby="flush-headingThree997"
             data-bs-parent="#accordionFlushExample997">
            <div class="accordion-body">
                <ol class="list-group list-group-numbered list-group-flush">
                    @forelse($works_this_month['grouped'] as $key => $value)
                        <li class="list-group-item">
                            <a href="{{route('equipment_work_periods.index', ['trk_id' => $value['trk_id'], 'next_to_be_at' => date('Y-m')])}}">{{$key}}</a>
                            <span class="badge text-bg-warning">{{$works_this_month['trk_counts'][$key]}}</span>
                            <ol class="list-group list-group-numbered list-group-flush">
                                @foreach($value['systems'] as $key2 => $value2)
                                    @foreach($value2 as $key3 => $value3)
                                        <li class="list-group-item">
                                            <a href="{{route('equipment_work_periods.index', ['trk_id' => $value['trk_id'], 'system_id' => $key2, 'next_to_be_at' => date('Y-m')])}}">{{$key3}}</a>
                                            <span class="badge text-bg-light">{{$value3}}</span>
                                        </li>
                                    @endforeach
                                @endforeach
                            </ol>
                        </li>
                    @empty
                        <li class="list-group-item">На этот месяц ничего нет ...</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
@endif
