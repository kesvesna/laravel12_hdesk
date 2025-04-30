@extends('layouts.backend.main')

@section('title', 'Главная | Счетчик редактирование')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Счетчик редактирование</h4>
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
                            <form action="{{route('trk_room_counters.update', $trk_room_counter)}}" method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="trk_id">ТРК <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="trk_id" id="trk_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_trks as $trk)
                                                    <option
                                                        value="{{$trk->id}}" {{old('trk_id', $trk_room_counter->trk_id) == $trk->id ? 'selected' : null}}>{{$trk->name}}</option>
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
                                            <label class="form-label form-label-sm" for="floor_id">Этаж <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="floor_id" id="floor_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_floors as $floor)
                                                    <option
                                                        value="{{$floor->id}}" {{$trk_room_counter->floor_id == $floor->id ? 'selected' : null}}>{{$floor->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('floor_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="brand_id">Бренд </label>
                                            <select required name="brand_id" id="brand_id"
                                                    class="form-select form-select-sm">
                                                <option value="null">Нет бренда ...</option>
                                                @forelse($all_brands as $brand)
                                                    <option
                                                        value="{{$brand->id}}" {{$trk_room_counter->brand_id == $brand->id ? 'selected' : null}}>{{$brand->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('brand_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label class="form-label form-label-sm" for="organization_id">Организация <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <select required name="organization_id" id="organization_id"
                                                    class="form-select form-select-sm">
                                                @forelse($all_organizations as $organization)
                                                    <option
                                                        value="{{$organization->id}}" {{$trk_room_counter->organization_id == $organization->id ? 'selected' : null}}>{{$organization->name}}</option>
                                                @empty
                                                    <option value="">нет данных ...</option>
                                                @endforelse
                                            </select>
                                            @error('organization_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="number">№ счетчика <span
                                                    class="text-danger"><b>*</b></span></label>
                                            <input value="{{old('number', $trk_room_counter->number)}}"
                                                   required class="form-control form-control-sm" list="number_data_list"
                                                   type="search" id="number" placeholder="Поиск ..." name="number">
                                            <datalist id="number_data_list">
                                                @forelse($all_counters as $counter)
                                                    <option value="{{$counter->number}}">
                                                @empty
                                                    <option value="нет данных ...">
                                                @endforelse
                                            </datalist>
                                            @error('number')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="mounted_at">Дата
                                                установки</label>
                                            <input class="form-control form-control-sm" type="date" id="mounted_at"
                                                   name="mounted_at"
                                                   value="{{old('mounted_at', date('Y-m-d', strtotime($trk_room_counter->mounted_at)))}}">
                                            @error('mounted_at')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="using_purpose">Где
                                                используется</label>
                                            <input class="form-control form-control-sm" type="text" id="using_purpose"
                                                   placeholder="Наружная реклама и т.д." name="using_purpose"
                                                   value="{{old('using_purpose', $trk_room_counter->using_purpose)}}">
                                            @error('using_purpose')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-4">
                                            <label class="form-label form-label-sm" for="comment">Комментарий</label>
                                            <input class="form-control form-control-sm" type="text" id="comment"
                                                   placeholder="Любой комментарий" name="comment"
                                                   value="{{old('comment', $trk_room_counter->comment)}}">
                                            @error('comment')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group mb-3 input-group-sm">
                                        <a href="{{route('trk_room_counters.show', $trk_room_counter)}}"
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
        <script src="{{asset('assets/js/jquery.min.js')}}"></script>
        <script>
            $(document).ready(function () {

                $('#trk_id').on('change', function () {
                    var idTrk = this.value;
                    $("#floor_id").html('');
                    $.ajax({
                        url: "{{url('api/fetch-rooms')}}",
                        type: "POST",
                        data: {
                            trk_id: idTrk,
                            _token: '{{csrf_token()}}',
                        },
                        dataType: 'json',
                        success: function (result) {
                            $('#floor_id').html('');
                            $.each(result.rooms, function (key, value) {
                                $("#floor_id").append('<option value="' + value
                                    .id + '">' + value.name + '</option>');
                            });
                            if (result.rooms.length === 0) {
                                $("#floor_id").append('<option value="">нет помещений ...</option>');
                            }
                        }
                    });
                });
            });
        </script>
@endsection
