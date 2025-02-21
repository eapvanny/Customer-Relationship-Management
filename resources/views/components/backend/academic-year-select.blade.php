<div {{ $attributes }}>
    <div class="form-group has-feedback">
        @if ($showLabel)
        <label for="{{ $name }}">
            {{ __('Academic Year') }} {{$slot}}
            @if ($required)
            <span class="text-danger">*</span>
            @endif
        </label>
        @endif
        @php
            $data = ['class' => 'form-control select2', 'id' => $id, 'required' => $required];
            if (!empty($placeholder)) {
                $data['placeholder'] = __($placeholder);
            }
        @endphp
        {!! Form::select($name, $options, $value, $data) !!}
        <span class="form-control-feedback"></span>
        <span class="text-danger">{{ $errors->first($name) }}</span>
    </div>
</div>
