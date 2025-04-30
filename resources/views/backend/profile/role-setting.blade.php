@extends('layouts.backend.profile-setting')

@section('title', 'Главная | Создание профиля')

@section('content')
    <div class="vh-100 d-flex justify-content-center align-items-center">
        <div class="card p-4 col-10 col-md-6 mx-auto" style="
                                                            background: rgba( 255, 255, 255, 0.4 );
                                                            box-shadow: 0 2px 10px 0 rgba( 31, 38, 135, 0.5 );
                                                            backdrop-filter: blur( 4px );
                                                            -webkit-backdrop-filter: blur( 4px );
                                                            border-radius: 10px;
                                                            border: 1px solid rgba( 255, 255, 255, 0.2 );">
            <form action="{{route('profile.store')}}" method="post">
                @csrf
                @method('post')
                <div class="row">
                    <div class="col">
                        @include('components.backend.message')
                    </div>
                </div>
                <div class="row row-cols-1">
                    <div class="col mb-4">
                        <label class="form-label form-label-sm" for="town_id"><h6>Город <span
                                    class="text-danger"><b>*</b></span></h6></label>
                        <select class="form-select form-select-sm text-black " name="town_id" required>
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
                    <div class="col mb-4">
                        <label class="form-label form-label-sm" for="organization_id"><h6>Организация <span
                                    class="text-danger"><b>*</b></span></h6></label>
                        <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal"
                                data-bs-target="#exampleModal2">Инфо
                        </button>
                        <select class="form-select form-select-sm" name="organization_id" required>
                            @forelse($organizations as $organization)
                                <option
                                    value="{{$organization->id}}" {{$organization->name == "Fort Group" ? 'selected' : null}}>{{$organization->name}}</option>
                            @empty
                                <option value="">нет данных ...</option>
                            @endforelse
                        </select>
                        @error('organization_id')
                        <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                    <div class="col mb-4">
                        <label class="form-label form-label-sm" for="division_id"><h6>Подразделение <span
                                    class="text-danger"><b>*</b></span></h6></label>
                        <select class="form-select form-select-sm" name="division_id" required id="user_division_id">
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
                    <div class="col mb-4">
                        <label class="form-label form-label-sm" for="function_id"><h6>Должность <span
                                    class="text-danger"><b>*</b></span></h6></label>
                        <select class="form-select form-select-sm" name="function_id" required id="user_function_id">
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
                    <div class="col mb-4">
                        <label for="phone" class="form-label form-label-sm"><h6>Телефон</h6></label>
                        <input type="tel" pattern="[0-9]{1}-[0-9]{3}-[0-9]{3}-[0-9]{2}-[0-9]{2}"
                               class="form-control form-control-sm" id="phone" name="phone"
                               placeholder="8-904-613-78-61">
                        @error('phone')
                        <div class="text-danger">{{$message}}</div>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <button class="btn col-12" type="submit" style="
                                                                    background: rgba( 24, 0, 255, 0.4 );
                                                                    box-shadow: 0 2px 10px 0 rgba( 31, 38, 135, 0.37 );
                                                                    backdrop-filter: blur( 4px );
                                                                    -webkit-backdrop-filter: blur( 4px );
                                                                    border-radius: 10px;
                                                                    border: 1px solid rgba( 255, 255, 255, 0.18 );">
                            Сохранить
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <!-- Modal info -->
    <div class="modal fade" id="exampleModal2" tabindex="-1" aria-labelledby="exampleModalLabel2"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel2">Полезная информация</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Если Вашей организации нет в списке, выберите по дефолту Fort Group</p>
                    <p>Сообщите админу, чтобы добавил Вашу организацию (админ - Гончаренко (ХВО))</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Закрыть</button>
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
