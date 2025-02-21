<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle') Dashboard @endsection
<!-- End block -->
@section('extraStyle')
    <style>
        .notification li {
            font-size: 16px;
        }
        .notification li.info span.badge {
            background: #00c0ef;
        }
        .notification li.warning span.badge {
            background: #f39c12;
        }
        .notification li.success span.badge {
            background: #00a65a;
        }
        .notification li.error span.badge {
            background: #dd4b39;
        }
        .total_bal {
            margin-top: 5px;
            margin-right: 5%;
        }
    </style>
@endsection
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Main content -->
    <section class="content p-0">

        @if (in_array($role_ref_id,[AppHelper::USER_SUPER_ADMIN,AppHelper::USER_SUPER_ADMIN_ORG,AppHelper::USER_ADMIN]))
            @include('backend.user.dashboard.admin')
        @else
            @if (isset($innerHTML))
                {!! $innerHTML !!}
            @else
                <p>Welcome!</p>
            @endif
        @endif
    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script src="{{asset(mix('js/dashboard.js'))}}"></script>
    @if (in_array($role_ref_id,[AppHelper::USER_SUPER_ADMIN,AppHelper::USER_SUPER_ADMIN_ORG,AppHelper::USER_ADMIN]))
        <script type="text/javascript">
            window.smsLabel = @php echo json_encode(array_keys($monthWiseSms)) @endphp;
            window.smsValue = @php echo json_encode(array_values($monthWiseSms)) @endphp;
            window.attendanceLabel = @php echo json_encode(array_keys($attendanceChartPresentData)) @endphp;
            window.presentData = @php echo json_encode(array_values($attendanceChartPresentData)) @endphp;
            window.absentData = @php echo json_encode(array_values($attendanceChartAbsentData)) @endphp;
            $(document).ready(function () {
                Dashboard.init();
                Dashboard.initAcademicCalendar();
            });
        </script>
    @endif
    <script type="text/javascript">
        $(document).ready(function(){

        });
    </script>
@endsection
<!-- END PAGE JS-->
