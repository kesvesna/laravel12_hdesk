@extends('layouts.backend.main')

@section('title', 'Главная | Помещение редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Помещение редактирование</h4>
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
                            <form action="{{route('room.update', $room)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow">
                                    <div class="col ms-3 mt-3">
                                        <!-- Button trigger modal responsibility -->
                                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                                data-bs-target="#exampleModal2">Как создать?
                                        </button>
                                    </div>
                                    <div class="input-group mt-3 px-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Название:<span
                                                    class="text-danger"> *</span></b></span>
                                        <input value="{{$room->name}}" name="name" type="text"
                                               class="form-control form-control-sm" placeholder="Название"
                                               aria-label="name" aria-describedby="basic-addon1" autofocus>
                                    </div>
                                    @error('name')
                                    <div class="text-danger px-3">{{$message}}</div>
                                    @enderror
                                    <div class="input-group mb-3 px-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Создан:</b></span>
                                        <input value="{{$room->created_at}}" name="created_at" type="text"
                                               class="form-control form-control-sm" placeholder="Создан"
                                               aria-label="alias" aria-describedby="basic-addon1" readonly>
                                    </div>
                                    <div class="input-group mb-3 px-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Автор:</b></span>
                                        <input value="{{$room->author->name}}" name="author_id" type="text"
                                               class="form-control form-control-sm" placeholder="Автор"
                                               aria-label="alias" aria-describedby="basic-addon1" readonly>
                                    </div>
                                    <div class="input-group mb-3 px-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Исправлен:</b></span>
                                        <input value="{{$room->updated_at}}" name="updated_at" type="text"
                                               class="form-control form-control-sm" placeholder="Исправлен"
                                               aria-label="alias" aria-describedby="basic-addon1" readonly>
                                    </div>
                                    <div class="input-group mb-3 px-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Редактор:</b></span>
                                        <input value="{{$room->last_editor->name}}" name="last_editor_id" type="text"
                                               class="form-control form-control-sm" placeholder="Редактор"
                                               aria-label="alias" aria-describedby="basic-addon1" readonly>
                                    </div>
                                    <div class="input-group mb-3 px-3 input-group-sm">
                                        <a href="{{route('room.show', $room)}}"
                                           class="btn btn-sm btn-outline-success col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                alt="back" title="Назад"></a>
                                        <button type="submit" class="btn btn-sm btn-outline-danger col-6 col-md-2"><img
                                                src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                title="Сохранить"></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- Modal alert -->
        <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel2">Создание помещения</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Если все помещения торгового комплекса</p>
                        <p>можно описать при помощи кириллицы,</p>
                        <p>то создавайте названия помещений кириллицей.</p>
                        <p>В остальных случаях латиница.</p>
                        <p>Не создавайте смешанных названий,</p>
                        <p>потом невозможно будет их найти</p>
                        <p>через фильтры.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
