@extends('layouts.backend.main')

@section('title', 'Главная | Контакты')

@section('content')
    <div class="page-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between shadow">
                        <h4 class="mb-sm-0">Контакты</h4>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <table class="table table-striped table-responsive table-sm table-hover">
                        <thead>
                        <tr>
                            <th scope="col">ФИО</th>
                            <th scope="col" class="d-none d-md-table-cell">Почта</th>
                            <th scope="col" class="d-none d-sm-table-cell">Телефон</th>
                        </tr>
                        </thead>
                        <tbody style="cursor: pointer;">
                        <form action="{{route('contacts.index')}}" method="get">
                            <tr>
                                <td>
                                    @csrf
                                    <input class="form-control form-control-sm" list="user_data_list" type="search"
                                           id="user_id" placeholder="Поиск ..." onchange="this.form.submit();"
                                           name="name" value="{{$old_filters['name'] ?? null}}">
                                    <datalist id="user_data_list">
                                        @forelse($users as $user)
                                            <option value="{{$user->name}}">
                                        @empty
                                            <option value="нет данных ...">
                                        @endforelse
                                    </datalist>
                                </td>
                                <td class="d-none d-md-table-cell">
                                    <input class="form-control form-control-sm" list="email_data_list" type="search"
                                           id="email_id" placeholder="Поиск ..." onchange="this.form.submit();"
                                           name="email" value="{{$old_filters['email'] ?? null}}">
                                    <datalist id="email_data_list">
                                        @forelse($users as $email)
                                            <option value="{{$email->email}}">
                                        @empty
                                            <option value="нет данных ...">
                                        @endforelse
                                    </datalist>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    <input class="form-control form-control-sm" list="phone_data_list" type="search"
                                           id="phone_id" placeholder="Поиск ..." onchange="this.form.submit();"
                                           name="phone" value="{{$old_filters['phone'] ?? null}}">
                                    <datalist id="phone_data_list">
                                        @forelse($users as $phone)
                                            <option value="{{$phone->phone}}">
                                        @empty
                                            <option value="нет данных ...">
                                        @endforelse
                                    </datalist>
                                </td>
                            </tr>
                        </form>
                        @forelse($users as $user)
                            <tr onclick="window.location='{{ route('contacts.show', $user->id) }}';">
                                <td>{{$user->name}}</td>
                                <td class="d-none d-md-table-cell"><a class="nav-link" href="mailto:{{$user->email}}"
                                                                      title="Написать"><span
                                            class=" border-bottom border-dark">{{$user->email}}</span></a></td>
                                <td class="d-none d-sm-table-cell">@if(isset($user->phone))
                                        <a class="nav-link" href="tel:{{$user->phone}}" title="Позвонить"><span
                                                class=" border-bottom border-dark">{{$user->phone}}</span></a>
                                    @endif</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">нет данных ...</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                    {{$users->withQueryString()->links()}}
                </div>
            </div>
        </div>
    </div>
    <!-- profile init js -->
    <script src="{{asset('assets/js/pages/profile.init.js')}}"></script>
@endsection
