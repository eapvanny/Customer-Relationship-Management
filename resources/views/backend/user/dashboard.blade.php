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

        <div class="box-banner mb-3">
            <div class="row px-3">
                <div class="col-12 col-md-12 col-xxl-6 fs-4 text-secondary fw-bold pt-3">
                    {{ optional(getAuthUser()->organization)->name }}
                </div>

                <div class="col-12 col-md-12 col-xxl-6">
                    <form action="{{route('user.dashboard')}}" method="GET" class="rouned-2" >

                        @method('GET')
                        <div class="row mt-1">
                            <x-backend.academic-year-select class="col-6 col-md-4 col-xxl-4" name="academic_year_id" value="{{ $academic_year_id }}" id="academic_year_id" autoSelectCurrentYear="true" :showLabel="false"/>

                            <div class="col-6 col-md-4 col-xxl-4">
                                <div class="form-group">
                                    <input type="date" class="form-control" value="{{ $dateFrom }}" id="from-date" name="date_from" onchange="this.form.submit()"/>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-xxl-4">
                                <div class="form-group">
                                    <input type="date" class="form-control" value="{{ $dateTo }}" id="to-date" name="date_to" onchange="this.form.submit()"/>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>

        <div class="row ps-4 pe-4 d-none">
            <div class="col-12">
                <div class="alert alert-warning" role="alert">
                    {{ __('You have a list of tasks(:totalTasks) to complete', ['totalTasks' => 5]) }}
                    <a class="btn btn-light bg-transparent" data-bs-toggle="collapse" href="#collapseToDo" role="button" aria-expanded="false" aria-controls="collapseExample">
                        <i class="fa-solid fa-chevron-down"></i>
                    </a>
                </div>

                <div class="collapse mb-2" id="collapseToDo">
                    <div class="card card-body">
                        <div class="alert alert-info" role="alert">
                            A simple info alert—check it out!
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if (!empty($academic_year_id))
        <div class='row ps-4 pe-4'>
            <ul class="slide_cards_reviews owl-carousel owl-theme ">
                @can('academic.class')
                    <x-backend.dashboard.total-class-count-box academicYearId="{{ $academic_year_id }}"/>
                @endcan
                @can('student.index')
                    <x-backend.dashboard.total-student-count-box academicYearId="{{ $academic_year_id }}"/>
                @endcan
                @can('teacher.index')
                    <x-backend.dashboard.total-teacher-count-box/>
                @endcan
                @can('parents.index')
                    <x-backend.dashboard.total-parent-count-box academicYearId="{{ $academic_year_id }}"/>
                @endcan
                @canany(['student_attendance.index', 'student_leave.index'])
                    <x-backend.dashboard.student-absence-count-box :dateFrom="$dateFrom" :dateTo="$dateTo"/>
                @endcan
                @canany(['employee_attendance.index', 'hrm.leave.index'])
                    <x-backend.dashboard.employee-absence-count-box :dateFrom="$dateFrom" :dateTo="$dateTo"/>
                @endcan
            </ul>
        </div>
        @endif
        <div class="row ps-4 pe-4">
            <div class="col-xl-6">
                <div class="row">
                    <x-backend.dashboard.academic-calendar class="col-12"/>

                </div>

            </div>
            <div class="col-xl-6">
                <div class="row">
                    <x-backend.dashboard.notifications class="col-12 mb-4"/>
                    @role('Super Admin Org', 'Super Admin')
                    <x-backend.dashboard.student-enrollment-chart class="col-12"/>
                    @endrole
                </div>
            </div>


        </div>
    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script src="{{asset(mix('js/dashboard.js'))}}"></script>
    <script>
        $(function(e){
            $("#academic_year_id").change(function(e){
                $(this).closest("form").submit();
            });
        });
    </script>
@endsection



