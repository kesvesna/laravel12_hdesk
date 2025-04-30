@extends('layouts.backend.main')

@section('title', 'Главная | Памятка арендатора редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Памятка арендатора редактирование</h4>
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
                            <form action="{{route('renter_memos.update', $renter_memo)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК <span class="text-danger"><b>*</b></span></label>
                                            <select name="trk_id" class="form-select form-select-sm" autofocus>
                                                @forelse($trks as $trk)
                                                    <option value="{{$trk->id}}" {{$renter_memo->trk_id == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="division_id" class="form-label form-label-sm">Подразделение <span class="text-danger"><b>*</b></span></label>
                                            <select name="division_id" class="form-select form-select-sm">
                                                @forelse($divisions as $division)
                                                    <option value="{{$division->id}}" {{$renter_memo->division_id == $division->id ? 'selected' : null}}>{{$division->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('division_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="function"><b>Должность:<span
                                                        class="text-danger"> *</span></b></label>
                                            <input required name="function" type="text" class="form-control form-control-sm"
                                                   placeholder="Администратор" value="{{$renter_memo->function}}">
                                            @error('function')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="name">Фамилия Имя Отчество:</label>
                                            <input name="name" type="text" class="form-control form-control-sm"
                                                   placeholder="Иванов Иван Иванович" value="{{$renter_memo->name}}">
                                            @error('name')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="phone">Номер телефона:</label>
                                            <input name="phone" type="text" class="form-control form-control-sm"
                                                   placeholder="102-34-56 или 8-904-613-78-61" value="{{$renter_memo->phone}}">
                                            @error('phone')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="email">Е-mail:</label>
                                            <input name="email" type="email" class="form-control form-control-sm"
                                                   placeholder="i.ivanov@fortgroup.ru" value="{{$renter_memo->email}}">
                                            @error('email')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col">
                                            <label class="form-label form-label-sm"><b>Создан:</b></label>
                                        <span>{{$renter_memo->created_at . ', '}}{{$renter_memo->author->name}}</span>
                                    </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm"><b>Исправлен:</b></label>
                                            <span>{{$renter_memo->updated_at . ', '}}{{$renter_memo->last_editor->name}}</span>
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('renter_memos.show', $renter_memo)}}"
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
