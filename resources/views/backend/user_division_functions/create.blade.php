@extends('layouts.backend.main')

@section('title', 'Главная | Поздраделение/Должность создание')

@section('content')
    @csrf
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Поздраделение/Должность создание</h4>
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
                            <form action="{{route('user_division_functions.store')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row row-cols-1">
                                        <div class="col mb-3">
                                            <label for="user_division_id" class="form-label form-label-sm">Подразделение <span class="text-danger"><b>*</b></span></label>
                                            <select required name="user_division_id" class="form-select form-select-sm" autofocus
                                                    id="user_division_id">
                                                @forelse($divisions as $division)
                                                    <option value="{{$division->id}}">{{$division->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('user_division_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                        <div class="col mb-3">
                                            <label for="user_function_id" class="form-label form-label-sm">Должность <span class="text-danger"><b>*</b></span></label>
                                            <select required name="user_function_id" class="form-select form-select-sm">
                                                @forelse($functions as $function)
                                                    <option value="{{$function->id}}">{{$function->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('user_function_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    <div class="row row-cols-1 row-cols-md-3 mt-3">
                                        <div class="col">
                                            <div class="input-group input-group-sm">
                                                <a href="{{route('user_division_functions.index')}}"
                                                   class="btn btn-sm btn-outline-success col-6"><img
                                                        src="{{asset('assets/images/backend/svg/skip-backward.svg')}}"
                                                        alt="back" title="Назад"></a>
                                                <button type="submit" class="btn btn-sm btn-outline-danger col-6"><img
                                                        src="{{asset('assets/images/backend/svg/save.svg')}}" alt="save"
                                                        title="Сохранить"></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
@endsection
