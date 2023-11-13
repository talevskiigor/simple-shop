<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>For Kids</title>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
{{--        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>--}}

{{--    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"--}}
{{--            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"--}}
{{--            crossorigin="anonymous"></script>--}}

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>
</head>
<body>
<script type="text/javascript">
    $(function () {
        $("[rel='tooltip']").tooltip();
    });
</script>
<nav class="navbar fixed-top navbar-expand-lg bg-body-tertiary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="/logo/forkids.png" alt="Bootstrap" height="60">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link" href="/"><i class="bi bi-house-door"></i> Почеток</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-question-circle"></i> За нас</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle ацтиве" aria-current="page" href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-shop"></i> Производи
                    </a>
                    <ul class="dropdown-menu">
                        @foreach($categories as $category)
                            <li><a class="dropdown-item" href="/categories/{{$category->slug}}">{{$category->name}}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link"><i class="bi bi-megaphone"></i> Контакт</a>
                </li>
            </ul>
            <form action="{{url('search')}}" class="d-flex" method="GET" role="search">
                <input class="form-control me-2" value="{{request()->get('find')}}" id="find" name="find" type="search" placeholder="Што сакате да најдете?" aria-label="Search">
                <button class="btn btn-outline-primary" type="submit">Барај</button>
            </form>
            <form class="d-flex form-inline">
                @if(\Cart::session(session(\App\Helpers\ShoppingCart::SHOPPING_CART_ID,Ramsey\Uuid\Uuid::uuid4()->toString()))->isEmpty())
                @else

                <a href="/cart" class="btn btn-outline-success my-2 my-sm-0" type="submit"><i
                        class="bi bi-cart-check"></i> Кошница
                    <span
                        class="badge rounded-pill bg-danger">{{\Cart::session(session(\App\Helpers\ShoppingCart::SHOPPING_CART_ID,Ramsey\Uuid\Uuid::uuid4()->toString()))->getTotalQuantity()}}</span>
                </a>
                @endif
            </form>
        </div>
    </div>
</nav>

<hr class="hr" style="margin-top: 86px">
<div class="container">
    {{-- nav bar END   --}}
    @yield('content')
</div>

@yield('header_section')

{{--footer--}}
<hr class="hr" style="margin-top: 64px">
<div class="container">
    <section class="pb-4">
        <div class="bg-light border rounded-5">

            <section class="p-4">
                <!-- Footer -->
                <footer class="text-center text-lg-start bg-light text-muted">
                    <!-- Section: Social media -->
                    <section class="d-flex justify-content-center justify-content-lg-between p-4 border-bottom">
                        <!-- Left -->
                        <div class="d-none d-lg-block">
                            <span>Get connected with us on social networks:</span>
                        </div>
                        <!-- Left -->

                        <!-- Right -->
                        <div>
                            <a href="" class="me-4 text-reset">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="" class="me-4 text-reset">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="" class="me-4 text-reset">
                                <i class="fab fa-google"></i>
                            </a>
                            <a href="" class="me-4 text-reset">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="" class="me-4 text-reset">
                                <i class="fab fa-linkedin"></i>
                            </a>
                            <a href="" class="me-4 text-reset">
                                <i class="fab fa-github"></i>
                            </a>
                        </div>
                        <!-- Right -->
                    </section>
                    <!-- Section: Social media -->

                    <!-- Section: Links  -->
                    <section class="">
                        <div class="container text-center text-md-start">
                            <!-- Grid row -->
                            <div class="row mt-3">
                                <!-- Grid column -->
                                <div class="col-md-3 col-lg-4 col-xl-3 mx-auto">
                                    <!-- Content -->
                                    <h6 class="text-uppercase fw-bold">
                                        <i class="fas fa-gem me-3"></i>Company name
                                    </h6>
                                    <p>
                                        Here you can use rows and columns to organize your footer content. Lorem ipsum
                                        dolor sit amet, consectetur adipisicing elit.
                                    </p>
                                </div>
                                <!-- Grid column -->

                                <!-- Grid column -->
                                <div class="col-md-2 col-lg-2 col-xl-2 mx-auto">
                                    <!-- Links -->
                                    <h6 class="text-uppercase fw-bold mb-4">
                                        Products
                                    </h6>
                                    <p>
                                        <a href="#!" class="text-reset">Angular</a>
                                    </p>
                                    <p>
                                        <a href="#!" class="text-reset">React</a>
                                    </p>
                                    <p>
                                        <a href="#!" class="text-reset">Vue</a>
                                    </p>
                                    <p>
                                        <a href="#!" class="text-reset">Laravel</a>
                                    </p>
                                </div>
                                <!-- Grid column -->

                                <!-- Grid column -->
                                <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mb-4">
                                    <!-- Links -->
                                    <h6 class="text-uppercase fw-bold mb-4">
                                        Useful links
                                    </h6>
                                    <p>
                                        <a href="#!" class="text-reset">Pricing</a>
                                    </p>
                                    <p>
                                        <a href="#!" class="text-reset">Settings</a>
                                    </p>
                                    <p>
                                        <a href="#!" class="text-reset">Orders</a>
                                    </p>
                                    <p>
                                        <a href="#!" class="text-reset">Help</a>
                                    </p>
                                </div>
                                <!-- Grid column -->

                                <!-- Grid column -->
                                <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mb-md-0 mb-4">
                                    <!-- Links -->
                                    <h6 class="text-uppercase fw-bold mb-4">Contact</h6>
                                    <p><i class="fas fa-home me-3"></i> New York, NY 10012, US</p>
                                    <p>
                                        <i class="fas fa-envelope me-3"></i>
                                        info@example.com
                                    </p>
                                    <p><i class="fas fa-phone me-3"></i> + 01 234 567 88</p>
                                    <p><i class="fas fa-print me-3"></i> + 01 234 567 89</p>
                                </div>
                                <!-- Grid column -->
                            </div>
                            <!-- Grid row -->
                        </div>
                    </section>
                    <!-- Section: Links  -->

                </footer>
                <!-- Footer -->
            </section>




        </div>

        <div class="p-4 text-center border-top mobile-hidden">
            <!-- Copyright -->
            <div class="text-center p-4">
                All rights reserved.<br>
                Copyright © {{\Carbon\Carbon::now()->year}} <a class="text-reset fw-bold"
                                                               href="https://forkids.mk/">ForKids.MK</a><br>
                Powered by: <a class="text-reset fw-bold" href=mailto:igor.talevslo+forkids@gmail.com">I.T.</a>
            </div>
            <!-- Copyright -->

        </div>

    </section>

</div>
{{--footer--}}

</body>
</html>
