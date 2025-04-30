<nav class="navbar fixed-bottom" style="
                                        background: rgba(248, 236, 255, 0.8);
                                        border-radius: 1px;
                                        border: 1px solid rgba(255, 255, 255, 0.4);">
    <div class="container-fluid">

        <div class="container">
            <div class="row row-cols-5">
                <div class="col">
                    <a class="btn col-12" href="{{route('site.index')}}">
                        <img src="{{asset('assets/images/lord-icons/home.gif')}}" alt="Home picture" width="45"
                             height="45" title="На главную">
                    </a>
                </div>
                <div class="col">
                    <button class="btn col-12" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasBottom" aria-controls="offcanvasBottom"
                            title="Заявки, задачи, ремонт, акты, оборудование, ХОВС">
                        <!--                <span class="navbar-toggler-icon"></span>-->
                        <img src="{{asset('assets/images/lord-icons/tool.gif')}}" alt="Equipment picture" width="45"
                             height="45">
                    </button>
                </div>
                <div class="col">
                    <button class="btn col-12" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasBottom2" aria-controls="offcanvasBottom2">
                        <!--                <span class="navbar-toggler-icon"></span>-->
                        <img src="{{asset('assets/images/lord-icons/city-hall.gif')}}" alt="Architecture picture"
                             width="45" height="45" title="Помещения, арендаторы, счетчики">
                    </button>
                </div>
                <div class="col">
                    <button class="btn col-12" type="button" data-bs-toggle="offcanvas"
                            data-bs-target="#offcanvasBottom3" aria-controls="offcanvasBottom3">
                        <!--                <span class="navbar-toggler-icon"></span>-->
                        <img src="{{asset('assets/images/lord-icons/box.gif')}}" alt="Storage picture" width="45"
                             height="45" title="Склад, запчасти">
                    </button>
                </div>
                <div class="col pt-1">
                    <button class="btn col-12" onclick="history.back()">
                        <img src="{{asset('assets/images/lord-icons/arrow-back.gif')}}" alt="Home picture" width="35"
                             height="35" title="Назад">
                    </button>
                </div>
            </div>
        </div>
        <div class="offcanvas offcanvas-bottom pt-3" tabindex="-1" id="offcanvasBottom"
             aria-labelledby="offcanvasBottomLabel"
             style="
             background: rgba(243, 224, 255, 0.9);
             border-radius: 1px;
             border: 1px solid rgba(255, 255, 255, 0.4);
             height: 60vh;">
            <div class="col-12 text-center mb-1">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <a href="#" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">Заявки</a>
            <a href="#" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">Задачи</a>
            <a href="#" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">Ремонт</a>
            <a href="#" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">Акты</a>
            <a href="#" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">Оборудование</a>
            <a href="#" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">ХОВС</a>
            <a href="#" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">Склад</a>
            <a href="#" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">Заказы</a>
        </div>
        <div class="offcanvas offcanvas-bottom pt-3" tabindex="-1" id="offcanvasBottom2"
             aria-labelledby="offcanvasBottomLabel2"
             style="
             background: rgba(243, 224, 255, 0.9);
             border-radius: 1px;
             border: 1px solid rgba(255, 255, 255, 0.4);
             height: 35vh;">
            <div class="col-12 text-center mb-1">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <a href="#" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">Арендаторы</a>
            <a href="#" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">Помещения</a>
            <a href="#" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">Счетчики</a>
        </div>
        <div class="offcanvas offcanvas-bottom pt-3" tabindex="-1" id="offcanvasBottom3"
             aria-labelledby="offcanvasBottomLabel3"
             style="
             background: rgba(243, 224, 255, 0.9);
             border-radius: 1px;
             border: 1px solid rgba(255, 255, 255, 0.4);
             height: 25vh;">
            <div class="col-12 text-center mb-1">
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <a href="{{route('dashboard.index')}}" class="btn d-block" style="font-size: 1.4em; font-weight: 600;">Админ
                панель</a>
            <form action="{{route('logout')}}" method="post" class="text-center">
                @csrf
                <input class="btn" onclick="this.form.submit();" value="Выйти"
                       style="font-size: 1.4em; font-weight: 600;"/>
            </form>
        </div>
    </div>
</nav>
