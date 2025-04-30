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
    <link href="{{asset('assets/css/role-setting.css')}}" rel="stylesheet" type="text/css"/>
    <!-- Bootstrap Css -->
    <link href="{{asset('assets/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css"/>
</head>

<body>

<!-- Begin page -->
<div id="layout-wrapper">

    <header id="page-topbar">
        <div class="layout-width">
            <div class="navbar-header">

            </div>
        </div>
    </header>
    <div class="vertical-overlay"></div>

    <div class="main-content">
        @yield('content')
    </div>

</div>


<!-- JAVASCRIPT -->
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
</body>

</html>
