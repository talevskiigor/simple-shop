@extends('layouts.admin')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3"><h1>Administrators</h1><a class="btn btn-primary" href="{{ route('users.create') }}">Add Administrators</a></div>
<div class="table-responsive"><table class="table align-middle bg-white"><tbody>
@forelse($users as $item)<tr><td><a href="{{ route('users.edit',$item) }}">{{ $item->name }}</a></td><td>{{ $item->email }}</td><td class="text-end"><div class="d-flex gap-2 justify-content-end"><a class="btn btn-sm btn-outline-primary" href="{{ route('users.edit',$item) }}">Edit</a><form method="post" action="{{ route('users.destroy',$item) }}" data-confirm="Remove this item?">@csrf @method('delete')<button class="btn btn-sm btn-outline-danger">Remove</button></form></div></td></tr>
@empty<tr><td>No items yet.</td></tr>@endforelse
</tbody></table></div>
@endsection
