@if($apps_to_your_division['show_to_division'] ?? false)
    <div class="col">
        <div class="card  shadow">
            <div class="card-header">
                <span class="fs-4 me-2">Заявки Вашему подразделению</span>
            </div>
            <div class="card-body">
                <div class="accordion accordion-flush" id="accordionFlushExample789">
                    @if($apps_to_your_division['to_division']['new_count'] > 0)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingOne11">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseOne11"
                                        aria-expanded="false" aria-controls="flush-collapseOne11">
                                    Новые<span
                                        class="ms-2 badge text-bg-warning">{{$apps_to_your_division['to_division']['new_count']}}</span>
                                </button>
                            </h2>
                            <div id="flush-collapseOne11" class="accordion-collapse collapse"
                                 aria-labelledby="flush-headingOne11"
                                 data-bs-parent="#accordionFlushExample789">
                                <div class="accordion-body">
                                    <ol class="list-group list-group-numbered list-group-flush">
                                        @forelse($apps_to_your_division['to_division']['new'] as $key => $value)
                                            <li class="list-group-item">
                                                <b>{{$key}}</b><br>
                                                <ol class="list-group list-group-numbered list-group-flush">
                                                    @foreach($value as $app)
                                                        <li class="list-group-item">
                                                            <a href="{{route('operation_applications.show', $app->id)}}">{{$app->created_at}}</a><br>
                                                            {{$app->trouble_description}}
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </li>
                                        @empty
                                            <li class="list-group-item">
                                                Новых заявок нет ...
                                            </li>
                                        @endforelse
                                    </ol>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if($apps_to_your_division['to_division']['in_process_count'] > 0)
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingTwo22">
                                <button class="accordion-button collapsed" type="button"
                                        data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo22"
                                        aria-expanded="false" aria-controls="flush-collapseTwo22">
                                    Выполняются<span
                                        class="ms-2 badge text-bg-success">{{$apps_to_your_division['to_division']['in_process_count']}}</span>
                                </button>
                            </h2>
                            <div id="flush-collapseTwo22" class="accordion-collapse collapse"
                                 aria-labelledby="flush-headingTwo22"
                                 data-bs-parent="#accordionFlushExample789">
                                <div class="accordion-body">
                                    <ol class="list-group list-group-numbered list-group-flush">
                                        @forelse($apps_to_your_division['to_division']['in_process'] as $key => $value)
                                            <li class="list-group-item">
                                                <b>{{$key}}</b><br>
                                                <ol class="list-group list-group-numbered list-group-flush">
                                                    @foreach($value as $app_in_process)
                                                        <li class="list-group-item">
                                                            <a href="{{route('operation_applications.show', $app_in_process->id)}}">{{$app_in_process->created_at}}</a><br>
                                                            {{$app_in_process->trouble_description}}<br>
                                                            <b>Выполнено: </b>{{$app_in_process->done_percents . '%'}}
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </li>
                                        @empty
                                            <li class="list-group-item">Заявок в процессе нет</li>
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
