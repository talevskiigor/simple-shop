@extends('layouts.admin')
@section('title', $item->exists ? 'Edit page' : 'Add page')
@section('content')
<h1>{{ $item->exists ? 'Edit page' : 'Add page' }}</h1>
<form class="card mt-3" method="post" action="{{ $item->exists ? route('pages.update',$item) : route('pages.store') }}">@csrf @if($item->exists) @method('put') @endif<div class="card-body">
<label class="form-label" for="title">Page title</label><input class="form-control mb-3" id="title" name="title" value="{{ old('title',$item->title) }}" required>
<label class="form-label" for="slug">URL name</label><input class="form-control mb-3" id="slug" name="slug" value="{{ old('slug',$item->slug) }}" required>
<label class="form-label" for="body">Content</label><textarea class="form-control" id="body" name="body" rows="15" data-editor>{{ old('body',html_entity_decode($item->body ?? '')) }}</textarea>
<label class="form-label mt-3" for="published">Publication</label><select class="form-select mb-3" id="published" name="published"><option value="0" @selected(!old('published',$item->published))>Draft</option><option value="1" @selected(old('published',$item->published))>Published</option></select>
<button class="btn btn-primary">Save page</button><a class="btn btn-link" href="{{ route('pages.index') }}">Back to pages</a>@if($item->exists && $item->published)<a class="btn btn-link" href="{{ url('/pages/'.$item->slug) }}" target="_blank" rel="noopener">View in store ↗</a>@endif
</div></form>
@endsection
