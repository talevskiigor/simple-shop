@extends('layouts.admin')
@section('content')
<h1>{{ $item->exists ? 'Edit administrator' : 'Add administrator' }}</h1>
<form class="card mt-3" style="max-width:760px" method="post" action="{{ $item->exists ? route('users.update',$item) : route('users.store') }}">@csrf @if($item->exists) @method('put') @endif<div class="card-body">
<p>Administrators can manage the entire store.</p>
<label class="form-label" for="name">Name</label><input class="form-control mb-3" id="name" name="name" value="{{ old('name',$item->name) }}" required>
<label class="form-label" for="email">Email</label><input class="form-control mb-3" id="email" name="email" type="email" value="{{ old('email',$item->email) }}" required autocomplete="username">
<label class="form-label" for="password">{{ $item->exists ? 'New password (leave empty to keep current password)' : 'Password' }}</label><input class="form-control mb-3" id="password" name="password" type="password" minlength="12" autocomplete="new-password" @required(!$item->exists)>
<label class="form-label" for="password_confirmation">Confirm password</label><input class="form-control mb-3" id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
<button class="btn btn-primary">Save administrator</button><a class="btn btn-link" href="{{ route('users.index') }}">Back to administrators</a>
</div></form>
@endsection
