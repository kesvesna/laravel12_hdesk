@extends('layouts.backend.main')

@section('title', 'Просмотр | Этаж')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Этаж</h4>
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
                            <div class="card shadow p-3">
                                <ul class="list-group mb-3">
                                    <li class="list-group-item"><b>Название: </b>{{$floor->name}}</li>
                                    <li class="list-group-item"><b>Используется в помещениях: </b>{{count($trk_rooms) . 'шт.'}}</li>
                                    @if(count($trk_rooms) > 0)
                                        @forelse($trk_rooms as $trk_room)
                                            <li class="list-group-item"><a href="{{route('trk_room.show', $trk_room->id)}}">{{$trk_room->trk->name . ', ' . $trk_room->building->name . ', ' . $trk_room->room->name}}</a></li>
                                        @empty
                                        @endforelse
                                        @role('sadmin')
                                        <form action="{{route('trk_room.change_floor_in_rooms', $floor)}}" method="post">
                                            @csrf
                                            @method('patch')
                                            <div class="works-add-parent-div p-2 mt-3 mb-1 rounded"
                                                 style="background-color: rgba(218, 117, 255, 0.2)">
                                                <label for="basic-url" class="form-label form-label-sm">Во всех помещениях заменить на:
                                                    <span class="text-danger"><b>*</b></span></label>
                                                <div class="work-add-div mb-1 mb-md-0">
                                                    <div class="row row-cols-1">
                                                        <div class="col-12 col-md-5">
                                                            <select required name="floor_id" class="form-select form-select-sm work-type-select">
                                                                <option value="">не заменять</option>
                                                                @forelse($floors as $floor)
                                                                    <option value="{{$floor->id}}">{{$floor->name}}</option>
                                                                @empty
                                                                    <option value="">нет данных ...</option>
                                                                @endforelse
                                                            </select>
                                                            @error('floor_id.*')
                                                            <div class="text-danger"
                                                                 style="margin-top: -1rem !important;">{{$message}}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2 mt-3 mb-3"><img
                                                    src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                    title="Сохранить">
                                            </button>
                                        </form>
                                        @endrole
                                    @endif
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('floor.index')}}"
                                       class="btn btn-outline-success col-4 col-md-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit'))
                                        <a href="{{route('floor.edit', $floor)}}"
                                           class="btn btn-outline-warning col-4 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('floor.destroy', $floor)}}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="ms-2 btn btn-outline-danger btn-sm"><img
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
