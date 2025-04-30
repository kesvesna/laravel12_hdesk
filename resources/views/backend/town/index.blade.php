@extends('layouts.backend.main')

@section('title', 'Главная | Города')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box">
                        <h4 class="d-inline-block me-3">Города</h4>
                        @if(auth()->user()->can('create'))
                            <a href="{{route('town.create')}}"><img
                                    src="{{asset('assets/images/backend/svg/plus-circle.svg')}}" alt="Add"
                                    title="Добавить" height="30"></a>
                        @endif
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
                    <div class="col">
                        @include('components.backend.message')
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="card shadow">
                                <div class="card-body">
                                    <table class="table table-striped table-hover shadow">
                                        <tbody style="cursor: pointer;">
                                        @forelse($towns as $town)
                                            @if(auth()->user()->can('read'))
                                                <tr onclick="window.location='{{ route('town.show', $town->id) }}';">
                                            @else
                                                <tr>
                                                    @endif
                                                    <td>{{$town->name}}</td>
                                                </tr>
                                                @empty
                                                    <tr>
                                                        <td>нет данных ...</td>
                                                    </tr>
                                                @endforelse
                                        </tbody>
                                    </table>
                                    {{$towns->withQueryString()->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- profile init js -->
        <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
