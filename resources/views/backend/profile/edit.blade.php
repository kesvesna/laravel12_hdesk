@extends('layouts.backend.main')

@section('title', 'Профиль | Редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="shadow page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Редактирование профиля</h4>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="card shadow">
                        <div class="card-header">
                            Профиль
                        </div>
                        <div class="card-body">
                            <form action="{{route('profile.update', $user)}}" method="post">
                                @csrf
                                @method('patch')
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
                                            <input type="text" class="form-control form-control-sm" id="firstnameInput"
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
                                            <input readonly type="email" class="form-control form-control-sm" id="emailInput"
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
                                            <label for="phone" class="form-label">Телефон</label>
                                            <input type="tel" pattern="[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{2}-[0-9]{2}"
                                                   class="form-control form-control-sm" id="phone"
                                                   placeholder="8-904-613-78-61" value="{{$user->phone ?? ''}}"
                                                   name="phone">
                                            @error('phone')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="organization_id" class="form-label">Организация<span
                                                    class="text-danger"><b> *</b></span></label>
                                            @role('sadmin')
                                            <select name="organization_id" id="organization_id" class="form-select form-select-sm form-select form-select-sm-sm">
                                                @forelse($organizations as $organization)
                                                    <option
                                                        value="{{$organization->id}}" {{isset($user->organization->id) && $organization->id === $user->organization->id ? 'selected' : null}}>{{$organization->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('organization_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                            @else
                                                <input required hidden name="organization_id" value="{{auth()->user()->organization->id}}">
                                                <span class="form-control form-control-sm">{{Auth::user()->organization->name}}</span>
                                                @endrole
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="town_id" class="form-label">Город<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select name="town_id" id="town_id" class="form-select form-select-sm">
                                                @forelse($towns as $town)
                                                    <option
                                                        value="{{$town->id}}" {{isset($user->town->id) && $town->id == $user->town->id ? 'selected' : null}}>{{$town->name ?? 'не заполнено'}}</option>
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
                                    @if(!is_null($divisions))
                                        <div class="col-lg-6">
                                            <div class="mb-3">
                                                <label for="division_id" class="form-label">Подразделение<span
                                                        class="text-danger"><b> *</b></span></label>
                                                @role('sadmin')
                                                <select name="division_id" id="user_division_id" class="form-select form-select-sm">
                                                    @forelse($divisions as $division)
                                                        <option
                                                            value="{{$division->id}}" {{isset($user->division->id) && $division->id == $user->division->id ? 'selected' : null}}>{{$division->name}}</option>
                                                    @empty
                                                        <option value="">нет данных ...</option>
                                                    @endforelse
                                                </select>
                                                @error('division_id')
                                                <div class="text-danger">{{$message}}</div>
                                                @enderror
                                                @else
                                                    <input required hidden name="division_id" value="{{auth()->user()->division->id}}">
                                                    <span class="form-control form-control-sm">{{Auth::user()->division->name}}</span>
                                                    @endrole
                                            </div>
                                        </div>
                                    @endif
                                    <!--end col-->
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="function_id" class="form-label">Должность<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select name="function_id" id="user_function_id" class="form-select form-select-sm">
                                                @forelse($functions as $function)
                                                    <option
                                                        value="{{$function->id}}" {{isset($user->function->id) && $function->id == $user->function->id ? 'selected' : null}}>{{$function->name}}</option>
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
                                    @if(!is_null($superiors))
                                        <div class="col-lg-6">
                                            <div class="mb-4">
                                                <label for="superior_id" class="form-label">Руководитель</label>
                                                <select name="superior_id" id="superior_id" class="form-select form-select-sm">
                                                    <option value="">Нет руководителя</option>
                                                    @forelse($superiors as $superior)
                                                        <option
                                                            value="{{$superior->id}}" {{isset($user->superior->id) && $superior->id === $user->superior->id ? 'selected' : null}}>{{$superior->name}}{{isset($superior->division) ? ' (' . $superior->division->name . ')' : ''}}</option>
                                                    @empty
                                                        <option value="">нет данных ...</option>
                                                    @endforelse
                                                </select>
                                                @error('superior_id')
                                                <div class="text-danger">{{$message}}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif
                                    <!--end col-->
                                </div>
                                @role('sadmin')
                                <div class="row">
                                <div class="col-lg-6">
                                    <div class="mb-3">
                                        <label for="is_blocked" class="form-label">Заблокирован<span
                                                class="text-danger"><b> *</b></span></label>
                                        <select name="is_blocked" id="is_blocked" class="form-select form-select-sm">
                                                <option value="0" {{isset($user->is_blocked) && $user->is_blocked == 0 ? 'selected' : null}}>нет</option>
                                                <option value="1" {{isset($user->is_blocked) && $user->is_blocked == 1 ? 'selected' : null}}>да</option>
                                        </select>
                                        @error('is_blocked')
                                        <div class="text-danger">{{$message}}</div>
                                        @enderror
                                    </div>
                                </div>
                                </div>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-3">
                                            <label for="role_id" class="form-label">Роль<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select name="role_name" id="role_name" class="form-select form-select-sm">
                                                @forelse($roles as $role)
                                                    <option value="{{$role->name}}" {{$user->hasRole($role->name) ? 'selected' : null}}>{{$role->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse

                                            </select>
                                            @error('role_name')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                @endrole
                                <div class="row row-cols-1 row-cols-md-2">
                                    <div class="col mb-3">
                                        <button type="submit" class="btn btn-danger btn-sm col-12">Сохранить</button>
                                    </div>
                                    <div class="col">
                                        <a href="{{route('profile.index')}}" class="btn btn-success btn-sm col-12">Назад в
                                            профиль</a>
                                    </div>
                                </div>
                                <!--end col-->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{asset('assets/js/jquery.min.js')}}"></script>
    <script>
        $(document).ready(function () {

            $('#user_division_id').on('change', function () {
                var idDivsion = this.value;
                $("#user_function_id").html('');
                $.ajax({
                    url: "{{url('api/fetch-functions')}}",
                    type: "POST",
                    data: {
                        user_division_id: idDivsion,
                        _token: '{{csrf_token()}}',
                    },
                    dataType: 'json',
                    success: function (result) {
                        $('#user_function_id').html('');
                        $.each(result.functions, function (key, value) {
                            $("#user_function_id").append('<option value="' + value
                                .id + '">' + value.name + '</option>');
                        });
                        if (result.functions.length === 0) {
                            $("#user_function_id").append('<option value="">нет должностей ...</option>');
                        }
                    }
                });
            });
        });
    </script>
@endsection
