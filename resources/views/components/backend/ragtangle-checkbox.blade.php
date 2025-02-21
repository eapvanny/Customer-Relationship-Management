@props(
    [
        'name', 
        'value',
        'required' => false,
        'checked' => false,
        'class'   => 'ms-3'
        ]
    )
<div class="radio iradio me-4 ms-1">
    <div class="iradio-box">
        <div class="wrap-in-iradio-box ">
            <input 
                class="radio-enrolment-type" 
                type="checkbox" 
                name="{{ $name }}" 
                id="{{ $name . '_' . $value }}" 
                value="{{ $value }}" 
                @if($required) required @endif
                @if($checked) checked @endif
            >
            <label for="{{ $name }}" class="{{$class}}">{{ $slot }}</label><small class="text-danger fst-italic fs-xxl-6"> {{ __($errors->first($name)) }}</small>
        </div>
        <span class="form-control-feedback"></span>
    </div>
</div>