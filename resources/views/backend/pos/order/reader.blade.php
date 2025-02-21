<div class="col-md-6">

    @if ($status=="PENDING"  && in_array($payment_status,["AWAITING_PAYMENT","FAILED"]))
        <a  style="background: #605ca8;padding: 6px 28px;" data-val="PAID" href="{{url('order/status')}}" class="status_item btn btn-info">Pay</a>
    @elseif($status=="COMPLETE" && in_array($payment_status,["AWAITING_PAYMENT","FAILED"]))  
        <a  style="background: #605ca8;padding: 6px 28px;" data-val="PAID" href="{{url('order/status')}}" class="status_item btn btn-info">Pay</a> 
    @else   
        <a  style="background: #605ca8;padding: 6px 28px;" class="btn btn-info" disabled>Paid</a>     
    @endif

</div>
<div class="col-md-6 text-right">
    @if($cashier)
        @if($status=="NEW")
            @if (!empty($checkStock) && $checkStock)
                <a  style="background: #2196f3;color: #fff" class="status_item btn" href="{{url('order/status')}}" data-val="PENDING" > {{ __('Accept') }} </a>
            @else
                <a  style="background: #2196f3;color: #fff" class="btn" disabled> {{ __('Accept') }} </a>
            @endif
           
            <a  class="status_item btn btn-danger" href="{{url('order/status')}}" data-val="CANCELLED" > {{ __('Cancel') }} </a>
        @elseif($status=="PENDING" && in_array($payment_status,["AWAITING_PAYMENT","FAILED"]))
            <a  style="padding: 6px 28px;" class="status_item btn btn-danger" href="{{url('order/status')}}" data-val="CANCELLED" > {{ __('Cancel') }} </a>
            <a  data-val="COMPLETE" href="{{url('order/status')}}" class="status_item btn btn-success"> {{ __('Complete') }} </a>
        @elseif($status=="PENDING" && in_array($payment_status,["PAID"]))    
            <a  data-val="COMPLETE" href="{{url('order/status')}}" class="status_item btn btn-success"> {{ __('Complete') }} </a>
        @elseif($status=="CANCELLED")
            <a  class="status_item btn btn-danger disabled" href="" data-val="CANCELLED" > {{ __('Cancelled') }} </a>
        @else
            <a data-val="COMPLETE" href="" class="status_item btn btn-success" disabled> {{ __('Completed') }} </a>
        @endif
    @else
        @if($status=="NEW")
            <a  class="status_item btn btn-danger" href="{{url('order/status')}}" data-val="CANCELLED" > {{ __('Cancel') }} </a>
        @elseif($status=="PENDING")
            <a  style="background: #8c8c8c;color: #fff"  class="status_item btn disabled"  data-val="PENDING" > {{ __('Pending') }} </a>
            <a  class="status_item btn btn-danger" href="{{url('order/status')}}" data-val="CANCELLED" > {{ __('Cancel') }} </a>
        @elseif($status=="CANCELLED")
            <a  class="status_item btn btn-danger disabled" href="" data-val="CANCELLED" > {{ __('Cancelled') }} </a>
        @else
            <a type="submit" data-val="COMPLETE" href="" class="status_item btn btn-success disabled"> {{ __('Completed') }} </a>
        @endif
    @endif
</div>