<div>
    <div class="box-header with-border ps-0 mb-2">
        <h3 class="box-title">{{ __('Rooms') }}</h3>
        <div class="box-tools pull-right">
            <div class="btn-group">
                <button type="button" title="Add" class="btn btn-info text-white" id="btn_add_exam_event_room" data-bs-toggle="modal" data-bs-target="#addExamEventRoomModal"><i class="fa fa-plus"></i> {{ __('Add Rooms') }}</button>
            </div>
        </div>


    </div>

    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <table class="table table-bordered" id="exam_room_table" width="100%">
                    <thead>
                        <tr>
                            <th style="width: 24px;">{{ __('No.') }}</th>
                            <th>{{ __('Room Code') }}</th>
                            <th>{{ __('Building') }}</th>
                            <th>{{ __('Room Name') }}</th>
                            <th>{{ __('Capacity') }}</th>
                            <th style="max-width: 24px;">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($examRooms as $i=>$it)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td>{{ $it->room->code }}</td>
                                <td>{{ $it->room->building->name }}</td>
                                <td>{{ $it->room->name }}</td>
                                <td>{{ $it->capacity }}</td>
                                <td>
                                    <form class="myAction" method="POST" action="{{ URL::route('exam_event.remove_room', $it->id) }}">
                                        @csrf
                                        <input name="_method" type="hidden" value="DELETE">
                                        <button type="submit" class="btn btn-danger btn-sm btn-remove-room" title="Delete">
                                            <i class="fa fa-fw fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
    <div class="modal modal-lg fade" id="addExamEventRoomModal" tabindex="-1" aria-labelledby="addExamEventRoomModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addExamEventRoomModalLabel">{{ __('Add Exam Rooms') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="room_table" width="100%">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" class="dont-style" id="check_all_room"/></th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Building') }}</th>
                                    <th>{{ __('Name') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rooms as $room)
                                <tr>
                                    <td><input type="checkbox" class="dont-style room-check" value="{{ $room->id }}"/></td>
                                    <td>{{ $room->code; }}</td>
                                    <td>{{ $room->building->name }}</td>
                                    <td>{{ $room->name }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    <button type="button" class="btn btn-primary" id="btnAddSelected">{{ __('Add Selected') }}</button>
                </div>
            </div>
        </div>
    </div>
    <form id="form_added_rooms" method="post" action="{{ $postActionUrl }}">
        @csrf
        <input type="hidden" name="id" value="{{ $id }}"/>
    </form>
</div>
@push('scripts')
    <script>
        var roomDt = null;
        var roomChecks = [];
        var examRoomDt = null;
        $(function(){
            roomDt = $('#room_table').DataTable({
                pageLength: 25,
                lengthChange: true,
                orderCellsTop: true,
                responsive: true,
            });
            examRoomDt = $('#exam_room_table').DataTable({
                pageLength: 25,
                lengthChange: true,
                orderCellsTop: true,
                responsive: true,
            });
            $("#btnAddSelected").click(function(e){
                $.each(roomChecks, function(i, v){
                    $("#form_added_rooms").append('<input type="hidden" name="room_id[]" value="' + v + '"/>');
                });
                $("#form_added_rooms").submit();
            });
        });
        $('html #exam_room_table').on('submit', 'form.myAction', function (e) {
            e.preventDefault();
            var that = this;
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
                    that.submit();
                }
            });
        });
        $(document).on('change', '.room-check', function(e){
            var checked = $(this).prop("checked");
            var room_id = $(this).val();
            if (checked) {
                if($.inArray(room_id, roomChecks) === -1) {
                    roomChecks.push(room_id);
                }
            } else {
                roomChecks.splice($.inArray(room_id, roomChecks), 1);
            }

        });
        $(document).on('change', '#check_all_room', function(e){
            var prop = $(this).prop("checked");
            roomDt.column(0).nodes().each(function(cell, i) {
                var room_id = $(cell).find('.room-check').val();
                $(cell).find('.room-check').prop("checked", prop);
                if(prop){
                    if($.inArray(room_id, roomChecks) === -1) {
                        roomChecks.push(room_id);
                    }
                }else{
                    roomChecks.splice($.inArray(room_id, roomChecks), 1);
                }

            });
        });
    </script>
@endpush
