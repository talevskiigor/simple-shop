@extends('layouts.guest')


@section('content')

    <!-- Session Status -->
    <auth-session-status class="mb-4" :status="session('status')"/>

    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="col col-sm-4 offset-sm-4">
            <div class="card">
                <div class="card-header">
                    Login
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="col col-sm-12 mb-3">
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <div class="col col-sm-12 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{old('email')}}"
                                   required autofocus autocomplete="email">
                        </div>

                        <div class="col col-sm-12 mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   required autocomplete="password">
                        </div>

                        <!-- Remember Me -->
                        <div class="col-sm-6 block mb-3">
                            <label for="remember_me" class="inline-flex items-center">
                                <input id="remember_me" type="checkbox"
                                       class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                            </label>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <input type="submit" value="Login" class="btn btn-primary">
                        </div>


                    </div>
                </div>
            </div>
        </div>


    </form>
@endsection
