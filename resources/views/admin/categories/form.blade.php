@extends('layouts.admin')
@section('content')
<h1>{{ $item->exists ? 'Edit category' : 'Add category' }}</h1>
<form class="card mt-3" style="max-width:760px" method="post" action="{{ $item->exists ? route('categories.update',$item) : route('categories.store') }}">@csrf @if($item->exists) @method('put') @endif<div class="card-body">
<label class="form-label" for="name">Name</label><input class="form-control mb-3" id="name" name="name" value="{{ old('name',$item->name) }}" required>
<label class="form-label" for="slug">URL name</label><input class="form-control mb-3" id="slug" name="slug" value="{{ old('slug',$item->slug) }}" required>
<button class="btn btn-primary">Save category</button><a class="btn btn-link" href="{{ route('categories.index') }}">Back to categories</a>
</div></form>
@endsection
