<!doctype html>
<html lang="en">

<head>

    <meta charset="utf-8"/>
    <title>Регистрация | FG</title>
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
<div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
    <div class="bg-overlay"></div>
    <!-- auth-page content -->
    <div class="auth-page-content overflow-hidden pt-lg-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card overflow-hidden m-0">
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

                            <div class="col-lg-6">
                                <div class="p-lg-5 p-4">
                                    <div>
                                        <h5 class="text-primary">Регистрация пользователя</h5>
                                    </div>

                                    <div class="mt-4">
                                        <form class="needs-validation" novalidate action="{{route('register')}}"
                                              method="post">
                                            @csrf
                                            <div class="mb-3">
                                                <label for="email" class="form-label">Электронная почта <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="email"
                                                       placeholder="i.ivanov@mail.ru" required name="email"
                                                       value="{{old('email')}}">
                                                <div class="invalid-feedback">
                                                    Заполните почту
                                                </div>
                                                @error('email')
                                                <span class="text-danger"><strong>{{$message}}</strong></span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="name" class="form-label">Фамилия И.О. <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="name"
                                                       placeholder="Иванов И.И." required name="name"
                                                       value="{{old('name')}}">
                                                <div class="invalid-feedback">
                                                    Заполните Фамилия И.О.
                                                </div>
                                                @error('name')
                                                <span class="text-danger"><strong>{{$message}}</strong></span>
                                                @enderror
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label" for="password">Пароль</label>
                                                <div class="position-relative auth-pass-inputgroup">
                                                    <input name="password" type="password"
                                                           class="form-control pe-5 password-input"
                                                           onpaste="return false" placeholder="Пароль"
                                                           id="password-input" aria-describedby="passwordInput"
                                                           pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                                    <button
                                                        class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon"
                                                        type="button" id="password-addon"><i
                                                            class="ri-eye-fill align-middle"></i></button>
                                                    <div class="invalid-feedback">
                                                        Заполните пароль
                                                    </div>
                                                    @error('password')
                                                    <span class="text-danger"><strong>{{$message}}</strong></span>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                                <h5 class="fs-13">Должно быть:</h5>
                                                <p id="pass-length" class="invalid fs-12 mb-2">Минимум <b>8 символов</b>
                                                </p>
                                                <p id="pass-lower" class="invalid fs-12 mb-2">Латиница <b>нижнем
                                                        регистре</b> (a-z)</p>
                                                <p id="pass-upper" class="invalid fs-12 mb-2">Латиница <b>верхнем
                                                        регистре</b> (A-Z)</p>
                                                <p id="pass-number" class="invalid fs-12 mb-0"><b>Цифры</b> (0-9)</p>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit">Регистрация</button>
                                            </div>

                                        </form>
                                    </div>

                                    <div class="mt-5 text-center">
                                        <p class="mb-0">Уже есть регистрация ? <a href="{{route('login')}}"
                                                                                  class="fw-semibold text-primary text-decoration-underline">
                                                Войти</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>
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
<script src="{{asset('assets/js/toastify.js')}}"></script>
<script src="{{asset('assets/js/flatpickr.js')}}"></script>
<script src="{{asset('assets/js/choices.min.js')}}"></script>
<script src="{{asset('assets/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
<script src="{{asset('assets/js/plugins.js')}}"></script>

<!-- validation init -->
<script src="{{asset('assets/js/pages/form-validation.init.js')}}"></script>
<!-- password create init -->
<script src="{{asset('assets/js/pages/password-create.init.js')}}"></script>
</body>

</html>
