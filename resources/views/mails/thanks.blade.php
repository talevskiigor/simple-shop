@extends('layouts.main')

@section('header_section')

@endsection

@section('content')



<div class="row">
    <div class="col col-sm-6 offset-sm-3 mt-5 mb-5">
        <div class="card">
            <div class="card-header bg-success text-light">
                <i class="fa-solid fa-hands-clapping"></i>  Ви благодариме на довербата !
            </div>
            <div class="card-body">
                <h5 class="card-title">Пораката е примена. <i class="fa-solid fa-clipboard-check"></i></h5>
                <p class="card-text">Нашиот тим ќе ве контактира во најбрз можен рок.</p>
                <p>
                <hr class="hr text-center">
                </p>
                <p class="text-center">

                <a href="{{url("")}}" class="btn btn-primary">Продолжи</a>
                </p>
            </div>
        </div>
    </div>
</div>



@endsection
