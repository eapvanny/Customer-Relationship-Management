<div {{ $attributes }}>
    <div class="form-group has-feedback">
        <label for="{{ $name }}">
            {{ __('Curriculum') }}
            @if ($addMore)
            <a href="{{ URL::route('curriculum.create') }}" target="__blank"> {{ __('(Add more)') }} </a>
            @endif
        </label>
        @php
        $attributes = ['class' => 'form-control select2', 'id' => $id, 'required' => $required];
        if (!empty($placeholder)) {
            $attributes['placeholder'] = $placeholder;
        }
        @endphp
        {!! Form::select($name, $options, $value, $attributes) !!}
        <span class="form-control-feedback"></span>
        <span class="text-danger">{{ $errors->first($name) }}</span>
    </div>
</div>
