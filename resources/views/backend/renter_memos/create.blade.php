@extends('layouts.backend.main')

@section('title', 'Главная | Памятка арендатора создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Памятка арендатора создание</h4>
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
                            <form action="{{route('renter_memos.store')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                   <div class="row">
                                       <div class="col mb-3">
                                           <label for="trk_id" class="form-label form-label-sm">ТРК <span class="text-danger"><b>*</b></span></label>
                                           <select name="trk_id" class="form-select form-select-sm" autofocus>
                                               @forelse($trks as $trk)
                                                   <option value="{{$trk->id}}">{{$trk->name}}</option>
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
                                                    <option value="{{$division->id}}">{{$division->name}}</option>
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
                                                   placeholder="Администратор">
                                            @error('function')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                        <label class="form-label form-label-sm" for="name">Фамилия Имя Отчество:</label>
                                        <input name="name" type="text" class="form-control form-control-sm"
                                               placeholder="Иванов Иван Иванович">
                                    @error('name')
                                    <div class="text-danger">{{$message}}</div>
                                    @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="phone">Номер телефона:</label>
                                            <input name="phone" type="text" class="form-control form-control-sm"
                                                   placeholder="102-34-56 или 8-904-613-78-61">
                                            @error('phone')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="email">Е-mail:</label>
                                            <input name="email" type="email" class="form-control form-control-sm"
                                                   placeholder="i.ivanov@fortgroup.ru">
                                            @error('email')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('renter_memos.index')}}"
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
