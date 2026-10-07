@extends('layouts.main')


@section('content')

    <h1>{{$page->title}}</h1>
    <div class="row">
        <div class="col col-sm-12">
            <div class="store-content">{!! app(\App\Services\ContentHtml::class)->clean(html_entity_decode($page->body)) !!}</div>
        </div>
    </div>


@endsection
