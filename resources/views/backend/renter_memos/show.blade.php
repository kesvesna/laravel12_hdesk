@extends('layouts.backend.main')

@section('title', 'Просмотр | Памятка арендатора')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Памятка арендатора</h4>
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
                                    <li class="list-group-item"><b>ФИО: </b>{{$renter_memo->name}}</li>
                                    <li class="list-group-item"><b>Телефон: </b>{{$renter_memo->phone}}</li>
                                    <li class="list-group-item"><b>E-mail: </b>{{$renter_memo->email}}</li>
                                    <li class="list-group-item"><b>ТРК: </b>{{$renter_memo->trk->name}}</li>
                                    <li class="list-group-item"><b>Подразделение: </b>{{$renter_memo->division->name}}</li>
                                    <li class="list-group-item"><b>Должность: </b>{{$renter_memo->function}}</li>
                                </ul>
                                <div class="btn-group btn-group-sm col-12 col-md-6" role="group"
                                     aria-label="Buttons mix example">
                                    <a href="{{route('renter_memos.index')}}"
                                       class="btn btn-outline-success col-4 col-md-2 rounded me-1"><img
                                            src="{{asset('assets/images/backend/svg/skip-backward.svg')}}" alt="back"
                                            title="Назад"></a>
                                    @if(auth()->user()->can('edit') || Auth::id() === $renter_memo->author->id)
                                        <a href="{{route('renter_memos.edit', $renter_memo)}}"
                                           class="btn btn-outline-warning col-4 col-md-2 rounded me-1"><img
                                                src="{{asset('assets/images/backend/svg/pencil.svg')}}" alt="edit"
                                                title="Редактировать"></a>
                                    @endif
                                    @if(auth()->user()->can('delete') || Auth::id() === $renter_memo->author->id)
                                        <form action="{{route('renter_memos.destroy', $renter_memo)}}" method="post">
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
