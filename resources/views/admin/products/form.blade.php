@extends('layouts.admin')
@section('title', $item->exists ? 'Edit product' : 'Add product')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h1>{{ $item->exists ? 'Edit product' : 'Add product' }}</h1>@if($item->exists)<a href="{{ url('/product/'.$item->slug) }}" target="_blank" rel="noopener">View in store ↗</a>@endif</div>
<form method="post" action="{{ $item->exists ? route('product.update',$item) : route('product.store') }}">@csrf @if($item->exists) @method('put') @endif
<div class="row g-4"><div class="col-xl-8"><div class="card"><div class="card-body">
<label class="form-label" for="name">Product name</label><input class="form-control mb-3" id="name" name="name" value="{{ old('name',$item->name) }}" required maxlength="255">
<label class="form-label" for="slug">URL name</label><input class="form-control" id="slug" name="slug" value="{{ old('slug',$item->slug) }}" required maxlength="255"><p class="form-text">Keep an existing URL unchanged to preserve shared links. Use letters, numbers and hyphens.</p>
<label class="form-label" for="description">Description</label><textarea class="form-control" data-editor id="description" name="description" rows="12">{{ old('description',html_entity_decode($item->description ?? '')) }}</textarea>
</div></div><div class="card mt-4"><div class="card-body">
<h2 class="h5">Product images and videos</h2><p class="text-secondary small">The first image is the cover. Move items to set their display order.</p>
<div data-gallery class="row g-3" id="product-gallery">
@php
$selectedIds = old('media_ids', $item->media->pluck('id')->all());
$selected = \App\Models\Media::whereIn('id', $selectedIds)->get()->keyBy('id');
@endphp
@foreach($selectedIds as $id) @if($media = $selected->get($id))
<div class="col-6 col-md-4" data-media-id="{{ $media->id }}"><div class="card h-100"><div class="card-body">
@if($media->type === 'image')<img class="media-thumb" src="{{ \App\Helpers\Image::get($media->path,256) }}" alt="{{ $media->alt }}">@else<div class="media-thumb d-flex align-items-center justify-content-center">Video</div>@endif
<div class="small text-truncate my-2">{{ $media->name }}</div><input type="hidden" name="media_ids[]" value="{{ $media->id }}"><div class="btn-group btn-group-sm"><button type="button" class="btn btn-outline-secondary" data-move="-1" aria-label="Move earlier">←</button><button type="button" class="btn btn-outline-secondary" data-move="1" aria-label="Move later">→</button><button type="button" class="btn btn-outline-danger" data-remove-media>Remove</button></div></div></div></div>
@endif @endforeach
</div><button type="button" class="btn btn-outline-primary mt-3" data-pick-gallery>Add from media library</button>
</div></div></div><div class="col-xl-4"><div class="card"><div class="card-body">
<label class="form-label" for="model">Model / SKU</label><input class="form-control mb-3" id="model" name="model" value="{{ old('model',$item->model) }}" required>
<label class="form-label" for="price">Price (MKD)</label><input class="form-control mb-3" id="price" name="price" type="number" min="0" step="0.01" value="{{ old('price',$item->price) }}" required>
<label class="form-label" for="discount">Discount (%)</label><input class="form-control mb-3" id="discount" name="discount" type="number" min="0" max="100" step="0.01" value="{{ old('discount',$item->discount ?? 0) }}">
<label class="form-label" for="quantity">In-stock quantity</label><input class="form-control mb-3" id="quantity" name="quantity" type="number" min="0" step="1" value="{{ old('quantity',$item->quantity) }}" required>
<label class="form-label" for="active">Store visibility</label><select class="form-select mb-3" id="active" name="active"><option value="1" @selected(old('active',$item->active))>Visible</option><option value="0" @selected(!old('active',$item->active))>Hidden</option></select>
<fieldset><legend class="h6">Categories</legend>@foreach($catalogCategories as $category)<div class="form-check"><input class="form-check-input" type="checkbox" name="category_ids[]" id="category-{{ $category->id }}" value="{{ $category->id }}" @checked(in_array($category->id,old('category_ids',$item->category->pluck('id')->all())))><label class="form-check-label" for="category-{{ $category->id }}">{{ $category->name }}</label></div>@endforeach</fieldset>
<button class="btn btn-primary w-100 mt-4">Save product</button><a class="btn btn-link w-100" href="{{ route('product.index') }}">Back to products</a>
</div></div></div></div></form>
@if($item->exists)<form class="mt-4" method="post" action="{{ route('product.destroy',$item) }}" data-confirm="Archive this product? It will disappear from the store.">@csrf @method('delete')<button class="btn btn-outline-danger">Archive product</button></form>@endif
@endsection
