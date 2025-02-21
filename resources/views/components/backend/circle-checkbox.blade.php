@props(
    [
        'name', 
        'value',
        'required' => false,
        'checked' => false,
        'disabled' => false,
        ]
    )
<div class="radio iradio me-4">
    <div class="iradio-box">
        <div class="wrap-in-iradio-box ">
            <input 
                class="radio-enrolment-type" 
                type="radio" 
                name="{{ $name }}" 
                id="{{ $name . '_' . $value }}" 
                value="{{ $value }}" 
                @if($required) required @endif
                @if($checked) checked @endif
                @if($disabled) disabled @endif
            >
            <label for="{{ $name }}" class="@if($disabled){{'text-muted opacity-25'}}@endif">{{ $slot }}</label>
        </div>
        <span class="form-control-feedback"></span>
        <span class="text-danger">{{ __($errors->first($name)) }}</span>
    </div>
</div>