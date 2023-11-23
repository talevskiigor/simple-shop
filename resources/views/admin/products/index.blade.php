@extends('layouts.admin')

@section('header_section')

@endsection

@section('content')

    <div class="row">
        <div class="col col-sm-12">
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th scope="row">ID</th>
                    <th scope="row">Image</th>
                    <th scope="row">Name</th>
                    <th scope="row">Quantity</th>
                    <th scope="row">Discount</th>
                    <th scope="row">Price</th>
                </tr>
                </thead>
                <tbody>
                @foreach($products as $product)
                    <tr @if(!$product->quantity) class="table-danger" @endif>
                        <td scope="row">{{$product->id}}</td>
                        <td><img src="{{\App\Helpers\Image::get($product->image,128)}}"></td>
                        <td>{{$product->name}} <br>{{$product->model}}</td>
                        <td>{{$product->quantity}}</td>
                        <td>{{$product->discount?$product->discount.'%':''}}</td>
                        <td class="text-end">{{number_format($product->price,2)}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
