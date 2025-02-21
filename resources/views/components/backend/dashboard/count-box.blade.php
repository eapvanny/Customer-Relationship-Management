<li>
    <div class="card rounded-4 border-none border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="media  d-flex justify-content-between ">
                <div class="media-body me-3">
                    <h2 class="text-black font-w700">{{ $total }}</h2>
                    <p class="mb-0 text-black font-w600 mb-2">{{ __($title) }}</p>
                    <span class="text-secondary">
                        {{ $detail }}
                    </span>
                </div>
                {{ $icon }}

            </div>
        </div>
    </div>
</li>
