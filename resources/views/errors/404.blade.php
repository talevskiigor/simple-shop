@extends('layouts.main')

@section('header_section')

@endsection

@section('content')


<div class="d-flex align-items-center justify-content-center m-5">
    <div class="text-center">
        <h1 class="display-1 fw-bold">404</h1>
        <p class="fs-3"> <span class="text-danger">Opps!</span> Page not found.</p>
        <p class="lead">
            Избраната страница не постои.
        </p>
        <a href="/" class="btn btn-primary">Продолжи на почетна страна</a>
    </div>
</div>
@endsection
