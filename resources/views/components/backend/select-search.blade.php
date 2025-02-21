<div {{ $attributes }}>
    <div class="form-group has-feedback">
        @if (!empty($label))
        <label for="{{ $name }}">
            {{ __($label) }} @if($required) <span class="text-danger">*</span> @endif
        </label>
        @endif
        @php
            $attributes = ['class' => 'form-control select2', 'id' => $id, 'required' => $required, 'disabled' => $disabled];
            if (!empty($placeholder)){
                $attributes['placeholder'] = __($placeholder);
            }
        @endphp
        {!! Form::select($name, $options, $value, $attributes) !!}
        <span class="form-control-feedback"></span>
        <span class="text-danger">{{ __($errors->first($name)) }}</span>
    </div>
</div>
