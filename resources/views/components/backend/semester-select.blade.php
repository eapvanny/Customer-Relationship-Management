<div {{ $attributes }}>
    <div class="form-group has-feedback">
        @if ($showLabel)
        <label for="group">
            {{ __('Semester') }} @if ($required) <span class="text-danger">*</span> @endif
        </label>
        @endif
        <select name="{{$name}}" class="form-select select2" require="{{ $required }}" $id="{{ $id }}">
            <option value="">{{ __('Pick a semester') }}</option>
            @foreach($options as $s)
                @php
                    $selected = '';
                    if ($s == $value){
                        $selected = 'selected';
                    }
                @endphp
                <option {{ $selected }} value="{{ $s }}">{{ __($s) }}</option>
            @endforeach
        </select>
        <span class="form-control-feedback"></span>
        <span class="text-danger">{{ $errors->first($name) }}</span>
    </div>
</div>
