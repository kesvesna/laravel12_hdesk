@extends('layouts.backend.main')

@section('title', 'Главная | Памятка арендатора')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Памятка арендатора</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('renter_memos.create')}}"><img
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
                                <div class="card-body">
                                    <table class="table table-striped table-hover shadow">
                                        <thead>
                                            <tr>
                                                <td>ТРК</td>
                                                <td>Подразделение</td>
                                                <td>Должность</td>
                                                <td>ФИО</td>
                                            </tr>
                                            <form action="{{route('renter_memos.index')}}" method="get">
                                                @csrf
                                                <tr>
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
                                                        <select name="division_id" class="form-select form-select-sm" id="division_id"
                                                                onchange="this.form.submit();">
                                                            <option value="">Все</option>
                                                            @forelse($divisions as $division)
                                                                <option
                                                                    value="{{$division->id}}" {{isset($old_filters['division_id']) && $old_filters['division_id'] === $division->id ? 'selected' : null}}>{{$division->name}}</option>
                                                            @empty
                                                                <option value="">нет данных ...</option>
                                                            @endforelse
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input class="form-control form-control-sm" list="datalistOptions"
                                                               id="exampleDataList" placeholder="Поиск ..." type="search"
                                                               onchange="this.form.submit();" name="function"
                                                               value="{{$old_filters['function'] ?? null}}">
                                                        <datalist id="datalistOptions">
                                                            @forelse($all_functions as $function)
                                                                <option value="{{$function->function}}">
                                                            @empty
                                                                <option value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                    </td>
                                                <td>
                                                        <input class="form-control form-control-sm" list="namelistOptions"
                                                               id="exampleDataList2" placeholder="Поиск ..."
                                                               onchange="this.form.submit();" name="name" type="search"
                                                               value="{{$old_filters['name'] ?? null}}">
                                                        <datalist id="namelistOptions">
                                                            @forelse($all_names as $name)
                                                                <option value="{{$name->name}}">
                                                            @empty
                                                                <option value="нет данных ...">
                                                            @endforelse
                                                        </datalist>
                                                </td>
                                            </tr>
                                            </form>
                                        </thead>
                                        <tbody style="cursor: pointer;">
                                        @forelse($renter_memos as $renter_memo)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('renter_memos.show', $renter_memo->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>{{$renter_memo->trk->name}}</td>
                                                    <td>{{$renter_memo->division->name}}</td>
                                                    <td>{{$renter_memo->function}}</td>
                                                    <td>{{$renter_memo->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="6">нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$renter_memos->withQueryString()->links()}}
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
