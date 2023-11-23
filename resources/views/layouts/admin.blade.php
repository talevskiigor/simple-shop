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


    <script src="{{ asset('js/share.js') }}"></script>
    <script src="https://kit.fontawesome.com/d770150ff4.js" crossorigin="anonymous"></script>
</head>
<body>
<script type="text/javascript">
    $(function () {
        $("[data-bs-toggle='tooltip']").tooltip();
        $("[rel='tooltip']").tooltip();
    });
</script>
<nav class="navbar fixed-top navbar-expand-lg bg-body-tertiary justify-content-end">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{url('/admin')}}">
            <img src="{{  \App\Helpers\Image::get( '/images/forkids-logo-h.png',256) }}"  alt="Bootstrap" height="60">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link text-success" href="/"><i class="bi bi-house-door"></i> Home </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="bi bi-question-circle"></i> TODO</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle ацтиве" aria-current="page" href="#" role="button"
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-sitemap"></i> Catalog
                    </a>
                    <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/admin/product"><i class="fa-solid fa-warehouse"></i> Products</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/admin/orders"><i class="fa-solid fa-money-bill-transfer"></i> Orders</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link text-danger" href="{{url('/admin/logout')}}"><i class="fa-solid fa-person-walking-arrow-right"></i> Exit </a>
                </li>
            </ul>


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
        </div>
    </section>

</div>
{{--footer--}}

</body>
</html>
