@extends('layouts.admin')
@section('content')
<div class="container" style="max-width:760px">
<h1>My account</h1>
@if(session('status'))<div class="alert alert-success">Changes saved.</div>@endif
@include('profile.partials.update-profile-information-form')
<hr>
@include('profile.partials.update-password-form')
<hr>
<form method="post" action="{{ route('profile.destroy') }}">
@csrf @method('delete')
<h2>Delete account</h2>
<label for="delete-password">Confirm your password</label>
<input class="form-control mb-3" type="password" name="password" id="delete-password" required autocomplete="current-password">
<x-input-error :messages="$errors->userDeletion->get('password')" />
<button class="btn btn-outline-danger">Delete my account</button>
</form>
</div>
@endsection
