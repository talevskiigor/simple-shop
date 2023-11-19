@extends('layouts.admin')

@section('content')

    <jslot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </jslot>

    <div class="py-12">
        <div class="maj-7xl mjuto sm:pj lg:pj">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}
                </div>
            </div>
        </div>
    </div>

@endsection
