@extends('layouts.main')


@section('content')
    <form method="POST" action="{{ url('contact') }}">
        @csrf

        <div class="row">
            <div class="col col-sm-6 offset-sm-3">
                <div class="card">
                    <div class="card-header">
                        Контакт
                    </div>
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col col-sm-12">
                                @include('_partials.company')
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-sm-4">
                                <hr>
                            </div>
                            <div class="col col-sm-4">или пишете ни тука</div>
                            <div class="col col-sm-4">
                                <hr>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col col-sm-12">
                                <div class="mb-3">
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Вашето име</label>
                                        <input type="name" class="form-control" id="name" name="name"
                                               required autofocus autocomplete="name"
                                        >
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Вашата електронска пошта (email)</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                               required autofocus autocomplete="email"
                                        >
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Телефон за контакт</label>
                                        <input type="phone" class="form-control" id="phone" name="phone"
                                               required autofocus autocomplete="phone"
                                        >
                                    </div>

                                    <div class="mb-3">
                                        <label for="message" class="form-label">Порака</label>
                                        <textarea class="form-control" id="message" name="message" rows="7" required></textarea>
                                    </div>
                                </div>

                            </div>


                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary m-5"><i class="fa-solid fa-envelopes-bulk"></i> Прати
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
