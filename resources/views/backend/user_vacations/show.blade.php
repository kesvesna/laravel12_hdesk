@extends('layouts.backend.main')

@section('title', 'Главная | Отпуск')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Отпуск</h4>
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
                            <div class="card p-3">
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="user_id" class="form-label form-label-sm">Сотрудник</label>
                                        <input readonly class="form-control form-control-sm" name="user_id"
                                               value="{{$user_vacation->user->name}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="start" class="form-label form-label-sm">Начало отпуска <span
                                                class="text-danger"><b>*</b></span></label>
                                        <input readonly type="date" class="form-control form-control-sm" name="start"
                                               value="{{$user_vacation->start}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-3">
                                        <label for="finish" class="form-label form-label-sm">Конец отпуска <span
                                                class="text-danger"><b>*</b></span></label>
                                        <input readonly type="date" class="form-control form-control-sm" name="finish"
                                               value="{{$user_vacation->finish}}">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col mb-4">
                                        <label for="result" class="form-label form-label-sm">Дней</label>
                                        <input readonly class="form-control form-control-sm" name="result"
                                               value="{{$user_vacation->result}}">
                                    </div>
                                </div>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('user_vacations.index')}}"
                                       class="btn btn-outline-success rounded col-4 col-md-2 me-2"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit'))
                                        <a href="{{route('user_vacations.edit', $user_vacation)}}"
                                           class="btn btn-outline-warning rounded col-4 col-md-2 me-2"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete'))
                                        <form action="{{route('user_vacations.destroy',  $user_vacation)}}"
                                              method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn rounded btn-outline-danger"><img
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
