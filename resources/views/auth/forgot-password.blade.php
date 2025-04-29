<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
      data-sidebar-image="none" data-preloader="disable">

<head>

    <meta charset="utf-8"/>
    <title>Сброс пароля | FG</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="FG HelpDesk" name="description"/>
    <meta content="kesvesna@rambler.ru" name="author"/>
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{asset('assets/images/favicon.svg')}}">

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
    <!-- custom Css-->
    <link href="{{asset('assets/css/custom.min.css')}}" rel="stylesheet" type="text/css"/>

</head>

<body>

<!-- auth-page wrapper -->
<div class="auth-page-wrapper auth-bg-cover py-3 d-flex justify-content-center align-items-center min-vh-100">
    <div class="bg-overlay"></div>
    <!-- auth-page content -->
    <div class="auth-page-content overflow-hidden pt-lg-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card overflow-hidden">
                        <div class="row justify-content-center g-0">
                            <div class="col-lg-6">
                                <div class="p-lg-5 p-4 auth-one-bg h-100">
                                    <div class="bg-overlay"></div>
                                    <div class="position-relative h-100 d-flex flex-column">
                                        <div class="mb-4">
                                            <a href="#" class="d-block">
                                                <img src="{{asset('assets/images/logo.png')}}" alt="" height="100">
                                            </a>
                                        </div>
                                        <div class="mt-auto">
                                            <div class="mb-3">
                                                <i class="ri-double-quotes-l display-4 text-success"></i>
                                            </div>

                                            <div id="qoutescarouselIndicators" class="carousel slide"
                                                 data-bs-ride="carousel">
                                                <div class="carousel-indicators">
                                                    <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="0" class="active" aria-current="true"
                                                            aria-label="Slide 1"></button>
                                                    <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                    <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                </div>
                                                <div class="carousel-inner text-center text-white-50 pb-5">
                                                    <div class="carousel-item active">
                                                        <p class="fs-15 fst-italic">" Мы далеко видим, потому что стоим
                                                            на плечах гигантов. "</p>
                                                    </div>
                                                    <div class="carousel-item">
                                                        <p class="fs-15 fst-italic">" Цели должны быть неудобными, чтобы
                                                            вы работали. "</p>
                                                    </div>
                                                    <div class="carousel-item">
                                                        <p class="fs-15 fst-italic">" Никогда не ошибается тот, кто
                                                            ничего не делает. "</p>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- end carousel -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->

                            <div class="col-lg-6">
                                <div class="p-lg-5 p-4">
                                    <h5 class="text-primary">Забыли пароль?</h5>
                                    <p class="text-muted">Сделайте сброс.</p>
                                    <div class="text-center">
                                        <lord-icon
                                            src="{{asset('assets/json/rhvddzym.json')}}" trigger="loop"
                                            colors="primary:#0ab39c" class="avatar-xl">
                                        </lord-icon>
                                    </div>
                                    @if(session('status'))
                                        <div class="alert alert-success text-center" role="alert">
                                            {{session('status')}}
                                        </div>
                                    @else
                                        <div class="alert alert-borderless alert-warning text-center mb-2 mx-2"
                                             role="alert">
                                            Отправим Вам ссылку на указанную почту.
                                        </div>
                                    @endif

                                    <div class="p-2">
                                        <form action="{{route('password.email')}}" method="post">
                                            @csrf
                                            <div class="mb-4">
                                                <label class="form-label">Электронная почта</label>
                                                <input type="email" class="form-control" id="email"
                                                       placeholder="i.ivanov@mail.ru" name="email">
                                                @error('email')
                                                <div class="text-danger">{{$message}}</div>
                                                @enderror
                                            </div>
                                            <div class="text-center mt-4">
                                                <button class="btn btn-success w-100" type="submit">Отправить ссылку для
                                                    сброса
                                                </button>
                                            </div>
                                        </form><!-- end form -->
                                    </div>

                                    <div class="mt-3">
                                        <p class="mb-0">Вспомнили пароль? <a href="{{route('login')}}"
                                                                             class="fw-semibold text-primary text-decoration-underline">
                                                Нажмите здесь </a></p>
                                    </div>
                                    <div class="mt-3">
                                        <p class="mb-0">Нет регистрации ? <a href="{{route('register')}}"
                                                                             class="fw-semibold text-primary text-decoration-underline">
                                                Регистрация</a></p>
                                    </div>
                                </div>
                            </div>
                            <!-- end col -->
                        </div>
                        <!-- end row -->
                    </div>
                    <!-- end card -->
                </div>
                <!-- end col -->

            </div>
            <!-- end row -->
        </div>
        <!-- end container -->
    </div>
    <!-- end auth page content -->

    <!-- footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <p class="mb-0">&copy;
                            <script>document.write(new Date().getFullYear())</script>
                            FG. Сделано с <i class="mdi mdi-heart text-danger"></i> инженерами FG.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- end Footer -->
</div>
<!-- end auth-page-wrapper -->

<!-- JAVASCRIPT -->
<script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
<script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>
<script src="{{asset('assets/libs/feather-icons/feather.min.js')}}"></script>
<script src="{{asset('assets/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
<script src="{{asset('assets/js/plugins.js')}}"></script>

<!-- password-addon init -->
<script src="{{asset('assets/js/pages/password-addon.init.js')}}"></script>
</body>

</html>
