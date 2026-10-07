@extends('layouts.admin')
@section('content')
<div class="container" style="max-width:760px">
<h1>My account</h1>
@if(session('status'))<div class="alert alert-success">Changes saved.</div>@endif
@include('profile.partials.update-profile-information-form')
<hr>
@include('profile.partials.update-password-form')
<p class="mt-4 text-secondary">Account removal is managed by another administrator under Administrators.</p>
</div>
@endsection
