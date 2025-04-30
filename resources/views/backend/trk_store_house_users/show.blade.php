@extends('layouts.backend.main')

@section('title', 'Просмотр | Склад/Пользователь')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Склад/Пользователь</h4>
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
                                    <li class="list-group-item"><b>ТРК: </b>{{$trk_store_house_user->trk->name}}</li>
                                    <li class="list-group-item">
                                        <b>Склад: </b>{{$trk_store_house_user->store_house->name}}</li>
                                    <li class="list-group-item">
                                        <b>Пользователь: </b>{{$trk_store_house_user->user->name ?? 'отсутствует'}}</li>
                                    <li class="list-group-item">
                                        <b>Комментарий: </b>{{$trk_store_house_user->comment ?? 'отсутствует'}}</li>
                                    <li class="list-group-item">
                                        <hr>
                                    </li>
                                    <li class="list-group-item"><b>Создан: </b>{{$trk_store_house_user->created_at . ', ' . $trk_store_house_user->author->name}}
                                    </li>
                                    <li class="list-group-item"><b>Исправлен: </b>{{$trk_store_house_user->updated_at . ', ' . $trk_store_house_user->last_editor->name}}
                                    </li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="javascript:history.back()"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if($trk_store_house_user->author->id == auth()->id()
                                        || auth()->user()->can('all'))
                                        <a href="{{route('trk_store_house_users.edit', $trk_store_house_user)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if($trk_store_house_user->author->id == auth()->id()
                                        || auth()->user()->can('all'))
                                        <form action="{{route('trk_store_house_users.destroy', $trk_store_house_user)}}"
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
