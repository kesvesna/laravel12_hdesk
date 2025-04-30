<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
      data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8"/>
    <title>@section('title')@show</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="FG HelpDesk" name="description"/>
    <meta content="kesvesna@rambler.ru" name="author"/>
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.svg')}}">

    <!-- Normalize Css -->
    <link href="{{asset('assets/css/normalize.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Layout config Js -->
    <script src="{{asset('assets/js/layout.js')}}"></script>
    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Flatpickr Css -->
    <link href="{{asset('assets/css/choices.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Choices Css -->
    <link href="{{asset('assets/css/flatpickr.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Icons Css -->
    <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css"/>
    <!-- App Css-->
    <link href="{{asset('assets/css/app.min.css')}}" rel="stylesheet" type="text/css"/>


</head>

<body>

<!-- Begin page -->
<div id="layout-wrapper">

    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">
                <div class="d-flex">

                    <button type="button" class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger"
                            id="topnav-hamburger-icon">
                    <span class="hamburger-icon">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    </button>
                </div>

                <div class="d-flex align-items-center">

{{--                    <div class="dropdown d-md-none topbar-head-dropdown header-item">--}}
{{--                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"--}}
{{--                                id="page-header-search-dropdown" data-bs-toggle="dropdown" aria-haspopup="true"--}}
{{--                                aria-expanded="false">--}}
{{--                            <i class="bx bx-search fs-22"></i>--}}
{{--                        </button>--}}
{{--                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"--}}
{{--                             aria-labelledby="page-header-search-dropdown">--}}
{{--                            <form class="p-3">--}}
{{--                                <div class="form-group m-0">--}}
{{--                                    <div class="input-group">--}}
{{--                                        <input type="text" class="form-control" placeholder="Search ..."--}}
{{--                                               aria-label="Recipient's username">--}}
{{--                                        <button class="btn btn-primary" type="submit"><i class="mdi mdi-magnify"></i>--}}
{{--                                        </button>--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </form>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="ms-1 header-item d-none d-sm-flex">--}}
{{--                        <button type="button" class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle"--}}
{{--                                data-toggle="fullscreen">--}}
{{--                            <i class='bx bx-fullscreen fs-22'></i>--}}
{{--                        </button>--}}
{{--                    </div>--}}

{{--                    <div class="ms-1 header-item d-none d-sm-flex">--}}
{{--                        <button type="button"--}}
{{--                                class="btn btn-icon btn-topbar btn-ghost-secondary rounded-circle light-dark-mode">--}}
{{--                            <i class='bx bx-moon fs-22'></i>--}}
{{--                        </button>--}}
{{--                    </div>--}}


                    <div>
                        <a href="{{route('dashboard.index')}}">
                            <img src="{{asset('assets/images/lord-icons/home.gif')}}" alt="home page" title="На главную" height="35">
                        </a>
                    </div>

                    <div class="dropdown ms-sm-3 header-item topbar-user">
                        <button type="button" class="btn" id="page-header-user-dropdown" data-bs-toggle="dropdown"
                                aria-haspopup="true" aria-expanded="false">
                        <span class="d-flex align-items-center">
                            <img class="rounded-circle header-profile-user"
                                 src="{{asset('assets/images/backend/svg/default-avatar.svg')}}" alt="Avatar">
                            <span class="text-start ms-xl-2">
                                <span
                                    class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">{{Auth::user()->name}}</span>
                                 <span
                                     class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">{{Auth::user()->division->name ?? 'не выбрано'}}</span>
                                <span
                                    class="d-none d-xl-block ms-1 fs-12 text-muted user-name-sub-text">{{Auth::user()->function->name ?? 'не выбрано'}}</span>
                            </span>
                        </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end">
                            <!-- item-->
                            <h6 class="dropdown-header">Здравствуйте {{Auth::user()->name}}!</h6>
                            <a class="dropdown-item" href="{{route('profile.index')}}"><i
                                    class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                    class="align-middle">Профиль</span></a>
                            <a class="dropdown-item" href="{{route('settings.index')}}"><i
                                    class="mdi mdi-account-wrench-outline text-muted fs-16 align-middle me-1"></i> <span
                                    class="align-middle">Настройки</span></a>
                            <div class="dropdown-divider"></div>
                            <form action="{{route('logout')}}" method="post" class="ps-1">
                                @csrf
                                <button class="dropdown-item btn" type="submit"><i
                                        class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle" data-key="t-logout">Выйти</span></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- ========== App Menu ========== -->
    <div class="app-menu navbar-menu">
        <!-- LOGO -->
        <div class="navbar-brand-box">
            <!-- Dark Logo-->
            <a href="{{route('dashboard.index')}}" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="{{asset('assets/images/lord-icons/home.gif')}}" alt="" height="35">
                    </span>
                <span class="logo-lg">
                        <img src="{{asset('assets/images/lord-icons/home.gif')}}" alt="" height="35">
                    </span>
            </a>
            <!-- Light Logo-->
            <a href="{{route('dashboard.index')}}" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="{{asset('assets/images/lord-icons/home.gif')}}" alt="" height="35">
                    </span>
                <span class="logo-lg">
                        <img src="{{asset('assets/images/lord-icons/home.gif')}}" alt="" height="35">
                    </span>
            </a>
            <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                <i class="ri-record-circle-line"></i>
            </button>
        </div>

        <div id="scrollbar">
            <div class="container-fluid">

                <div id="two-column-menu">
                </div>
                <ul class="navbar-nav" id="navbar-nav">

                    @if(auth()->user()->division->name != 'Арендатор')
                    <li class="menu-title"><span data-key="t-menu">Эксплуатация</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarActivities" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarDashboards">
                            <i class="ri-file-2-line"></i><span data-key="t-dashboards">Делопроизводство</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarActivities">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="#sidebarActs" class="nav-link" data-bs-toggle="collapse" role="button"
                                       aria-expanded="false" aria-controls="sidebarAccount" data-key="t-level-1.2">
                                        Акты
                                    </a>
                                    <div class="collapse menu-dropdown" id="sidebarActs">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{route('avrs.index')}}"
                                                   class="nav-link  {{ request()->routeIs('avrs.*')?'active':'' }}"><span> Выполненных работ </span></a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{route('tech_acts.index')}}"
                                                   class="nav-link  {{ request()->routeIs('tech_acts.*')?'active':'' }}"
                                                   data-key="t-level-2.1"> Технические </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a href="#sidebarApplications" class="nav-link" data-bs-toggle="collapse"
                                       role="button" aria-expanded="false" aria-controls="sidebarAccount"
                                       data-key="t-level-1.2">
                                        Заявки
                                    </a>
                                    <div class="collapse menu-dropdown" id="sidebarApplications">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{route('operation_applications.index')}}"
                                                   class="nav-link {{ request()->routeIs('operation_applications.*')?'active':'' }}"
                                                   data-key="t-analytics"> В эксплуатацию </a>
                                            </li>
                                            @role('sadmin')
                                            <li class="nav-item">
                                                <a href="#sidebarAdminApplications" class="nav-link"
                                                   data-bs-toggle="collapse" role="button" aria-expanded="false"
                                                   aria-controls="sidebarCrm" data-key="t-level-2.2"> В администрацию
                                                </a>
                                                <div class="collapse menu-dropdown" id="sidebarAdminApplications">
                                                    <ul class="nav nav-sm flex-column">
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1"> На--}}
{{--                                                                работы </a>--}}
{{--                                                        </li>--}}
                                                        <li class="nav-item">
                                                            <a href="{{route('admin_app_good_moves.index')}}" class="nav-link {{ request()->routeIs('admin_app_good_moves.*')?'active':'' }}" data-key="t-level-2.1">
                                                                Ввоз/вывоз </a>
                                                        </li>
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1">--}}
{{--                                                                Инженерная </a>--}}
{{--                                                        </li>--}}
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1"> От--}}
{{--                                                                подрядчика </a>--}}
{{--                                                        </li>--}}
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1">--}}
{{--                                                                Магнитная карта </a>--}}
{{--                                                        </li>--}}
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1">--}}
{{--                                                                Промоакция </a>--}}
{{--                                                        </li>--}}
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1"> Список--}}
{{--                                                                сотрудников </a>--}}
{{--                                                        </li>--}}
                                                    </ul>
                                                </div>
                                            </li>
                                            @endrole
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('tasks.index')}}"
                                       class="nav-link  {{ request()->routeIs('tasks.*')?'active':'' }}"><span>Задачи</span></a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('orders.index')}}" class="nav-link {{ request()->routeIs('orders.*')?'active':'' }}"><span> Заказы запчастей </span></a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('trk_repairs.index')}}"
                                       class="nav-link  {{ request()->routeIs('trk_repairs.*')?'active':'' }}"><span> Ремонт </span></a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('equipment_work_periods.index')}}"
                                       class="nav-link  {{ request()->routeIs('equipment_work_periods.*')?'active':'' }}"><span> Тех. мероприятия </span></a>
                                </li>
                                <li class="nav-item">
                                    <a href="#sidebarCheckLists" class="nav-link" data-bs-toggle="collapse"
                                       role="button" aria-expanded="false" aria-controls="sidebarAccount"
                                       data-key="t-level-1.2">
                                        Чек листы
                                    </a>
                                    <div class="collapse menu-dropdown" id="sidebarCheckLists">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{route('trk_room_climates.index')}}"
                                                   class="nav-link {{ request()->routeIs('trk_room_climates.*')?'active':'' }}"
                                                   data-key="t-nft"> Климат </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{route('checklists_conditioner.index')}}"
                                                   class="nav-link  {{ request()->routeIs('checklists_conditioner.*')?'active':'' }}"
                                                   data-key="t-analytics"> Кондиционеры </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{route('checklists_fancoil.index')}}"
                                                   class="nav-link  {{ request()->routeIs('checklists_fancoil.*')?'active':'' }}"
                                                   data-key="t-analytics"> Фанкойлы </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{route('checklists_balk.index')}}"
                                                   class="nav-link  {{ request()->routeIs('checklists_balk.*')?'active':'' }}"
                                                   data-key="t-analytics"> Балки </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{route('checklists_air_diffuser.index')}}"
                                                   class="nav-link  {{ request()->routeIs('checklists_air_diffuser.*')?'active':'' }}"
                                                   data-key="t-analytics"> Диффузоры </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{route('checklists_air_duct.index')}}"
                                                   class="nav-link  {{ request()->routeIs('checklists_air_duct.*')?'active':'' }}"
                                                   data-key="t-analytics"> Воздуховоды </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{route('checklists_air_supply.index')}}"
                                                   class="nav-link  {{ request()->routeIs('checklists_air_supply.*')?'active':'' }}"
                                                   data-key="t-analytics"> Приточные установки </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{route('checklists_air_extract.index')}}"
                                                   class="nav-link  {{ request()->routeIs('checklists_air_extract.*')?'active':'' }}"
                                                   data-key="t-analytics"> Вытяжки </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarTrkParts" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarDashboards">
                            <i class="ri-building-2-line"></i><span data-key="t-dashboards">ТРК</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarTrkParts">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{route('renter_trk_room_brands.index')}}"
                                       class="nav-link  {{ request()->routeIs('renter_trk_room_brands.*')?'active':'' }}"
                                       data-key="t-analytics"> Арендаторы </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('trk_equipments.index')}}"
                                       class="nav-link {{ request()->routeIs('trk_equipments.*')?'active':'' }}"
                                       data-key="t-analytics"> Оборудование </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('room_checks.index')}}"
                                       class="nav-link {{ request()->routeIs('room_checks.*')?'active':'' }}">
                                        Обходы </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#sidebarStoreHouse" class="nav-link" data-bs-toggle="collapse"
                                       role="button" aria-expanded="false" aria-controls="sidebarStoreHouse"
                                       data-key="t-level-1.2"> Склады
                                    </a>
                                    <div class="collapse menu-dropdown" id="sidebarStoreHouse">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{route('trk_store_houses.index')}}"
                                                   class="nav-link {{ request()->routeIs('trk_store_houses.*')?'active':'' }}"
                                                   data-key="t-level-2.1"> Запчасти </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{route('trk_store_house_users.index')}}"
                                                   class="nav-link {{ request()->routeIs('trk_store_house_users.*')?'active':'' }}"
                                                   data-key="t-level-2.1"> Доступ </a>
                                            </li>
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('trk_room_counters.index')}}"
                                       class="nav-link {{ request()->routeIs('trk_room_counters.*')?'active':'' }}">
                                        Счетчики </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('trk_room.index')}}"
                                       class="nav-link {{ request()->routeIs('trk_room.*')?'active':'' }}"
                                       data-key="t-nft"> Помещения </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarReports" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-bar-chart-horizontal-line"></i><span data-key="t-dashboards">Отчеты</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarReports">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#sidebarEmployeeReports" class="nav-link" data-bs-toggle="collapse" role="button"
                                           aria-expanded="false" aria-controls="sidebarEmployeeReports" data-key="t-level-1.2">
                                            Сотрудник
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarEmployeeReports">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{route('employee_reports.general_report.index')}}"
                                                       class="nav-link  {{ request()->routeIs('employee_reports.general_report.*')?'active':'' }}"><span> Общий </span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('employee_reports.operation_application.index')}}"
                                                       class="nav-link  {{ request()->routeIs('employee_reports.operation_application.*')?'active':'' }}"
                                                       data-key="t-level-2.1"> Заявки </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('employee_reports.avrs.index')}}"
                                                       class="nav-link  {{ request()->routeIs('employee_reports.avrs.*')?'active':'' }}"
                                                       data-key="t-level-2.1"> Акты выполненных работ </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#sidebarDivisionReports" class="nav-link" data-bs-toggle="collapse" role="button"
                                           aria-expanded="false" aria-controls="sidebarDivisionReports" data-key="t-level-1.2">
                                            Подразделение
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarDivisionReports">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{route('division_reports.all_trk.index')}}"
                                                       class="nav-link  {{ request()->routeIs('division_reports.all_trk.*')?'active':'' }}"><span> Все ТРК </span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('division_reports.employees.index')}}"
                                                       class="nav-link  {{ request()->routeIs('division_reports.employees.*')?'active':'' }}"><span> Сотрудники </span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#sidebarTrkReports" class="nav-link" data-bs-toggle="collapse" role="button"
                                           aria-expanded="false" aria-controls="sidebarTrkReports" data-key="t-level-1.2">
                                            ТРК
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarTrkReports">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{route('trk_reports.general_report.index')}}"
                                                       class="nav-link  {{ request()->routeIs('trk_reports.general_report.*')?'active':'' }}"><span> Общий </span></a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('trk_reports.operation_application.index')}}"
                                                       class="nav-link  {{ request()->routeIs('trk_reports.operation_application.*')?'active':'' }}"
                                                       data-key="t-level-2.1"> Заявки </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('trk_reports.repair.index')}}"
                                                       class="nav-link  {{ request()->routeIs('trk_reports.repair.*')?'active':'' }}"
                                                       data-key="t-level-2.1"> Ремонт </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarStatistics" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarStatistics">
                                <i class="ri-calculator-line"></i><span data-key="t-dashboards">Статистика</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarStatistics">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#sidebarTrkStatistics" class="nav-link" data-bs-toggle="collapse" role="button"
                                           aria-expanded="false" aria-controls="sidebarTrkStatistics" data-key="t-level-1.2">
                                            ТРК
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarTrkStatistics">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{route('trk_statistics.spare_parts.index')}}"
                                                       class="nav-link  {{ request()->routeIs('trk_statistics.spare_parts.*')?'active':'' }}"><span> Запчасти </span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarSoft" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-file-info-line"></i><span data-key="t-dashboards">Софт в помощь</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarSoft">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#sidebarAir" class="nav-link" data-bs-toggle="collapse"
                                           role="button" aria-expanded="false" aria-controls="sidebarAir"
                                           data-key="t-level-1.2">
                                            Воздух
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarAir">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{url('useful_soft/air/air_flow')}}"
                                                       class="nav-link"
                                                       data-key="t-analytics"> Расход </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @if(Auth::user()->hasRole('sadmin'))
                            <li class="nav-item">
                                <a class="nav-link menu-link" href="#sidebarDispatcher" data-bs-toggle="collapse" role="button"
                                   aria-expanded="false" aria-controls="sidebarDispatcher">
                                    <i class="ri-user-2-line"></i><span data-key="t-dashboards">Диспетчеризация</span>
                                </a>
                                <div class="collapse menu-dropdown" id="sidebarDispatcher">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a href="{{route('dispatcher_evropolis.index')}}"
                                               class="nav-link {{ request()->routeIs('dispatcher_evropolis.*')?'active':'' }}"
                                               data-key="t-analytics"> Европолис </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarPeoples" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarDashboards">
                            <i class="ri-user-2-line"></i><span data-key="t-dashboards">Персонал</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarPeoples">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{route('user_time_sheets.index')}}"
                                       class="nav-link {{ request()->routeIs('user_time_sheets.*')?'active':'' }}"
                                       data-key="t-analytics"> Табель </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('user_vacations.index')}}"
                                       class="nav-link {{ request()->routeIs('user_vacations.*')?'active':'' }}"
                                       data-key="t-nft"> Отпуск </a>
                                </li>
{{--                                <li class="nav-item">--}}
{{--                                    <a href="#" class="nav-link" data-key="t-nft"> Дежурство </a>--}}
{{--                                </li>--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a href="#" class="nav-link" data-key="t-nft"> График </a>--}}
{{--                                </li>--}}
{{--                                <li class="nav-item">--}}
{{--                                    <a href="{{route('contacts.index')}}"--}}
{{--                                       class="nav-link {{ request()->routeIs('contacts.*')?'active':'' }}"--}}
{{--                                       data-key="t-nft"> Контакты </a>--}}
{{--                                </li>--}}
                            </ul>
                        </div>
                    </li>
                    @endif

                        <li class="nav-item">
                            <a href="{{route('contacts.index')}}"
                               class="nav-link menu-link {{ request()->routeIs('contacts.*')?'active':'' }}">
                                <i class="ri-phone-line"></i>
                                <span data-key="t-widgets">
                                Контакты
                                </span>
                            </a>
                        </li>
                    @endif

                    @if(auth()->user()->division->name == \App\Models\UserDivisions\UserDivision::RENTER || auth()->user()->hasRole('sadmin'))
                    <li class="menu-title"><span data-key="t-menu"></span></li>
                    <li class="menu-title"><span data-key="t-menu">Аренда</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarActivities" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarDashboards">
                            <i class="ri-file-2-line"></i><span data-key="t-dashboards">Делопроизводство</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarActivities">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="#sidebarApplications" class="nav-link" data-bs-toggle="collapse"
                                       role="button" aria-expanded="false" aria-controls="sidebarAccount"
                                       data-key="t-level-1.2">
                                        Заявки
                                    </a>
                                    <div class="collapse menu-dropdown" id="sidebarApplications">
                                        <ul class="nav nav-sm flex-column">
                                            <li class="nav-item">
                                                <a href="{{route('operation_applications.index')}}"
                                                   class="nav-link {{ request()->routeIs('operation_applications.*')?'active':'' }}"
                                                   data-key="t-analytics"> В эксплуатацию </a>
                                            </li>
{{--                                            <li class="nav-item">--}}
{{--                                                <a href="#sidebarAdminApplications" class="nav-link"--}}
{{--                                                   data-bs-toggle="collapse" role="button" aria-expanded="false"--}}
{{--                                                   aria-controls="sidebarCrm" data-key="t-level-2.2"> В администрацию--}}
{{--                                                </a>--}}
{{--                                                <div class="collapse menu-dropdown" id="sidebarAdminApplications">--}}
{{--                                                    <ul class="nav nav-sm flex-column">--}}
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1"> На--}}
{{--                                                                работы </a>--}}
{{--                                                        </li>--}}
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="{{route('admin_app_good_moves.index')}}" class="nav-link {{ request()->routeIs('admin_app_good_moves.*')?'active':'' }}" data-key="t-level-2.1">--}}
{{--                                                                Ввоз/вывоз </a>--}}
{{--                                                        </li>--}}
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1">--}}
{{--                                                                Инженерная </a>--}}
{{--                                                        </li>--}}
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1"> От--}}
{{--                                                                подрядчика </a>--}}
{{--                                                        </li>--}}
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1">--}}
{{--                                                                Магнитная карта </a>--}}
{{--                                                        </li>--}}
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1">--}}
{{--                                                                Промоакция </a>--}}
{{--                                                        </li>--}}
{{--                                                        <li class="nav-item">--}}
{{--                                                            <a href="#" class="nav-link" data-key="t-level-2.1"> Список--}}
{{--                                                                сотрудников </a>--}}
{{--                                                        </li>--}}
{{--                                                    </ul>--}}
{{--                                                </div>--}}
{{--                                            </li>--}}
                                        </ul>
                                    </div>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('tasks.index')}}"
                                       class="nav-link  {{ request()->routeIs('tasks.*')?'active':'' }}"><span>Задачи</span></a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarTrkParts" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarDashboards">
                            <i class="ri-building-2-line"></i><span data-key="t-dashboards">ТРК</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarTrkParts">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{route('trk_room_counters.index')}}"
                                       class="nav-link {{ request()->routeIs('trk_room_counters.*')?'active':'' }}">
                                        Счетчики </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('trk_room.index')}}"
                                       class="nav-link {{ request()->routeIs('trk_room.*')?'active':'' }}"
                                       data-key="t-nft"> Помещения </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link menu-link {{ request()->routeIs('renter_memos.*')?'active':'' }}" href="{{route('renter_memos.index')}}">
                            <i class="ri-phone-line"></i> <span data-key="t-widgets">Контакты</span>
                        </a>
                    </li>
                    @endif

                        @role('sadmin')
                    <li class="menu-title"><span data-key="t-menu"></span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarSiteFunctions" data-bs-toggle="collapse"
                           role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                            <i class="ri-task-line"></i><span data-key="t-dashboards">Функционал сайта</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarSiteFunctions">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{route('user_bug_reports.index')}}"
                                       class="nav-link {{ request()->routeIs('user_bug_reports.*')?'active':'' }}"
                                       data-key="t-analytics"> Баги </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('user_wishes.index')}}"
                                       class="nav-link {{ request()->routeIs('user_wishes.*')?'active':'' }}"
                                       data-key="t-nft"> Пожелания </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                        @endrole
                    <li class="menu-title"><span data-key="t-menu"></span></li>
                    <li class="menu-title"><span data-key="t-menu"></span></li>
                    @if(auth()->user()->hasAnyRole('admin', 'sadmin'))
                        <li class="menu-title"><span data-key="t-menu">Админ</span></li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse"
                               role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-building-4-line"></i><span data-key="t-dashboards">Архитектура сайта</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarDashboards">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#names" class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAccount" data-key="t-level-1.2"> Названия
                                        </a>
                                        <div class="collapse menu-dropdown" id="names">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{route('town.index')}}"
                                                       class="nav-link {{ request()->routeIs('town.*')?'active':'' }}"
                                                       data-key="t-analytics"> Города </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('trk.index')}}"
                                                       class="nav-link {{ request()->routeIs('trk.*')?'active':'' }}"
                                                       data-key="t-crm"> Торговые комплексы </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('building.index')}}"
                                                       class="nav-link {{ request()->routeIs('building.*')?'active':'' }}"
                                                       data-key="t-ecommerce"> Блоки/Зоны </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('floor.index')}}"
                                                       class="nav-link {{ request()->routeIs('floor.*')?'active':'' }}"
                                                       data-key="t-crypto"> Этажи/Отметки </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('axe.index')}}"
                                                       class="nav-link {{ request()->routeIs('axe.*')?'active':'' }}"
                                                       data-key="t-projects"> Оси </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('room.index')}}"
                                                       class="nav-link {{ request()->routeIs('room.*')?'active':'' }}"
                                                       data-key="t-nft"> Помещения </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('store_house_names.index')}}"
                                                       class="nav-link {{ request()->routeIs('store_house_names.*')?'active':'' }}"
                                                       data-key="t-analytics"> Склады </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('spare_part_names.index')}}"
                                                       class="nav-link {{ request()->routeIs('spare_part_names.*')?'active':'' }}"
                                                       data-key="t-nft"> Запчасти </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('organization.index')}}"
                                                       class="nav-link  {{ request()->routeIs('organization.*')?'active':'' }}"
                                                       data-key="t-analytics"> Организации </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('brands.index')}}"
                                                       class="nav-link  {{ request()->routeIs('brands.*')?'active':'' }}"
                                                       data-key="t-analytics"> Бренды </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('equipment_names.index')}}"
                                                       class="nav-link {{ request()->routeIs('equipment_names.*')?'active':'' }}"
                                                       data-key="t-nft"> Оборудование </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('work_names.index')}}"
                                                       class="nav-link {{ request()->routeIs('work_names.*')?'active':'' }}"
                                                       data-key="t-nft"> Типы работ </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('parameter_names.index')}}"
                                                       class="nav-link {{ request()->routeIs('parameter_names.*')?'active':'' }}"
                                                       data-key="t-nft"> Параметры </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('equipment_statuses.index')}}"
                                                       class="nav-link {{ request()->routeIs('equipment_statuses.*')?'active':'' }}"
                                                       data-key="t-nft"> Статусы оборудования </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('equipment_users.index')}}"
                                                       class="nav-link {{ request()->routeIs('equipment_users.*')?'active':'' }}"
                                                       data-key="t-nft"> Потребители оборудования </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('room_purposes.index')}}"
                                                       class="nav-link {{ request()->routeIs('room_purposes.*')?'active':'' }}"
                                                       data-key="t-nft"> Назначение помещений </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('resume_names.index')}}"
                                                       class="nav-link {{ request()->routeIs('resume_names.*')?'active':'' }}"
                                                       data-key="t-nft"> Решения комиссии </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('order_statuses.index')}}"
                                                       class="nav-link {{ request()->routeIs('order_statuses.*')?'active':'' }}"
                                                       data-key="t-nft"> Статусы заказов </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('admin_app_statuses.index')}}"
                                                       class="nav-link {{ request()->routeIs('admin_app_statuses.*')?'active':'' }}"
                                                       data-key="t-nft"> Статусы заявок админам </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('tare_types.index')}}"
                                                       class="nav-link {{ request()->routeIs('tare_types.*')?'active':'' }}"
                                                       data-key="t-nft"> Типы тары </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('renter_memo_divisions.index')}}"
                                                       class="nav-link {{ request()->routeIs('renter_memo_divisions.*')?'active':'' }}"
                                                       data-key="t-nft"> Подразделения для памятки арендатора </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link menu-link" href="#sidebarCountersParts"
                                           data-bs-toggle="collapse" role="button" aria-expanded="false"
                                           aria-controls="sidebarMultilevel">
                                            <span data-key="t-multi-level">Счетчики</span>
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarCountersParts">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{route('counter_counts.index')}}"
                                                       class="nav-link {{ request()->routeIs('counter_counts.*')?'active':'' }}"
                                                       data-key="t-level-1.1"> Показания </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('tariff_names.index')}}"
                                                       class="nav-link {{ request()->routeIs('tariff_names.*')?'active':'' }}"
                                                       data-key="t-level-1.1"> Тарифы </a>
                                                </li>
                                                <li class="nav-item">
                                                    <a href="{{route('counter_types.index')}}"
                                                       class="nav-link {{ request()->routeIs('counter_types.*')?'active':'' }}"
                                                       data-key="t-level-1.1"> Типы </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="menu-title"><span data-key="t-menu"></span></li>
                    @endcan
                    @role('sadmin')
                    <li class="menu-title"><span data-key="t-menu">Сисадмин</span></li>
                    <li class="nav-item">
                        <a class="nav-link menu-link" href="#sidebarUsers" data-bs-toggle="collapse" role="button"
                           aria-expanded="false" aria-controls="sidebarUsers">
                            <i class="ri-user-2-line"></i><span data-key="t-dashboards">Пользователи</span>
                        </a>
                        <div class="collapse menu-dropdown" id="sidebarUsers">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{route('profile.all')}}"
                                       class="nav-link  {{ request()->routeIs('profile.*')?'active':'' }}"
                                       data-key="t-analytics"> Профили </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('roles.index')}}"
                                       class="nav-link  {{ request()->routeIs('roles.*')?'active':'' }}"
                                       data-key="t-analytics"> Роли </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('permissions.index')}}"
                                       class="nav-link  {{ request()->routeIs('permissions.*')?'active':'' }}"
                                       data-key="t-analytics"> Права </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route('user_division_functions.index')}}"
                                       class="nav-link  {{ request()->routeIs('user_division_functions.*')?'active':'' }}"
                                       data-key="t-analytics"> Подразделение/Должность </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarSoft" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarDashboards">
                                <i class="ri-file-2-line"></i><span data-key="t-dashboards">Софт в помощь</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarSoft">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#sidebarShop" class="nav-link" data-bs-toggle="collapse" role="button"
                                           aria-expanded="false" aria-controls="sidebarAccount" data-key="t-level-1.2">
                                            Покупки
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarShop">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{url('useful_soft/shopping/one_kilo_price')}}"
                                                       class="nav-link"><span>Цена за килограмм</span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#sidebarAir" class="nav-link" data-bs-toggle="collapse"
                                           role="button" aria-expanded="false" aria-controls="sidebarAir"
                                           data-key="t-level-1.2">
                                            Воздух
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarAir">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{url('useful_soft/air/air_flow')}}"
                                                       class="nav-link"
                                                       data-key="t-analytics"> Расход </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarStatistic" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarStatistic">
                                <i class="ri-file-2-line"></i><span data-key="t-dashboards">Статистика</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarStatistic">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#sidebarAirFilters" class="nav-link" data-bs-toggle="collapse" role="button"
                                           aria-expanded="false" aria-controls="sidebarAirFilters" data-key="t-level-1.2">
                                            Замена фильтров
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarAirFilters">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{route('avrs.index')}}"
                                                       class="nav-link  {{ request()->routeIs('avrs.*')?'active':'' }}"><span> Приточные фильтры </span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                    <li class="nav-item">
                                        <a href="#sidebarSparePart" class="nav-link" data-bs-toggle="collapse" role="button"
                                           aria-expanded="false" aria-controls="sidebarSparePart" data-key="t-level-1.2">
                                            Запчасти
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarSparePart">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{route('avrs.index')}}"
                                                       class="nav-link  {{ request()->routeIs('avrs.*')?'active':'' }}"><span> Использование запчастей </span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarAnalitic" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarAnalitic">
                                <i class="ri-file-2-line"></i><span data-key="t-dashboards">Аналитика</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarAnalitic">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="#sidebarAnalitic1" class="nav-link" data-bs-toggle="collapse" role="button"
                                           aria-expanded="false" aria-controls="sidebarAnalitic1" data-key="t-level-1.2">
                                            Аналитика 1
                                        </a>
                                        <div class="collapse menu-dropdown" id="sidebarAnalitic1">
                                            <ul class="nav nav-sm flex-column">
                                                <li class="nav-item">
                                                    <a href="{{route('avrs.index')}}"
                                                       class="nav-link  {{ request()->routeIs('avrs.*')?'active':'' }}"><span> Аналитика 1.1 </span></a>
                                                </li>
                                            </ul>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarParsers" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarParsers">
                                <i class="ri-user-2-line"></i><span data-key="t-dashboards">Парсер</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarParsers">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('parse.index')}}"
                                           class="nav-link  {{ request()->routeIs('parse.*')?'active':'' }}"
                                           data-key="t-analytics"> Парсер старых баз данных</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link" href="#sidebarLogs" data-bs-toggle="collapse" role="button"
                               aria-expanded="false" aria-controls="sidebarLogs">
                                <i class="ri-user-2-line"></i><span data-key="t-dashboards">Логи</span>
                            </a>
                            <div class="collapse menu-dropdown" id="sidebarLogs">
                                <ul class="nav nav-sm flex-column">
                                    <li class="nav-item">
                                        <a href="{{route('operation_applications_log.index')}}"
                                           class="nav-link  {{ request()->routeIs('operation_applications_log.*')?'active':'' }}"
                                           data-key="t-analytics"> Логи заявок</a>
                                    </li>
                                </ul>
                            </div>
                        </li>
                    @endrole
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('profile.*')?'active':'' }}" href="{{route('profile.index')}}">
                                <i class="ri-user-line"></i> <span data-key="t-widgets">Профиль</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link {{ request()->routeIs('settings.*')?'active':'' }}" href="{{route('settings.index')}}">
                                <i class="ri-settings-line"></i> <span data-key="t-widgets">Настройки</span>
                            </a>
                        </li>
                </ul>
            </div>
            <!-- Sidebar -->
        </div>

        <div class="sidebar-background"></div>
    </div>
    <!-- Left Sidebar End -->
    <!-- Vertical Overlay-->
    <div class="vertical-overlay"></div>

    <div class="main-content">
        @yield('content')
    </div>

</div>


<!-- JAVASCRIPT -->
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>
<script src="{{asset('assets/libs/feather-icons/feather.min.js')}}"></script>
<script src="{{asset('assets/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
<script src="{{asset('assets/js/plugins.js')}}"></script>

<!-- App js -->
<script src="{{asset('assets/js/app.js')}}"></script>
</body>

</html>
