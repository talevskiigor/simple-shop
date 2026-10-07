@extends('layouts.admin')
@section('title', 'Products')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h1>Products</h1><a class="btn btn-primary" href="{{ route('product.create') }}">Add product</a></div>
<form class="d-flex gap-2 mb-3"><input class="form-control" type="search" name="q" value="{{ request('q') }}" placeholder="Search name or model" aria-label="Search products"><select class="form-select w-auto" name="stock" aria-label="Availability"><option value="">All stock</option><option value="out" @selected(request('stock')==='out')>Out of stock</option></select><button class="btn btn-outline-primary">Search</button></form>
<div class="table-responsive"><table class="table align-middle bg-white"><thead><tr><th>Product</th><th>Model</th><th>Price (MKD)</th><th>Stock</th><th>Visible</th><th></th></tr></thead><tbody>
@forelse($products as $product)<tr><td><img src="{{ \App\Helpers\Image::get($product->image,64) }}" width="56" height="56" class="object-fit-contain me-2" alt=""><a href="{{ route('product.edit',$product) }}">{{ $product->name }}</a></td><td>{{ $product->model }}</td><td>{{ number_format($product->getPrice(),2) }}</td><td>{{ $product->quantity }}</td><td>{{ $product->active ? 'Yes' : 'Hidden' }}</td><td class="text-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('product.edit',$product) }}">Edit</a></td></tr>
@empty<tr><td colspan="6">No products found.</td></tr>@endforelse
</tbody></table></div>{{ $products->links() }}
@endsection
