<div {{ $attributes }}>
    <div class="card rounded-4 border-none border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="media  d-flex justify-content-between ">
                <div class="media-body">
                    <p class="mb-0 text-black font-w600 mb-2">{{__('Students Reporting')}}</p>
                </div>
            </div>
            <div id="chartStudent" class="p-4"></div>
        </div>
    </div>
</div>
@push("scripts")
<script type="text/javascript">
    var studentCharts = @json($studentCharts);
    $(document).ready(function () {
        Dashboard.initStudentEnrollmentChart(studentCharts);

    });
</script>
@endpush
