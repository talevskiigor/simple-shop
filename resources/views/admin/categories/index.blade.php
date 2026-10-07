@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h1>Categories</h1><a class="btn btn-primary" href="{{ route('categories.create') }}">Add Categories</a></div>
<div class="table-responsive"><table class="table align-middle bg-white"><tbody>
@forelse($items as $item)<tr><td><a href="{{ route('categories.edit',$item) }}">{{ $item->name }}</a></td><td>{{ $item->product_count }} products</td><td class="text-end"><div class="d-flex gap-2 justify-content-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('categories.edit',$item) }}">Edit</a><form method="post" action="{{ route('categories.destroy',$item) }}" data-confirm="Remove this item?">@csrf @method('delete')<button class="btn btn-sm btn-outline-danger">Remove</button></form></div></td></tr>
@empty<tr><td>No items yet.</td></tr>@endforelse
</tbody></table></div>
@endsection
