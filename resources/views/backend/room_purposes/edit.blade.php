@extends('layouts.backend.main')

@section('title', 'Назначение помещения | Редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Редактирование назначения</h4>
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
                            <form action="{{route('room_purposes.update', $room_purpose)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Название:<span
                                                    class="text-danger"> *</span></b></span>
                                        <input value="{{$room_purpose->name}}" name="name" type="text"
                                               class="form-control form-control-sm" placeholder="Название"
                                               aria-label="name" aria-describedby="basic-addon1" autofocus>
                                    </div>
                                    @error('name')
                                    <div class="text-danger">{{$message}}</div>
                                    @enderror
                                    <div class="input-group mb-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Создан:</b></span>
                                        <input value="{{$room_purpose->created_at}}" name="created_at" type="text"
                                               class="form-control form-control-sm" placeholder="Создан"
                                               aria-label="alias" aria-describedby="basic-addon1" readonly>
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Автор:</b></span>
                                        <input value="{{$room_purpose->author->name}}" name="author_id" type="text"
                                               class="form-control form-control-sm" placeholder="Автор"
                                               aria-label="alias" aria-describedby="basic-addon1" readonly>
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Исправлен:</b></span>
                                        <input value="{{$room_purpose->updated_at}}" name="updated_at" type="text"
                                               class="form-control form-control-sm" placeholder="Исправлен"
                                               aria-label="alias" aria-describedby="basic-addon1" readonly>
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-text input-group-sm col-6 col-md-2"
                                              id="basic-addon1"><b>Редактор:</b></span>
                                        <input value="{{$room_purpose->last_editor->name}}" name="last_editor_id"
                                               type="text" class="form-control form-control-sm" placeholder="Редактор"
                                               aria-label="alias" aria-describedby="basic-addon1" readonly>
                                    </div>
                                    <div class="input-group mb-3 input-group-sm">
                                        <a href="{{route('room_purposes.show', $room_purpose)}}"
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
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
