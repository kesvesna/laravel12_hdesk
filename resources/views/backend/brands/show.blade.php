@extends('layouts.backend.main')

@section('title', 'Просмотр | Бренд')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Бренд</h4>
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
                                @include('components.backend.message')
                                <ul class="list-group mb-3">
                                    <li class="list-group-item"><b>Название: </b>{{$brand->name}}</li>
                                    <li class="list-group-item">
                                        <p>Используется:</p>
                                        <p>В арендаторах: {{ count($brand->trk_renters) . ' шт.'}}</p>
                                        @forelse($brand->trk_renters as $trk_renter)
                                            <a href="{{route('renter_trk_room_brands.show', $trk_renter->id)}}">{{$trk_renter->trk_room->trk->name . ', ' . $trk_renter->trk_room->building->name . ', ' . $trk_renter->trk_room->floor->name . ', ' . $trk_renter->trk_room->room->name . ', ' . $trk_renter->organization->name}}</a><br>
                                        @empty
                                            <span>нет данных ...</span>
                                        @endforelse
                                    </li>
                                    <li class="list-group-item">
                                        <p>В счетчиках: {{ count($brand->trk_counters) . ' шт.'}}</p>
                                        @forelse($brand->trk_counters as $trk_counter)
                                            <a href="{{route('trk_room_counters.show', $trk_counter->id)}}">{{$trk_counter->trk->name . ', ' . $trk_counter->floor->name . ', ' . $trk_counter->organization->name . ', ' . $trk_counter->number}}</a><br>
                                        @empty
                                            <span>нет данных ...</span>
                                        @endforelse
                                    </li>
                                    <li class="list-group-item">
                                        <p>В заявках в администрацию: {{ count($brand->admin_apps) . ' шт.'}}</p>
                                        @forelse($brand->admin_apps as $admin_app)
                                            <a href="{{route('admin_app_good_moves.show', $admin_app->id)}}">{{$admin_app->created_at}}</a><br>
                                        @empty
                                            <span>нет данных ...</span>
                                        @endforelse
                                    </li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('brands.index')}}"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit'))
                                        <a href="{{route('brands.edit', $brand)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('brands.destroy', $brand)}}" method="post">
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
