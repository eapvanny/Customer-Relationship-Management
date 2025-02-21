<!-- Master page  -->
@extends('backend.layouts.master')

<!-- Page title -->
@section('pageTitle')
    Tickets
@endsection
<!-- End block -->

@section('extraStyle')
    <style>
        /* .modal-fullscreen .modal-dialog {
                width: 100%;
                max-width: none;
                height: 100%;
                margin: 0;
            }

            .modal-fullscreen .modal-content {
                height: 100%;
                display: flex;
                flex-direction: column;
            }

            .modal-body {
                flex: 1;
                display: flex;
                flex-direction: column;
            } */

        .chat-container {
            flex-grow: 1;
            overflow-y: auto;
        }

        .chat-input-container {
            position: sticky;
            bottom: 0;
            width: 100%;
            background: white;
            padding: 10px;
        }
    </style>
@endsection

<!-- Page body extra class -->
@section('bodyCssClass')
@endsection
<!-- End block -->
@php
    use App\Http\Helpers\AppHelper;
@endphp
<!-- BEGIN PAGE CONTENT-->
@section('pageContent')
    <!-- Section header -->
    <section class="content-header">
        <ol class="breadcrumb">
            <li><a href="{{ URL::route('dashboard.index') }}"><i class="fa fa-dashboard"></i> {{ __('Dashboard') }} </a></li>
            <li class="active"> {{ __('Tickets') }} </li>
        </ol>
    </section>
    <!-- ./Section header -->
    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="wrap-outter-header-title">
                    <h1>
                        {{ __('Tickets') }}
                        <small> {{ __('List') }} </small>
                    </h1>
                    <div class="box-tools pull-right">
                        <button id="filters" class="btn btn-outline-secondary" data-bs-toggle="collapse"
                            data-bs-target="#filterContainer">
                            <i class="fa-solid fa-filter"></i> {{ __('Filter') }}
                        </button>
                        <a class="btn btn-info text-white" href="{{ URL::route('ticket.create') }}"><i
                                class="fa fa-plus-circle"></i> {{ __('Add New') }} </a>
                    </div>
                </div>

                <div class="wrap-outter-box">
                    <div class="box box-info">
                        <div class="box-header">
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <form action="{{ route('ticket.index') }}" method="GET" id="filterForm">
                                        <div class="wrap_filter_form @if (!$is_filter) collapse @endif"
                                            id="filterContainer">
                                            <a id="close_filter" class="btn btn-outline-secondary btn-sm">
                                                <i class="fa-solid fa-xmark"></i>
                                            </a>
                                            <div class="row">
                                                <div class="col-xl-4">
                                                    <div class="form-group">
                                                        <label for="Department">{{ __('Department') }}</label>
                                                        {!! Form::select('department_id', $departments, request('department_id'), [
                                                            'placeholder' => __('Select a department'),
                                                            'class' => 'form-control select2',
                                                            'id' => 'department_id',
                                                        ]) !!}
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="form-group">
                                                        <label for="status_id">{{ __('Status') }} <span
                                                                class="text-danger">*</span></label>
                                                        {!! Form::select(
                                                            'status_id',
                                                            [
                                                                AppHelper::STATUS_OPEN => __(AppHelper::STATUS[AppHelper::STATUS_OPEN]),
                                                                AppHelper::STATUS_PENDING => __(AppHelper::STATUS[AppHelper::STATUS_PENDING]),
                                                                AppHelper::STATUS_RESOLVED => __(AppHelper::STATUS[AppHelper::STATUS_RESOLVED]),
                                                                AppHelper::STATUS_CLOSED => __(AppHelper::STATUS[AppHelper::STATUS_CLOSED]),
                                                            ],
                                                            request('status_id'),
                                                            ['class' => 'form-control select2', 'id' => 'status_id', 'placeholder' => __('Select a status')],
                                                        ) !!}
                                                    </div>
                                                </div>
                                                <div class="col-xl-4">
                                                    <div class="form-group">
                                                        <label for="priority_id">{{ __('Priority') }} <span
                                                                class="text-danger">*</span></label>
                                                        {!! Form::select(
                                                            'priority_id',
                                                            [
                                                                AppHelper::PRIORITY_LOW => __(AppHelper::PRIORITY[AppHelper::PRIORITY_LOW]),
                                                                AppHelper::PRIORITY_MEDIUM => __(AppHelper::PRIORITY[AppHelper::PRIORITY_MEDIUM]),
                                                                AppHelper::PRIORITY_HIGH => __(AppHelper::PRIORITY[AppHelper::PRIORITY_HIGH]),
                                                                AppHelper::PRIORITY_URGENT => __(AppHelper::PRIORITY[AppHelper::PRIORITY_URGENT]),
                                                            ],
                                                            request('priority_id'),
                                                            ['class' => 'form-control select2', 'id' => 'priority_id', 'placeholder' => __('Select a priority')],
                                                        ) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 mt-2">
                                                    <button id="apply_filter"
                                                        class="btn btn-outline-secondary btn-sm float-end" type="submit">
                                                        <i class="fa-solid fa-magnifying-glass"></i> {{ __('Apply') }}
                                                    </button>
                                                    <a href="{{ route('ticket.index') }}"
                                                        class="btn btn-outline-secondary btn-sm float-end me-1">
                                                        <i class="fa-solid fa-xmark"></i> {{ __('Cancel') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>

                                </div>
                            </div>
                        </div>
                        <!-- /.box-header -->
                        <div class="box-body margin-top-20">
                            <div class="table-responsive mt-4">
                                <table id="datatabble"
                                    class="table table-bordered table-striped list_view_table display responsive no-wrap datatable-server"
                                    width="100%">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th> {{ __('Department') }} </th>
                                            <th> {{ __('ID No.') }} </th>
                                            <th> {{ __('Employee Name') }} </th>
                                            <th> {{ __('Subject') }} </th>
                                            <th> {{ __('Description') }} </th>
                                            <th> {{ __('Status') }} </th>
                                            <th> {{ __('Priority') }} </th>
                                            <th class="notexport"> {{ __('Action') }} </th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                    </tbody>

                                </table>
                            </div>
                        </div>
                        <!-- /.box-body -->
                        <div class="btn-group">
                            <form id="myAction" method="POST">
                                @csrf
                                <input name="_method" type="hidden" value="DELETE">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="modal modal-lg fade" id="showTicketModal" tabindex="-1" role="dialog"
        aria-labelledby="showTicketModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content rounded-0">
                <div class="modal-header">
                    <h5 class="modal-title" id="showTicketModalLabel">{{ __('Message Messenger') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" id="btnClose"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body d-flex flex-column">
                    <!-- Chat Messages (Scrollable) -->
                    <div class="chat-container flex-grow-1 overflow-auto p-3" id="chat-box">
                        <!-- Messages will be dynamically loaded here -->
                    </div>

                    <!-- Fixed Input for New Message -->
                    <div class="chat-input-container border-top p-3 bg-white">
                        <input type="hidden" id="ticket_id">
                        <div class="d-flex align-items-center">
                            <textarea id="chatMessage" class="form-control me-2" rows="1" placeholder="Type a message"></textarea>
                            <button type="button" class="btn btn-primary" id="sendMessage">{{ __('Send') }}</button>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-close">
                        {{ __('Close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- /.content -->
@endsection
<!-- END PAGE CONTENT-->

<!-- BEGIN PAGE JS-->
@section('extraScript')
    <script type="text/javascript">
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            t = $('#datatabble').DataTable({
                processing: false,
                serverSide: true,
                ajax: {
                    url: "{!! route('ticket.index', request()->all()) !!}",
                },
                pageLength: 10,
                columns: [{
                        data: 'id',
                        name: 'id'
                    },
                    {
                        data: 'department',
                        name: 'department'
                    },
                    {
                        data: 'id_card',
                        name: 'id_card'
                    },
                    {
                        data: 'employee_name',
                        name: 'employee_name'
                    },
                    {
                        data: 'subject',
                        name: 'subject'
                    },
                    {
                        data: 'description',
                        name: 'description'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },
                    {
                        data: 'priority',
                        name: 'priority'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false
                    }
                ],
            });

            $(document).on('click', '.show-ticket', function() {
                var ticketId = $(this).data('id');

                $('#ticket_id').val('');
                $('#chat-box').html('');
                $("#chatMessage").val('');

                $.ajax({
                    url: "{!! route('ticket.show', ':id') !!}".replace(':id', ticketId),
                    method: 'GET',
                    success: function(response) {
                        $('#ticket_id').val(ticketId);

                        let lastMessageId = 0;

                        function fetchMessages() {
                            $.ajax({
                                url: `/chat/messages/${ticketId}`,
                                method: "GET",
                                data: {
                                    lastMessageId: lastMessageId, // Send the last message ID to the server
                                },
                                success: function(response) {
                                    if (response.length > 0) {
                                        response.forEach(msg => {
                                            if (msg.id >
                                                lastMessageId
                                                ) { // Only append truly new messages
                                                let isCurrentUser = msg
                                                    .user_id ===
                                                    {{ auth()->id() }};
                                                let alignmentClass =
                                                    isCurrentUser ?
                                                    'd-flex justify-content-end' :
                                                    'd-flex justify-content-start';
                                                let bgColor =
                                                    isCurrentUser ?
                                                    'bg-primary text-white' :
                                                    'bg-secondary text-white';

                                                // Add user photo only for received messages (bg-secondary)
                                                let photoHtml = !
                                                    isCurrentUser ?
                                                    `<img src="${msg.user_photo_url}" class="rounded-circle" style="width: 20px; height: 20px; margin-right: 10px;">` :
                                                    '';

                                                // Check if chat-box exists, if not create it with proper styling
                                                if ($("#chat-box")
                                                    .length === 0) {
                                                    $("#modal-body").append(
                                                        `<div id="chat-box" style="height: 50%; overflow-y: auto;"></div>`
                                                    );
                                                }

                                                // Determine if the message is long or short
                                                let messageLength = msg
                                                    .message.length;
                                                let messageStyle = (
                                                        messageLength > 70 ?
                                                        'max-width: 50%; word-wrap: break-word;' :
                                                        '') +
                                                    (isCurrentUser ?
                                                        ' margin-left: auto;' :
                                                        ' margin-right: auto;'
                                                    );

                                                $("#chat-box").append(`
                            <div class="message ${alignmentClass} my-2" style="display: flex; justify-content: ${isCurrentUser ? 'flex-end' : 'flex-start'};">
                                <div class="d-flex align-items-start">
                                    ${photoHtml} <!-- Show the photo only for received messages -->
                                    <div class="p-2 rounded ${bgColor}" style="${messageStyle}">
                                        ${msg.message}
                                    </div>
                                </div>
                            </div>
                        `);
                                                lastMessageId = msg
                                                    .id; // Update lastMessageId after adding new messages
                                            }
                                        });
                                    }
                                }
                            });
                        }


                        // Initial fetch of messages
                        fetchMessages();
                        // Send a new message
                        $("#sendMessage").off("click").on("click", function() {
                            let message = $("#chatMessage").val().trim();
                            if (message === "") return;

                            $.ajax({
                                url: "{{ route('send.message') }}",
                                method: "POST",
                                data: {
                                    ticket_id: ticketId,
                                    message: message,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    $("#chatMessage").val(
                                        ""); // Clear input field

                                    // Append message only once after sending
                                    let isCurrentUser = response.user_id ===
                                        {{ auth()->id() }};
                                    let alignmentClass = isCurrentUser ?
                                        'd-flex justify-content-end' :
                                        'd-flex justify-content-start';

                                    let bgColor = isCurrentUser ?
                                        'bg-primary text-white' :
                                        'bg-secondary text-white';

                                    $("#chat-box").append(`
                            <div class="message ${alignmentClass} my-2">
                                <div class="p-2 rounded ${bgColor}" style="max-width: 60%;">
                                    ${response.message}
                                </div>
                            </div>
                        `);
                                    lastMessageId = response
                                        .id; // Ensure lastMessageId is updated immediately
                                }
                            });
                        });
                        // Refresh chat messages every 3 seconds
                        setInterval(fetchMessages, 3000);

                        $('#showTicketModal').modal('show');
                    },
                    error: function() {
                        alert('Failed to load ticket details.');
                    }
                });
            });
            // Clear modal content on close
            $(document).on('click', '#btn-close, #btnClose', function() {
                $('#showTicketModal').modal('hide');
                $('#chat-box').html('');
                $('#ticket_id').val('');
                $("#chatMessage").val("");
            });

            $('#close_filter').click(function(){
                $("#filters").trigger('click');
            });

            //delete grade_level
            $('#datatabble').delegate('.delete', 'click', function(e) {
                let action = $(this).attr('href');
                console.log()
                $('#myAction').attr('action', action);
                e.preventDefault();
                swal({
                    title: 'Are you sure?',
                    text: 'You will not be able to recover this record!',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dd4848',
                    cancelButtonColor: '#8f8f8f',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'No, keep it'
                }).then((result) => {
                    if (result.value) {
                        $('#myAction').submit();
                    }
                });
            });
        });
    </script>
@endsection
<!-- END PAGE JS-->
