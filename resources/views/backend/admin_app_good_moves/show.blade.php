@extends('layouts.backend.main')

@section('title', 'Просмотр | Заявка на ввоз/вывоз')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Заявка на ввоз/вывоз</h4>
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
                                <ul class="list-group mb-4">
                                    <li class="list-group-item">
                                        <b>Заявка на:</b> {{$admin_app_good_move->operation_type == 'import' ? 'ввоз' : 'вывоз'}}
                                    </li>
                                    <li class="list-group-item"><b>Начало:</b> {{$admin_app_good_move->start_at}}</li>
                                    <li class="list-group-item"><b>Конец:</b> {{$admin_app_good_move->finish_at}}</li>
                                    <li class="list-group-item"
                                        @if($admin_app_good_move->admin_app_status->name == \App\Models\AdminApps\AdminAppStatus::APPROVE)
                                        style="background: rgba(10, 255, 9, 0.1);"
                                        @endif
                                        @if($admin_app_good_move->admin_app_status->name == \App\Models\AdminApps\AdminAppStatus::REJECT)
                                            style="background: rgba(255, 9, 9, 0.2);"
                                        @endif>
                                        <b>Статус:</b> {{$admin_app_good_move->admin_app_status->name}}
                                    </li>
                                    @if($admin_app_good_move->comment)
                                        <li class="list-group-item"><b>Комментарий:</b> {{$admin_app_good_move->comment}}</li>
                                    @endif
                                    <li class="list-group-item"><b>ТРК:</b> {{$admin_app_good_move->trk_room->trk->name}}</li>
                                    <li class="list-group-item"><b>Помещение: </b><a href="{{route('trk_room.show', $admin_app_good_move->trk_room->id)}}">{{$admin_app_good_move->trk_room->room->name}}</a></li>
                                    <li class="list-group-item"><b>Арендатор:</b> {{$admin_app_good_move->organization->name}}</li>
                                    <li class="list-group-item"><b>Торговая марка:</b> {{$admin_app_good_move->brand->name}}</li>
                                    <li class="list-group-item"><b>Загрузочная зона:</b> {{$admin_app_good_move->gate_number}}</li>
                                    <li class="list-group-item"><b>Материально ответственный:</b> {{$admin_app_good_move->responsible_user}}</li>
                                </ul>
                                <label class="form-label form-label-sm"><b>Материальные ценности на {{$admin_app_good_move->operation_type == 'import' ? 'ввоз' : 'вывоз'}}:</b></label>
                                <ol class="list-group list-group-numbered mb-4">
                                    @forelse($admin_app_good_move->admin_app_goods as $good)
                                        <li class="list-group-item">
                                            {{$good->name . ', '}}{{$good->tare_type->name . ', '}}{{$good->value . ' шт.'}}
                                        </li>
                                    @empty
                                        <li class="list-group-item">нет данных ...</li>
                                    @endforelse
                                </ol>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('admin_app_good_moves.index')}}"
                                       class="btn btn-outline-success rounded me-1 btn-sm"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>

                                    <button type="button" class="btn btn-outline-success rounded btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal3">
                                        <img src="{{asset('assets/images/backend/svg/check2-all.svg')}}" alt="approve" title="Согласовать заявку" height="20">
                                    </button>

                                    <button type="button" class="btn btn-outline-danger rounded btn-sm me-1" data-bs-toggle="modal"
                                            data-bs-target="#exampleModal2">
                                     <img src="{{asset('assets/images/backend/svg/close.svg')}}" alt="reject" title="Отклонить заявку" height="20">
                                    </button>

                                    @if(auth()->user()->can('edit') || Auth::id() === $admin_app_good_move->author->id)
                                        <a href="{{route('admin_app_good_moves.edit', $admin_app_good_move)}}"
                                           class="btn btn-outline-warning rounded me-1 btn-sm"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete') || Auth::id() === $admin_app_good_move->author->id)
                                        <form action="{{route('admin_app_good_moves.destroy', $admin_app_good_move)}}" method="post">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-outline-danger btn-sm"><img
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
            <!-- Modal reject -->
            <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2"
                 aria-hidden="true">
                <form action="{{route('admin_app_good_moves.reject', $admin_app_good_move)}}" method="post">
                    @csrf
                    @method('patch')
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel2">Отклонение заявки</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                           <label class="form-label form-label-sm">Причина отклонения заявки
                           <span class="text-danger"><b>*</b></span></label>
                            <textarea name="comment" class="form-control form-control-sm" required placeholder="Причина"></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-success">Сохранить</button>
                            <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Отмена</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>
            <!-- Modal approve -->
            <div class="modal fade" id="exampleModal3" tabindex="-1" aria-labelledby="exampleModalLabel3"
                 aria-hidden="true">
                <form action="{{route('admin_app_good_moves.approve', $admin_app_good_move)}}" method="post">
                    @csrf
                    @method('patch')
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel3">Разрешить выполнение заявки?</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <label class="form-label form-label-sm">Комментарий (необязательно)</label>
                            <textarea name="comment" class="form-control form-control-sm" placeholder="Комментарий"></textarea>
                        </div>
                            <div class="row row-cols-2 p-3">
                                <div class="col">
                            <button type="submit" class="btn btn-success col-12">Да</button>
                                </div>
                                <div class="col">
                            <button type="button" class="btn btn-danger col-12" data-bs-dismiss="modal">Нет</button>
                                </div>
                            </div>
                    </div>
                </div>
                </form>
            </div>
            <!-- profile init js -->
            <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
