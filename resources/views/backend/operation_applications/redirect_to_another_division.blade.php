@extends('layouts.backend.main')

@section('title', 'Главная | Перенаправление заявки')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                        <h4 class="mb-sm-0">Перенаправление заявки в другое подразделение</h4>
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
                            <form action="{{route('operation_applications.redirect_to_another_division_update', $operation_application)}}"
                                  method="post">
                                @csrf
                                @method('patch')
                                <div class="col">
                                    @include('components.backend.message')
                                </div>
                                <div class="card shadow p-3">
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="trk_id" class="form-label form-label-sm">ТРК<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <input required disabled name="trk_id" class="form-control form-control-sm" value="{{$operation_application->trk->name}}">
                                            @error('trk_id')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col mb-3">
                                            <label for="division_id" class="form-label form-label-sm">Куда перенаправить<span
                                                    class="text-danger"><b> *</b></span></label>
                                            <select required name="division_id" id="division_id"
                                                    class="form-select form-select-sm">
                                                @forelse($divisions as $division)
                                                    <option
                                                        value="{{$division->id}}" {{isset($user->trk->id) && $user->trk->id === $division->id ? 'selected' : null}}>{{$division->name}}</option>
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
                                        <div class="col mb-4">
                                            <label for="trouble_description" class="form-label form-label-sm">Проблема
                                                <span class="text-danger"><b>*</b></span></label>
                                            <textarea disabled required name="trouble_description"
                                                      class="form-control form-control-sm"
                                                      placeholder="Где проблема и в чем она">{{old('trouble_description', $operation_application->trouble_description)}}</textarea>
                                            @error('trouble_description')
                                            <div class="text-danger">{{$message}}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="input-group input-group-sm">
                                        <a href="{{route('operation_applications.show', $operation_application)}}"
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
