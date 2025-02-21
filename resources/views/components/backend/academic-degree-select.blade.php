<div {{ $attributes }}>
    <div class="form-group has-feedback">
        @if ($showLabel)
        <label for="{{ $name }}"> {{ __('Academic Degree') }}
            <i class="fa fa-question-circle" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Set province"></i>
            @if ($required)<span class="text-danger">*</span>@endif
        </label>
        @endif
        {!! Form::select($name, $options, $value, ['placeholder' => __('Pick a degree'),'class' => 'form-control select2', 'required' => $required]) !!}

        <span class="fa fa-calendar form-control-feedback"></span>
        <span class="text-danger">{{ $errors->first($name) }}</span>
    </div>
</div>
