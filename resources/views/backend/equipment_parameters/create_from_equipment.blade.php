@extends('layouts.backend.main')

@section('title', 'Параметры | Создание')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Параметры новые</h4>
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
                            <form action="{{route('equipment_parameters.store_from_equipment')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="equipment_id" class="form-label form-label-sm">Оборудование
                                                <span class="text-danger"><b>*</b></span></label>
                                            <input required readonly hidden name="equipment_id"
                                                   value="{{$trk_equipment->id}}">
                                            <input readonly class="form-control form-control-sm"
                                                   value="{{$trk_equipment->equipment_name->name}}">
                                            @error('equipment_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="parameter_name" class="form-label form-label-sm">Параметр <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input required type="text" list="parameter_names_list"
                                                   class="form-control form-control-sm" id="basic-url"
                                                   aria-describedby="basic-addon3" placeholder="Начните писать ..."
                                                   name="parameter_name">
                                            <datalist id="parameter_names_list">
                                                @forelse($parameter_names as $parameter_name)
                                                    <option value="{{$parameter_name->name}}">
                                                @empty
                                                    <option value="нет данных ...">
                                                @endforelse
                                            </datalist>
                                            @error('parameter_name')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="value" class="form-label form-label-sm">Величина</label>
                                            <input name="value" type="text" class="form-control form-control-sm"
                                                   value="{{old('value')}}" placeholder="Величина">
                                            @error('value')
                                            <div class="text-danger px-3 pb-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label for="comment" class="form-label form-label-sm">Комментарий</label>
                                            <input name="comment" type="text" class="form-control form-control-sm"
                                                   value="{{old('comment')}}" placeholder="Комментарий">
                                            @error('comment')
                                            <div class="text-danger px-3 pb-3">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('trk_equipments.show', $trk_equipment)}}"
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
    </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
    {{--        <script src="{{asset('assets/js/equipment_parameters/add_user.js')}}" defer></script>--}}
    {{--        <script src="{{asset('assets/js/equipment_parameters/delete_user.js')}}" defer></script>--}}
@endsection
