@if(!empty($works_expired['works']) && $works_expired['total_count'] > 0)
    <div class="accordion-item">
        <h2 class="accordion-header" id="flush-heading5566">
            <button class="accordion-button collapsed" type="button"
                    data-bs-toggle="collapse" data-bs-target="#flush-collapse5566"
                    aria-expanded="false" aria-controls="flush-collapse5566">
                Просроченные<span
                    class="ms-2 badge text-bg-danger">{{$works_expired['total_count']}}</span>
            </button>
        </h2>
        <div id="flush-collapse5566" class="accordion-collapse collapse"
             aria-labelledby="flush-heading5566"
             data-bs-parent="#accordionFlushExample3344">
            <div class="accordion-body">
                <ol class="list-group list-group-numbered list-group-flush">
                    @forelse($works_expired['grouped'] as $key => $value)
                        <li class="list-group-item">
                            <a href="{{route('equipment_work_periods.index', ['trk_id' => $value['trk_id'], 'expired_works_date' => $prev_month])}}">{{$key}}</a>
                            <span class="badge text-bg-warning">{{$works_expired['trk_counts'][$key]}}</span>
                            <ol class="list-group list-group-numbered list-group-flush">
                                @foreach($value['systems'] as $key2 => $value2)
                                    @foreach($value2 as $key3 => $value3)
                                        <li class="list-group-item">
                                            <a href="{{route('equipment_work_periods.index', ['trk_id' => $value['trk_id'], 'system_id' => $key2, 'expired_works_date' => $prev_month])}}">{{$key3}}</a>
                                            <span class="badge text-bg-light">{{$value3}}</span>
                                        </li>
                                    @endforeach
                                @endforeach
                            </ol>
                        </li>
                    @empty
                        <li class="list-group-item">Просроченных работ нет</li>
                    @endforelse
                </ol>
            </div>
        </div>
    </div>
@endif
