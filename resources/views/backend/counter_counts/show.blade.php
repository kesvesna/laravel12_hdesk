@extends('layouts.backend.main')

@section('title', 'Просмотр | Показания')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Показания</h4>
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
                    @include('components.backend.message')
                    <div class="row">
                        <div class="col">
                            <div class="card shadow p-3">
                                <ul class="list-group mb-3">
                                    <li class="list-group-item"><b>ТРК: </b>{{$counter_count->trk_room_counter->trk->name}}</li>
                                    <li class="list-group-item"><b>Этаж: </b>{{$counter_count->trk_room_counter->floor->name}}
                                    </li>
                                    <li class="list-group-item">
                                        <b>Бренд: </b>{{$counter_count->trk_room_counter->brand->name ?? 'отсутствует'}}</li>
                                    <li class="list-group-item">
                                        <b>Организация: </b>{{$counter_count->trk_room_counter->organization->name ?? 'отсутствует'}}</li>
                                    <li class="list-group-item"><b>№ счетчика: </b><a href="{{route('trk_room_counters.show', $counter_count->trk_room_counter->id)}}">{{$counter_count->trk_room_counter->number}}</a>
                                    </li>
                                    <li class="list-group-item"><b>Тип: </b>{{$counter_count->trk_room_counter->counter_type->name}}</li>
                                    <li class="list-group-item">
                                        <b>Коэффициент: </b>{{$counter_count->trk_room_counter->coefficient}}</li>
                                    <li class="list-group-item">
                                        <b>Тариф: </b>{{$counter_count->tariff ? 'день' : 'ночь'}}</li>
                                    <li class="list-group-item"><b>Дата: </b>{{$counter_count->created_at}}</li>
                                    <li class="list-group-item"><b>Показания: </b>{{$counter_count->count}}</li>
                                    <li class="list-group-item"><b>Комментарий: </b>{{$counter_count->comment}}</li>
                                </ul>
                                <ul class="list-group mb-3">
                                    <li class="list-group-item"><b>Создано: </b>{{$counter_count->author->name . ', ' . $counter_count->created_at}}</li>
                                    <li class="list-group-item"><b>Изменено: </b>{{$counter_count->last_editor->name . ', ' . $counter_count->updated_at}}</li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back()"
                                       class="me-2 rounded btn btn-outline-success col-4 col-md-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('counter_count update'))
                                        <a href="{{route('counter_counts.edit', $counter_count)}}"
                                           class="me-2 rounded btn btn-outline-warning col-4 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('counter_counts.destroy', $counter_count)}}"
                                              method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-outline-danger"><img
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
