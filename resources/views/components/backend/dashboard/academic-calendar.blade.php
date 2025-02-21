<div {{ $attributes }}>
    <div class="card rounded-4 border-none border-0 shadow-sm me-0">
        <div class="card-body p-4">
            <div class="media  d-flex justify-content-between ">
                <div class="media-body">
                    <p class="mb-0 text-black font-w600 mb-2">{{__('Academic Calendar')}}</p>
                    <span id="menu-navi">
                        <button type="button" id="today" class="calendar-btn calendar-move-today">Today</button>
                        <button type="button" class="calendar-btn calendar-move-day"><i id="btn-left" class="calendar-icon ic-arrow-line-left"></i></button>
                        <button type="button" class="calendar-btn calendar-move-day"><i id="btn-right" class="calendar-icon ic-arrow-line-right"></i></button>
                    </span>
                    <span id="year-month" class="calendar-render-range"></span>
                </div>
            </div>
            <div id="calendar" style="height: 500px"></div>
        </div>
    </div>
</div>

@push('scripts')
<script type="text/javascript">
    var studentCharts = @json([]);
    $(document).ready(function () {
        Dashboard.initAcademicCalendar();

    });
</script>
@endpush
