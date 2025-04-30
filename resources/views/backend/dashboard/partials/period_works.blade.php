

{{--@if($period_works['show'] ?? false)--}}
    <div class="col">
        <div class="card shadow">
            <div class="card-header">
                <span class="fs-4 me-2">Тех. мероприятия</span>
                @if(auth()->user()->can('equipment_work_period create') || auth()->user()->can('all'))
                    <a href="{{route('equipment_work_periods.create')}}"><img class="pb-1"
                                                                              src="{{asset('assets/images/backend/svg/plus-circle.svg')}}"
                                                                              alt="Add" title="Добавить"
                                                                              height="30"></a>
                @endif
            </div>
            <div class="card-body">
                <div class="accordion accordion-flush" id="accordionFlushExample997">
                    <div id="works-this-month"></div>
                    <div id="works-next-month"></div>
                    <div id="works-expired"></div>
                </div>
            </div>
        </div>
    </div>
{{--@endif--}}
