@extends('layouts.backend.profile-setting')

@section('title', 'Главная | Создание профиля')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="shadow page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Создание профиля</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header">
                            Заполните пожалуйста форму
                        </div>
                        <div class="card-body">
                            <form action="{{route('profile.store')}}" method="post">
                                @csrf
                                @method('post')
                                <div class="row">
                                    <div class="col">
                                        @include('components.backend.message')
                                    </div>
                                </div>
                                <div class="row row-cols-1 row-cols-lg-2">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="firstnameInput" class="form-label">Фамилия И.О.<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <input readonly type="text" class="form-control" id="firstnameInput"
                                                   placeholder="Иванов И.И." value="{{$user->name}}" name="name"
                                                   required>
                                            @error('name')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="emailInput" class="form-label">Электронная почта<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <input readonly type="email" class="form-control" id="emailInput"
                                                   placeholder="i.ivanov@mail.ru" value="{{$user->email}}" required
                                                   name="email">
                                            @error('email')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row row-cols-1 row-cols-lg-2">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="phonenumberInput" class="form-label">Телефон<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <input type="text" class="form-control" id="phonenumberInput"
                                                   placeholder="+7 123 45 67" value="" name="phone" required>
                                            @error('phone')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="town_id" class="form-label">Город<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select name="town_id" id="town_id" class="form-select">
                                                @forelse($towns as $town)
                                                    <option value="{{$town->id}}">{{$town->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('town_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="organization_id" class="form-label">Организация<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select name="organization_id" id="organization_id" class="form-select">
                                                @forelse($organizations as $organization)
                                                    <option
                                                        value="{{$organization->id}}">{{$organization->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('organization_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <select hidden name="division_id" id="division_id" class="form-select">
                                        @forelse($divisions as $division)
                                            <option value="{{$division->id}}">{{$division->name}}</option>
                                        @empty
                                            <option value="">нет данных ...</option>
                                        @endforelse
                                    </select>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="function_id" class="form-label">Должность<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select name="function_id" id="function_id" class="form-select">
                                                @forelse($functions as $function)
                                                    <option value="{{$function->id}}">{{$function->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('function_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <input hidden value="" name="superior_id">
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="row row-cols-1 row-cols-md-2">
                                            <div class="col text-end">
                                                <button type="submit" class="btn btn-success col-6">Сохранить</button>
                                            </div>
                                            <div class="col">
                                                <button type="button" class="btn btn-light col-6"
                                                        onclick="history.back(); false;">Назад
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end col-->
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
@endsection
