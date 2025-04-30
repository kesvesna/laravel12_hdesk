@extends('layouts.backend.main')

@section('title', 'Просмотр | Помещение')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Помещение</h4>
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
                                    <li class="list-group-item"><b>Название: </b>{{$room->name}}</li>
                                    <li class="list-group-item"><b>Состоит из:</b>
                                    @forelse($language as $item)
                                        {{$item . ', '}}
                                    @empty
                                        <span>нет данных ...</span>
                                    @endforelse
                                    </li>
                                    <li class="list-group-item"><b>Используется: </b>{{count($trk_rooms) . 'шт.'}}</li>
                                    @if(count($trk_rooms) > 0)
                                        @forelse($trk_rooms as $trk_room)
                                            <li class="list-group-item"><a href="{{route('trk_room.show', $trk_room->id)}}">{{$trk_room->trk->name . ' - ' . $trk_room->room->name}}</a></li>
                                        @empty
                                        @endforelse
                                        @role('sadmin')
                                        <form action="{{route('trk_room.change_room_name_in_trk_rooms', $trk_room)}}" method="post">
                                            @csrf
                                            @method('patch')
                                            <label for="room_name_id" class="form-label form-label-sm mt-3">Везде заменить на:</label>
                                            <select required name="room_name_id" class="form-select form-select-sm">
                                                <option value="">не заменять</option>
                                                @forelse($room_names as $room_name)
                                                    <option value="{{$room_name->id}}">{{$room_name->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2 mt-3"><img
                                                    src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                    title="Сохранить">
                                            </button>
                                        </form>
                                        @endrole
                                    @endif
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back()"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-1"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit') || Auth::id() === $room->author->id)
                                        <a href="{{route('room.edit', $room)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-1"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete') || Auth::id() === $room->author->id)
                                        <form action="{{route('room.destroy', $room)}}" method="post">
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
