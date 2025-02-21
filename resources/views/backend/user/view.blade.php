<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')User Profile @endsection
<!-- End block -->

<!-- Page body extra class -->
@section('bodyCssClass') @endsection
<!-- End block -->

<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Main content -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{URL::route('user.dashboard')}}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li><a href="{{URL::route('user.index')}}"><i class="fa-solid fa-user"></i> {{ __('User') }} </a></li>
            <li class="active"> {{ __('User profile') }} </li>
        </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="col-md-12">
            <div class="wrap-outter-header-title">
                <h1>
                {{$user->name}}
                    <small> {{ __('Details') }} </small>
                </h1>
                <div class="box-tools pull-right">
                    <div class="btn-group">
                        <a href="#"  class="btn-pr btn-sm-pr btn-print btnPrintInformation"><i class="fa fa-print"></i> {{ __('Print') }} </a>
                    </div>
                    <div class="btn-group">
                        <a href="{{URL::route('user.show',$user->id)}}?print_idcard=1" target="_blank" class="btn-pr btn-sm-pr"><i class="fa fa-id-card"></i> {{ __('ID Card') }} </a>

                    </div>
                    <div class="btn-group">
                        @can('user.edit')
                        <a href="{{URL::route('user.edit',$user->id)}}" class="btn-pr btn-sm-pr"><i class="fa fa-edit"></i> {{ __('Edit') }} </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
        <div class="wrap-outter-box">
            <div class="row">
                <div class="col-lg-12">
                    <div class="box box-info">
                        <div class="box-body box-profile">
                            <div class="row">
                                <div class="col-lg-3">
                                    <form id="uploadForm" method="post" enctype="multipart/form-data" action="{{ route('users.updateProfilePhoto', $user->id) }}">
                                        @csrf
                                        <input type="hidden" name="old_photo" value="{{ $user->photo }}">
                                        <input type="file" id="image_file" name="photo" style="display: none">
                                    </form>
                                    <div class="position-relative text-center box-img">
                                        <div id="loadingGif" class="position-absolute top-50 start-50 translate-middle loading">
                                            <i class="fa-solid fa-spinner fa-spin-pulse fa-2xl"></i>
                                        </div>
                                        <img class="profile-user-img img-fluid img-circle" src="@if($user->photo){{ Storage::url($user->photo) }} @else {{ asset('images/avatar.png')}} @endif">
                                        <button type="button" onclick="openFileUploader()" class="position-absolute top-0 text-right translate-left">
                                            <i class="fas fa-pen-to-square"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-lg-9">
                                    <h3 class="profile-username text-center my-1">  {{$user->name}}</h3>
                                    <p class="text-muted text-center">
                                        @foreach ($userRoles as $role)
                                            {{ $role->name }}
                                        @endforeach
                                    </p>
                                    <ul class="list-group list-group-unbordered profile-log">
                                        <li class="list-group-item size">
                                            <strong> {{ __('Username') }} :</strong>
                                            <span>{{$user->username ?? 'N/A'}}</span>
                                        </li>
                                        <li class="list-group-item size">
                                            <strong><i class="fa fa-info-circle margin-r-5"></i> {{__('Full name') }} :</strong>
                                            <span>
                                                {{$user->name ?? 'N/A'}}
                                            </span>
                                        </li>
                                        <li class="list-group-item size">
                                            <strong><i class="fa fa-envelope margin-r-5"></i>  {{ __('Email') }} :</strong>
                                            <span>{{$user->email ?? 'N/A'}}</span>
                                        </li>
                                        <li class="list-group-item size">
                                            <strong><i class="fa fa-phone margin-r-5"></i> {{ __('Phone no') }} :</strong>
                                            <span>{{$user->phone_no ?? 'N/A'}}</span>
                                        </li>
                                        <li class="list-group-item size">
                                            <strong><i class="fa-solid fa-clock margin-r-5"></i> {{ __('Created At') }} :</strong>
                                            <span>{{date('F j,Y', strtotime($user->created_at))}}</span>
                                        </li>
                                        {{-- <div class="mt-3">
                                            <a href="#" class="btn btn-primary btn-block btnUpdate float-end"><b> {{ __('Update') }} </b></a>
                                        </div> --}}
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
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
        $(document).ready(function () {
            $('.btnPrintInformation').click(function () {
                $('ul.nav-tabs li:not(.active)').addClass('no-print');
                $('ul.nav-tabs li.active').removeClass('no-print');
                window.print();
            });
            Login.profileUpdate();
            Generic.initCommonPageJS();
        });
        function openFileUploader() {
        document.getElementById('image_file').click();
    }

    document.getElementById('image_file').addEventListener('change', function () {
        const form = document.getElementById('uploadForm');

        // Display the loading gif
        document.getElementById('loadingGif').style.display = 'block';

        // Check if a file is selected
        if (this.files.length > 0) {
            const formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
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
