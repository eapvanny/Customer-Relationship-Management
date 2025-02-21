<div {{ $attributes }}>
    <div class="card rounded-4 border-none border-0 shadow-sm me-0">
        <div class="card-body p-4">
            <div class="media  d-flex justify-content-between ">
                <div class="media-body">
                    <p class="mb-0 text-black font-w600 mb-2">{{__('Notices Board')}}</p>
                </div>
            </div>
            <div>
                @if (count($notifications) > 0)
                    @foreach ($notifications as $notification)
                        <div class="notification-ui_dd-content my-1 px-3 py-4 shadow-sm rounded-3 row">
                            <div class="col-8">
                                <div class="notification-list_img d-flex justify-content-center">
                                    <i class="fa fa-info-circle" aria-hidden="true"></i>
                                    <span class="ms-2 text-secondary h5">{{$notification['message']}}</span>
                                </div>
                            </div>
                            <div class="col-4">
                                <span class="d-flex"><small class="bg-primary text-light ms-auto p-sm-2 rounded-pill fw-bolder">{{$notification['created_at']}}</small></span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="alert alert-light" role="alert">
                        {{ __("No notifications.") }}
                    </div>

                @endif
            </div>
        </div>
    </div>
</div>
