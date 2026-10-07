@extends('layouts.admin')
@section('title', 'Overview')
@section('content')
<h1>Store overview</h1><p class="text-secondary">Manage your catalog, content, and media from one place.</p>
<div class="row g-3 mt-2">
@foreach(['product.index'=>['Products',\App\Models\Product::count(),'Add products, set prices and stock.'],'categories.index'=>['Categories',\App\Models\Category::count(),'Organize the store navigation.'],'pages.index'=>['Pages',\App\Models\Page::count(),'Write and publish store information.'],'media.index'=>['Media',\App\Models\Media::count(),'Upload and reuse images and videos.']] as $route=>[$label,$count,$help])
<div class="col-md-6 col-xl-3"><a href="{{ route($route) }}" class="card h-100 text-decoration-none text-dark shadow-sm"><div class="card-body p-4"><h2 class="h5">{{ $label }}</h2><div class="display-5 my-3">{{ $count }}</div><p class="text-secondary mb-0">{{ $help }}</p></div></a></div>
@endforeach
</div>
@endsection
