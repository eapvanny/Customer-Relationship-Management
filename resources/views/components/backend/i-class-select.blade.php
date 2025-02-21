<div {{ $attributes }}>
    <div class="form-group has-feedback">
        @if ($showLabel)
        <label for="i_class_id">
            {{ __('Class') }} <span class="text-danger">*</span>
        </label>
        @endif
        {!! Form::select($name, $options, $value, ['placeholder' => 'Pick a class', 'class' => 'form-control select2', 'id' => $id, 'required' => $required]) !!}
        <span class="form-control-feedback"></span>
        <span class="text-danger">{{ $errors->first($name) }}</span>
    </div>
</div>
