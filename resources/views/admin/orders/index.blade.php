@extends('layouts.admin')

@section('header_section')

@endsection

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <div class="h1"><i class="fa-solid fa-money-bill-transfer"></i> Orders</div>
            <hr class="hr">
        </div>
        <div class="col col-sm-12">


            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th scope="row">ID</th>
                    <th scope="row">Name</th>
                    <th scope="row">Items</th>
                    <th scope="row">Bank Ref</th>
                    <th scope="row" class="text-end">Total</th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr @if($item->finished) class="table-success" @else class="table-danger" @endif>
                        <td cope="row">{{$item->id}}</td>

                        <td>
                            <strong>{{$item->first}} {{$item->last}}<br></strong>
                            {{$item->address}}, {{$item->city}}<br>
                            {{$item->phone}}<br>{{$item->email}}

                        </td>
                        <td class="small">

                                @foreach($item->items as $key => $i)
                                <div class="row">
                                        <div class="col col-sm-1"><a class="text-reset" href="{{url('/product/' . $i->associatedModel->slug)}}" target="_blank">{{$i->associatedModel->id}}</a> </div>
                                    <div class="col col-sm-7 align-items-star"> <a class="text-reset" href="{{url('/product/' . $i->associatedModel->slug)}}" target="_blank"> {{$i->associatedModel->name}} </a></div>
                                        <div class="col col-sm-2">{{$i->associatedModel->model}}</div>
                                        <div class="col col-sm-2 text-end">{{number_format($i->associatedModel->price,2)}}</div>
                                </div>
                                @endforeach



                        </td>
                        <td>
                            {{ $item->finished ? 'Confirmed' : 'Not confirmed' }} {{ $item->bank_ref ?: '' }}
                            @foreach($item->paymentAttempts as $attempt)
                            <div class="border-top mt-2 pt-2 small">
                                <strong>{{ $attempt->is_test ? 'TEST — no fulfillment' : 'Payment' }}</strong><br>
                                {{ number_format($attempt->amount_minor / 100, 2) }} MKD · {{ $attempt->status }}<br>
                                Reference: {{ $attempt->reference }}<br>Bank: {{ $attempt->bank_reference ?: 'Pending' }}
                                @if($attempt->status === 'returned_success')
                                <form action="{{ url('/admin/payments/'.$attempt->id.'/confirm') }}" method="POST" class="mt-2">
                                    @csrf
                                    <label><input type="checkbox" name="bank_verified" value="1" required> I checked this reference and amount as successful in the cPay merchant portal.</label>
                                    <button class="btn btn-sm btn-outline-success mt-1">Confirm {{ $attempt->is_test ? 'test payment' : 'payment and stock' }}</button>
                                </form>
                                @endif
                            </div>
                            @endforeach
                        </td>

                        <td class="text-end">
                            {{$item->updated_at}}<br>
                            {{$item->created_at}}<br>
                            <strong>{{number_format($item->total,2)}}</strong>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection
