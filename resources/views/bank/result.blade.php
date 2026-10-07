@extends('layouts.main')
@section('content')
<div class="card my-4"><div class="card-body">
<h1 class="h3">Резултат од плаќањето</h1>
@if($attempt->status === 'confirmed')
<div class="alert alert-success">Уплатата е потврдена во cPay.</div>
@elseif($attempt->status === 'returned_success')
<div class="alert alert-success">Банката врати успешен резултат за уплатата.</div>
@elseif($attempt->status === 'failed')
<div class="alert alert-warning">Уплатата е откажана или неуспешна. Кошницата е зачувана.</div>
@else
<div class="alert alert-info">Се чека одговор од банката.</div>
@endif
<p>Износ за уплата: <strong>{{ number_format($attempt->amount_minor / 100, 2) }} ден.</strong></p>
@if($attempt->is_test)<p class="alert alert-warning">Пробна уплата од 1 денар со реална картичка. Оваа нарачка не е за испорака и не ја менува залихата.</p>@endif
<p>Референца: {{ $attempt->reference }} @if($attempt->bank_reference) / {{ $attempt->bank_reference }} @endif</p>
<p>Статусот на уплатата се проверува во cPay пред потврдување на нарачката.</p>
<a class="btn btn-outline-primary" href="/cart">Назад во кошничка</a>
</div></div>
@endsection
