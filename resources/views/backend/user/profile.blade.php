<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    Profile
@endsection
<!-- End block -->
@section('extraStyle')
    <style>
        .text-center{
            margin: auto
        }
    </style>
@endsection
<!-- Page body extra class -->
@section('bodyCssClass')
@endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Main content -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li class="active"> {{ __('User profile') }} </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="col-md-12">
            <div class="wrap-outter-header-title">
                <h1>
                    {{ $user->name }}
                    <small> {{ __('Details') }} </small>
                </h1>
            </div>
        </div>
        <div class="wrap-outter-box">
            <div class="row">
                <div class="col-lg-12">
                    <div class="box box-info">
                        <div class="box-body box-profile">
                            <div class="row">
                                <div class="col-lg-3">
                                    <form id="uploadForm" method="post" enctype="multipart/form-data"
                                        action="{{ route('users.updateProfilePhoto', $user->id) }}">
                                        @csrf
                                        <input type="hidden" name="old_photo" value="{{ $user->photo }}">
                                        <input type="file" id="image_file" name="photo" style="display: none">
                                    </form>
                                    <div class="position-relative text-center box-img">
                                        <div id="loadingGif"
                                            class="position-absolute top-50 start-50 translate-middle loading">
                                            <i class="fa-solid fa-spinner fa-spin-pulse fa-2xl"></i>
                                        </div>
                                        <img class="profile-user-img img-fluid img-circle"
                                            src="{{ $user->photo ? asset('storage/' . $user->photo) : asset('images/avatar.png') }}">

                                        <button type="button" onclick="openFileUploader()"
                                            class="position-absolute top-0 text-right translate-left">
                                            <i class="fas fa-pen-to-square"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <h3 class="profile-username text-center my-1"> {{ $user->name }}</h3>
                                    <p class="text-muted text-center">{{ $user->role->name }}</p>
                                    <ul class="list-group list-group-unbordered profile-log">
                                        <li class="list-group-item size">
                                            <strong><i class="fa fa-user margin-r-5"></i> {{ __('Username') }} :</strong>
                                            <span>{{ $user->username }}</span>
                                        </li>
                                        <li class="list-group-item size">
                                            <strong><i class="fa fa-info-circle margin-r-5"></i> {{ __('Full name') }}
                                                :</strong>
                                            <span>
                                                {{ $user->name }}
                                            </span>
                                        </li>
                                        <li class="list-group-item size">
                                            <strong><i class="fa fa-envelope margin-r-5"></i> {{ __('Email') }}
                                                :</strong>
                                            <span>{{ $user->email }}</span>
                                        </li>
                                        <li class="list-group-item size">
                                            <strong><i class="fa fa-phone margin-r-5"></i> {{ __('Phone no') }} :</strong>
                                            <span>{{ $user->phone_no }}</span>
                                        </li>
                                        <li class="list-group-item size">
                                            <strong><i class="fa-solid fa-clock margin-r-5"></i> {{ __('Created At') }}
                                                :</strong>
                                            <span>{{ date('F j,Y', strtotime($user->created_at)) }}</span>
                                        </li>
                                        {{-- <div class="mt-3">
                                            <a href="#"
                                                class="btn btn-primary text-white btn-block btnUpdate float-end"><b>
                                                    {{ __('Update') }} </b></a>
                                        </div> --}}
                                    </ul>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                    <!-- /.box -->
                </div>
                <div class="col-md-3"></div>

                <!-- /.box -->
            </div>
        </div>

        <!-- /.row -->
    </section>
    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function() {
            var btnUpdate = $('.btnUpdate');
            var btnCancel = $('.btnCancel');
            Login.profileUpdate();
            Generic.initCommonPageJS();

            btnUpdate.on('click', function() {
                $(this).hide();
            });

            btnCancel.on('click', function() {
                btnUpdate.show();
            });
        });

        function openFileUploader() {
            document.getElementById('image_file').click();
        }

        document.getElementById('image_file').addEventListener('change', function() {
            const form = document.getElementById('uploadForm');

            // Display the loading gif
            document.getElementById('loadingGif').style.display = 'block';

            // Check if a file is selected
            if (this.files.length > 0) {
                const formData = new FormData(form);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: formData
                    })
                    .then(response => response.json()) // if expecting JSON response
                    .then(data => {
                        console.log(data);
                        // Handle success or error responses as needed
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Handle error
                    })
                    .finally(() => {
                        // Hide the loading gif
                        document.getElementById('loadingGif').style.display = 'none';
                    });
            }
        });
        document.getElementById('image_file').addEventListener('change', function() {
            document.getElementById('uploadForm').submit();
        });
    </script>
@endsection
<!-- END PAGE JS-->
