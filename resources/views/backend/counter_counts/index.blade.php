@extends('layouts.backend.main')

@section('title', 'Главная | Показания счетчиков')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Показания счетчиков</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('counter_counts.create')}}"><img
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
                                                data-bs-toggle="modal" data-bs-target="#counter_counts">Отчет
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-striped table-hover shadow">
                                        <thead>
                                        <tr>
                                            <th>Дата</th>
                                            <th>ТРК</th>
                                            <th>Этаж</th>
                                            <th>Бренд</th>
                                            <th>Тип счетчика</th>
                                            <th>Тариф</th>
                                            <th>Номер</th>
                                            <th>Показания</th>
                                        </tr>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        <form action="{{route('counter_counts.index')}}" method="get">
                                            @csrf
                                            <tr>
                                                <td></td>
                                                <td class="d-none d-md-table-cell">
                                                    <select name="trk_id" class="form-select form-select-sm" id="trk_id"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($trks as $trk)
                                                            <option
                                                                value="{{$trk->id}}" {{isset($old_filters['trk_id']) && $old_filters['trk_id'] === $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <select name="floor_id" class="form-select form-select-sm"
                                                            id="floor_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($floors as $floor)
                                                            <option
                                                                value="{{$floor->id}}" {{isset($old_filters['floor_id']) && $old_filters['floor_id'] === $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <input class="form-control form-control-sm" list="brand_data_list"
                                                           type="search" id="brand_name" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="brand_name"
                                                           value="{{$old_filters['brand_name'] ?? null}}">
                                                    <datalist id="brand_data_list">
                                                        @forelse($brands as $brand)
                                                            <option value="{{$brand->name}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <select name="counter_type_id" class="form-select form-select-sm"
                                                            id="counter_type_id" onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        @forelse($counter_types as $counter_type)
                                                            <option
                                                                value="{{$counter_type->id}}" {{isset($old_filters['counter_type_id']) && $old_filters['counter_type_id'] === $counter_type->id ? 'selected' : null}}>{{$counter_type->name}}</option>
                                                        @empty
                                                            <option value="">нет данных ...</option>
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <select name="tariff" class="form-select form-select-sm" id="tariff"
                                                            onchange="this.form.submit();">
                                                        <option value="">Все</option>
                                                        <option
                                                            value="1" {{isset($old_filters['tariff']) && $old_filters['tariff'] == 1 ? 'selected' : null}}>
                                                            день
                                                        </option>
                                                        <option
                                                            value="00" {{isset($old_filters['tariff']) && $old_filters['tariff'] == 0 ? 'selected' : null}}>
                                                            ночь
                                                        </option>
                                                    </select>
                                                </td>
                                                <td class="d-none d-md-table-cell">
                                                    <input class="form-control form-control-sm" list="number_data_list"
                                                           type="search" id="number" placeholder="Поиск ..."
                                                           onchange="this.form.submit();" name="number"
                                                           value="{{$old_filters['number'] ?? null}}">
                                                    <datalist id="number_data_list">
                                                        @forelse($counters as $counter)
                                                            <option value="{{$counter->number}}">
                                                        @empty
                                                            <option value="нет данных ...">
                                                        @endforelse
                                                    </datalist>
                                                </td>
                                                <td></td>
                                            </tr>
                                        </form>
                                        @forelse($counter_counts as $counter_count)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('counter_counts.show', $counter_count->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>{{$counter_count->date}}</td>
                                                    <td>{{$counter_count->trk_room_counter->trk->name}}</td>
                                                    <td>{{$counter_count->trk_room_counter->floor->name}}</td>
                                                    <td>{{$counter_count->trk_room_counter->brand->name ?? 'отсутствует'}}</td>
                                                    <td>{{$counter_count->trk_room_counter->counter_type->name}}</td>
                                                    <td>{{$counter_count->tariff ? 'день' : 'ночь'}}</td>
                                                    <td>{{$counter_count->trk_room_counter->number}}</td>
                                                    <td>{{$counter_count->count}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="7">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$counter_counts->withQueryString()->links()}}
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
                                                <form action="{{route('counter_counts.export')}}" method="post">
                                                    @csrf
                                                    @method('post')
                                                    <div class="row">
                                                        <div class="col mb-3">
                                                            <label class="form-label form-label-sm"
                                                                   for="trk_id_2">ТРК</label>
                                                            <select required name="trk_id"
                                                                    class="form-select form-select-sm" id="trk_id_2">
                                                                @forelse($trks as $trk)
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
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
