<div>
    {{-- {!! dump($errors) !!} --}}
    {{-- {!! $attributes !!} --}}
    <label for="{{$name}}" class="form-label">{{ $label }}</label>

    <input {{ $attributes->merge(['type' => 'text']) }}
        class="form-control
        @if ($errors->has($name)) is-invalid
        @elseif($errors->any()) is-valid @endif"
        id="{{ $name }}" name="{{$name}}" value="{{ old($name, $value) }}">

    @if ($errors->has($name))
        <div class="invalid-feedback">
            {{ $errors->first($name) }}
        </div>
    @endif

</div>
