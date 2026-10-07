<!doctype html>
<html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}"><meta name="robots" content="noindex,nofollow">
<title>@yield('title', 'Administration') · ForKids</title>
@vite(['resources/js/app.js', 'resources/scss/app.scss'])
</head><body class="admin-shell">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark"><div class="container-fluid px-lg-4">
<a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">ForKids <span class="badge text-bg-light ms-2">Admin</span></a>
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#admin-nav" aria-label="Open menu"><span class="navbar-toggler-icon"></span></button>
<div class="collapse navbar-collapse" id="admin-nav">
<div class="navbar-nav me-auto">
@foreach(['product.index'=>'Products','categories.index'=>'Categories','pages.index'=>'Pages','media.index'=>'Media','orders.index'=>'Orders','users.index'=>'Administrators'] as $route=>$label)
<a class="nav-link {{ request()->routeIs(explode('.', $route)[0].'.*') ? 'active' : '' }}" href="{{ route($route) }}">{{ $label }}</a>
@endforeach
</div><div class="navbar-nav align-items-lg-center gap-2"><a class="nav-link" href="/" target="_blank" rel="noopener">View store ↗</a><a class="nav-link" href="{{ route('profile.edit') }}">My account</a><form method="post" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-light btn-sm">Sign out</button></form></div>
</div></div></nav>
<main class="container-fluid px-lg-5 py-4">
@if(config('store.sandbox'))<div class="alert alert-info py-2 small">Staging / test store · Payments and outgoing email are disabled.</div>@endif
@if(session('status'))<div role="status" class="alert alert-success">{{ session('status') }}</div>@endif
@if($errors->any())<div role="alert" class="alert alert-danger"><strong>Please check these details:</strong><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</main>
@include('admin.media.picker')
</body></html>
