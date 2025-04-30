@extends('layouts.backend.main')

@section('title', 'Просмотр | Счетчик')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Счетчик</h4>
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
                            <div class="card shadow p-3">
                                <ul class="list-group mb-3">
                                    <li class="list-group-item"><b>ТРК: </b>{{$trk_room_counter->trk->name}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Этаж: </b>{{$trk_room_counter->floor->name}}</li>
                                    <li class="list-group-item"><b>Юр. лицо: </b>
                                        {{$trk_room_counter->organization->name ?? 'отсутствует'}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Бренд: </b>{{$trk_room_counter->brand->name ?? 'отсутствует'}}
                                    </li>
                                    <li class="list-group-item"><b>№ счетчика: </b>{{$trk_room_counter->number}}</li>
                                    <li class="list-group-item"><b>Тип: </b>{{$trk_room_counter->counter_type->name}}
                                    </li>
                                    <li class="list-group-item"><b>Коэффициент: </b>{{$trk_room_counter->coefficient}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Тариф: </b>{{$trk_room_counter->counter_tariff->name}}</li>
                                    <li class="list-group-item"><b>Дата
                                            установки: </b>{{$trk_room_counter->mounted_at ?? 'не указано'}}</li>
                                    <li class="list-group-item"><b>Используется
                                            для: </b>{{$trk_room_counter->using_purpose ?? 'не указано'}}</li>
                                    <li class="list-group-item"><b>Комментарий</b>
                                        {{$trk_room_counter->comment ?? 'не указано'}}</li>
                                </ul>
                                <div class="row">
                                    <div class="col">
                                <span>Средний расход <b>день</b>: </span><span  style="background-color: lightyellow;"> <b>{{$average_day_consumption}}</b></span>
                                    </div>
                                        @if(!is_null($average_night_consumption))

                                    <br>
                                        <div class="row">
                                        <div class="col">
                                    <span>Средний расход <b>ночь</b>: </span><span  style="background-color: lightcyan;"><b>{{$average_night_consumption}}</b></span>
                                        </div>
                                            @endif
                                </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="mt-4 table-striped table table-sm table-bordered table-hover shadow">
                                        <thead>
                                            <tr>
                                                <th>Дата</th>
                                                <th>Тариф</th>
                                                <th>Показания</th>
                                                <th>Расход</th>
                                                <th>Заполнил</th>
                                                <th>Комментарий</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                                @forelse($trk_room_counter->counts as $count)
                                                    <?php $count->tariff == 1 ? $result = $trk_room_counter->coefficient * ($count->count - $prev_day_count) : $result = $trk_room_counter->coefficient * ($count->count - $prev_night_count); ?>
                                                    <?php $count->tariff == 1 ? $prev_day_count = $count->count : $prev_night_count = $count->count; ?>
                                                    <?php
                                                            $background_color = null;

                                                            if($count->tariff == 1)
                                                            {
                                                                $result > $average_day_consumption * 2 ? $background_color = 'lightpink' : '';

                                                            } else {

                                                                $result > $average_night_consumption * 2 ? $background_color = 'lightpink' : '';
                                                            }
                                                    ?>
                                                    <tr onclick="window.location='{{ route('counter_counts.show', $count->id) }}';" style="cursor:pointer;">
                                                    <td>{{$count->date}}</td>
                                                    <td>{{$count->tariff ? 'день' : 'ночь'}}</td>
                                                    <td>{{$count->count}}</td>
                                                        <td style="background-color: {{$background_color}}">{{$result}}</td>
                                                        <?php $background_color = null; ?>
                                                        <td>{{$count->author->name}}</td>
                                                        <td>{{$count->comment}}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back()"
                                       class="btn btn-outline-success rounded col-4 col-md-2 me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('counter_count create'))
                                        <a href="{{route('counter_counts.create_from_trk_room_counter', $trk_room_counter)}}" class="btn btn-sm btn-outline-success rounded col-4 col-md-2 me-2"><img
                                                src="{{asset('assets/images/backend/svg/receipt-cutoff.svg')}}"
                                                alt="count" title="Заполнить показания"></a>
                                    @endif
                                    @if(auth()->user()->can('counter update'))
                                        <a href="{{route('trk_room_counters.edit', $trk_room_counter)}}"
                                           class="btn btn-sm btn-outline-warning rounded col-4 col-md-2 me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    <form action="{{route('trk_room_counters.export_one_counter', [$trk_room_counter, 'excel'])}}" method="get">
                                        @csrf
                                        @method('get')
                                        <button type="submit" class="btn btn-sm btn-outline-success me-2"><img
                                                src="{{asset('assets/images/backend/svg/file_xslx.svg')}}"  height="26" alt="На печать excel"
                                                title="На печать"></button>
                                    </form>
                                    <form action="{{route('trk_room_counters.export_one_counter', [$trk_room_counter, 'not excel'])}}" method="get">
                                        @csrf
                                        @method('get')
                                        <button type="submit" class="btn btn-sm btn-outline-success me-2"><img
                                                src="{{asset('assets/images/backend/svg/file_pdf.svg')}}"  height="26" alt="На печать pdf"
                                                title="На печать"></button>
                                    </form>
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('trk_room_counters.destroy', $trk_room_counter)}}"
                                              method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn rounded btn-outline-danger"><img
                                                    src="{{asset('assets/images/backend/svg/trash3.svg')}}" alt="delete"
                                                    title="Удалить"></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <!-- profile init js -->
            <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
