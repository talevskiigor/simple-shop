@extends('layouts.main')

@section('content')

    <form action="{{url('order')}}" method="POST">
        @csrf

        <div class="row">
            <div class="col col-sm-8 offset-sm-2">

                <div class="card">
                    <div class="card-header">
                        <div class="row">
                            <div class="col col-sm-1"><h1><i class="bi bi-house"></i></h1></div>
                            <div class="col col-sm-11"><h2> Податоци за достава </h2></div>
                        </div>

                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Достава е бесплатна *</h5>
                        <p class="card-text">Доставата се врши само до градовите.<br> За други населени места ќе мора да
                            го подигнете производот од магацините во најблискиот град.</p>
                        <div class="row">
                            <div class="col col-sm-6 offset-sm-3">
                                <hr>

                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-sm-12">
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
                        </div>

                        <div class="row mb-5">
                            <div class="col col-sm-6">
                                <label for="first" class="form-label">Име</label>
                                <input type="text" required class="form-control" value="{{$first}}" id="first" name="first"
                                       placeholder="Вашето име?">
                                @error('first')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                            </div>
                            <div class="col col-sm-6">
                                <label for="last" class="form-label">Презиме</label>
                                <input type="text" required class="form-control" value="{{$last}}" id="last" name="last"
                                       placeholder="Вашето презиме?">
                                @error('last')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col col-sm-6">

                                <label for="address" class="form-label">Адреса <small>(Улица и број)</small></label>
                                <input type="text" required id="address" value="{{$address}}" class="form-control" name="address"
                                       placeholder="пр: Кузман Јосифовски, број 7А влез 2 стан 8">
                                @error('address')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                            </div>
                            <div class="col col-sm-6">
                                <label for="city" class="form-label">Град </label>
                                <input type="text" required id="city" value="{{$city}}" class="form-control" name="city"
                                       placeholder="пример: Прилеп">
                                @error('city')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                            </div>
                        </div>
                        <div class="row mb-5">
                            <div class="col col-sm-6">
                                <label for="phone" class="form-label">Телефонски број</label>
                                <input type="text" required class="form-control" id="phone" name="phone"
                                       placeholder="пример: 071404006" value="{{$phone}}">
                                @error('phone')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror

                            </div>
                            <div class="col col-sm-6">
                                <label for="email" required class="form-label">Електронска пошата</label>
                                <input type="text" class="form-control" value="{{$email}}"  id="email" name="email"
                                       placeholder="name@example.com">
                                @error('email')
                                <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-5">
                            <div class="col col-sm-12">
                                <label for="comment" class="form-label">Дополнителен коментар</label>
                                <textarea class="form-control" id="comment" name="comment" rows="3">{{$comment}}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col col-sm-6 offset-sm-3">
                                <hr>
                            </div>
                        </div>

                        <div class="row mb-1">
                            <div class="col col-sm-9"></div>
                            <div class="col col-sm-3 d-flex flex-row-reverse">
                                <button type="submit" class="btn btn-outline-primary btn-lg"><i
                                        class="bi bi-caret-right-square"></i> Плати
                                </button>
                            </div>
                        </div>

                    </div>
                </div>


            </div>
        </div>

    </form>

@endsection
