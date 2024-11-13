@extends('layouts.guest')


@section('content')

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="col col-sm-4 offset-sm-4">
            <div class="card">
                <div class="card-header">
                    Login
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="name" class="form-control" id="name" name="name"
                                   required autofocus autocomplete="name"
                                   placeholder="Your Name">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email"
                                   required autofocus autocomplete="email"
                                   placeholder="name@example.com">
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   required autofocus autocomplete="password">
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="password_confirmation"
                                   name="password_confirmation"
                                   required autofocus autocomplete="password_confirmation">
                        </div>

                        <div class="mb-3">
                            <input type="submit" value="Register" class="btn btn-primary">
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
