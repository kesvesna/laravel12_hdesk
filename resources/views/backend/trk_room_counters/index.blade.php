@extends('layouts.backend.main')

@section('title', 'Главная | Счетчики')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Счетчики</h4>
                        @if(auth()->user()->can('counter create') || Auth::user()->hasRole('sadmin'))
                            <a href="{{route('trk_room_counters.create')}}"><img
                                    src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                    title="Добавить" height="30"></a>
                        @endif
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
                    <div class="col">
                        @include('components.backend.message')
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="card shadow">
                                <div class="card-title ps-3 pt-3">
                                    <div class="col">
                                        <button type="button" class="btn btn-outline-success btn-sm"
                                                data-bs-toggle="modal" data-bs-target="#counter_counts">Выгрузка показаний
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                    <table class="table table-striped table-hover shadow">
                                        <thead>
                                        <tr>
                                            <th colspan="2">Последние показания</th>
                                            @if(count($all_trks) != 1)
                                                <th>ТРК</th>
                                            @endif
                                            <th>Этаж</th>
                                            <th>Бренд</th>
                                            <th>№ счетчика</th>
                                            <th>Тип</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('trk_room_counters.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td colspan="2"></td>
                                                @if(count($all_trks) != 1)
                                                <td>
                                                    <select name="trk_id" class="form-select form-select-sm" id="trk_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_trks as $trk)
                                                            <option
                                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                @endif
                                                <td>
                                                    <select name="floor_id" class="form-select form-select-sm" id="floor_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_floors as $floor)
                                                            <option
                                                                value="{{$floor->id}}" {{isset($old_filters['floor_id']) && $old_filters['floor_id'] === $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" list="brand_data_list"
                                                           type="search" id="brand_name" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="brand_name"
                                                           value="{{$old_filters['brand_name'] ?? null}}">
                                                    <datalist id="brand_data_list">
                                                        @forelse($all_brands as $brand)
                                                            <option value="{{$brand->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                                <td>
                                                    <input class="form-control form-control-sm" list="number_data_list"
                                                           type="search" id="number" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="number"
                                                           value="{{$old_filters['number'] ?? null}}">
                                                    <datalist id="number_data_list">
                                                        @forelse($all_numbers as $number)
                                                            <option value="{{$number->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                                <td>
                                                    <select name="counter_type_id" class="form-select form-select-sm"
                                                            id="counter_type_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($all_counter_types as $counter_type)
                                                            <option
                                                                value="{{$counter_type->id}}" {{isset($old_filters['counter_type_id']) && $old_filters['counter_type_id'] === $counter_type->id ? 'selected' : null}}>{{$counter_type->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                            </tr>
                                        </form>
                                        @forelse($trk_room_counters as $trk_room_counter)
                                            @if(auth()->user()->can('read'))
{{--                                                <tr onclick="window.location='{{ route('trk_room_counters.show', $trk_room_counter->id) }}';">--}}
                                                <tr>
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>
                                                        @if(auth()->user()->can('counter_count create'))
                                                            <a href="{{route('counter_counts.create_from_trk_room_counter', $trk_room_counter)}}"><img src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                                                    title="Заполнить показания" height="25"></a>
                                                        @endif
                                                    </td>
                                                    <td class="text-nowrap" onclick="window.location='{{ route('trk_room_counters.show', $trk_room_counter->id) }}';" style="background-color:


                                                    {{Carbon\Carbon::createFromFormat('Y-m-d', $trk_room_counter->counts->last()->date)->gt(date('Y-m-' . '20')) ? '#daffd5' : '#ffd5d5'}}

                                                    ;">
                                                        {{$trk_room_counter->counts->last()->date}}
                                                    </td>
                                                    @if(count($all_trks) != 1)
                                                        <td class="text-nowrap" onclick="window.location='{{ route('trk_room_counters.show', $trk_room_counter->id) }}';">{{$trk_room_counter->trk->name}}</td>
                                                    @endif
                                                    <td class="text-nowrap" onclick="window.location='{{ route('trk_room_counters.show', $trk_room_counter->id) }}';">{{$trk_room_counter->floor->name}}</td>
                                                    <td class="text-nowrap" onclick="window.location='{{ route('trk_room_counters.show', $trk_room_counter->id) }}';">{{$trk_room_counter->brand->name ?? 'отсутствует'}}</td>
                                                    <td class="text-nowrap" onclick="window.location='{{ route('trk_room_counters.show', $trk_room_counter->id) }}';">{{$trk_room_counter->number}}</td>
                                                    <td onclick="window.location='{{ route('trk_room_counters.show', $trk_room_counter->id) }}';">{{$trk_room_counter->counter_type->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="5">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    </div>
                                    {{$trk_room_counters->withQueryString()->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal for counters count export -->
        <div class="modal fade" id="counter_counts" tabindex="-1"
             aria-labelledby="counter_counts" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel5">Отчет по показаниям
                            счетчиков</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="{{route('trk_room_counters.export')}}" method="post">
                            @csrf
                            @method('post')
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm"
                                           for="trk_id_2">ТРК</label>
                                    <select required name="trk_id"
                                            class="form-select form-select-sm" id="trk_id_2">
                                        @forelse($all_trks as $trk)
                                            <option
                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm"
                                           for="floor_id_2">Этаж</label>
                                    <select name="floor_id"
                                            class="form-select form-select-sm" id="floor_id_2">
                                            <option value="">Все</option>
                                        @forelse($all_floors as $floor)
                                            <option
                                                value="{{$floor->id}}" {{isset($old_filters['floor_id']) && $old_filters['floor_id'] === $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="start_date">Начиная
                                        с:</label>
                                    <input type="date" name="start_date"
                                           class="form-control form-control-sm" required
                                           value="{{isset($old_filters['trk_id']) ? $old_filters['trk_id'] : null}}">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="finish_date">по:</label>
                                    <input type="date" name="finish_date"
                                           class="form-control form-control-sm" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col mb-3">
                                    <label class="form-label form-label-sm" for="file_type">Тип
                                        файла</label>
                                    <select required name="file_type"
                                            class="form-select form-select-sm" id="file_type">
                                        <option value=".xslx">EXCEL XSLX</option>
                                        <option value=".pdf">PDF</option>
                                        <option value=".html">HTML</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success btn-sm">Выгрузить
                                отчет
                            </button>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Закрыть
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
