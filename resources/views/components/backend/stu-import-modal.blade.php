<div class="modal modal-lg fade" id="{{ $modalId }}" aria-labelledby="{{ $modalId }}Label" aria-hidden="true" >
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ $formAction }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ $modalId }}Label">{{ __('Import File') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div id="student-import">

                </div>
                <div class="modal-body">
                    <div class="card mb-2">
                        <div class="card-body">
                            <span class="fw-bold">{{ __('Note') }}:</span><br/>
                            - {{ __('File size must not exceed :number MB.', ['number' => 20]) }}<br/>
                            - {{ __('File extension must be excel document(.xlsx).') }}<br/>
                            - {{ __('To download sample file,') }} <a href="{{ $sampleFileUrl }}" class="btn btn-light btn-sm"><i class="fa-solid fa-download"></i> {{ __('Click here') }}</a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">{{ $slot }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="form-group has-feedback">
                            <label for="import_file" class="form-label">{{ __('Select file to import') }}</label>
                            <input class="form-control" type="file" name="import_file" id="import_file">
                            <span class="form-control-feedback"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-success" name="submit"><i class="fa-solid fa-file-import"></i> {{ __('Import') }}</button>
                </div>
            </form>
        </div>
    </div>
   
</div>
@push('scripts')
<script type="text/javascript">

$(document).ready(function() {
    function initializeSelect2() {
        $('#academic_year_ids, #department_ids, #major_ids, #academic_degree_ids, #grade_level_ids').select2({
            dropdownParent: $("#importModals")
        });
    }

    function validateField(fieldId) {
        var fieldValue = $(fieldId).val();
        if (!fieldValue) {
            $(fieldId).closest('.form-group').addClass('has-error');
            $(fieldId).siblings('.form-control-feedback').text('The field is required.').css('color', 'red');
            return false;
        } else {
            $(fieldId).closest('.form-group').removeClass('has-error');
            $(fieldId).siblings('.form-control-feedback').text('');
            return true;
        }
    }

    $('#stu-modal').on('click', function() {
        $.ajax({
            url: "{{ route('student.import') }}", 
            method: 'GET',
            success: function(response) {
                $('#student-import').empty();
                $('#student-import').html(response);
                initializeSelect2();
              
                // Clear previous errors
                $('.form-group').removeClass('has-error');
                $('.form-control-feedback').text('');

                function resetDropdown(selectId) {
                    $(selectId).empty().append('<option value="">{{ __("Please Choose") }}</option>');
                }

                $('#academic_year_ids').on('change', function() {
                    var academicYearId = $(this).val();
                    resetDropdown('#department_ids');
                    resetDropdown('#major_ids');
                    resetDropdown('#academic_degree_ids');
                    resetDropdown('#grade_level_ids');

                    if (academicYearId) {
                        $.ajax({
                            url: '{{ route("get.grade.levels") }}',
                            type: 'GET',
                            data: { academic_year_id: academicYearId },
                            success: function(data) {
                                var departmentSelect = $('#department_ids');
                                $.each(data.departments, function(index, department) {
                                    departmentSelect.append('<option value="' + department.id + '">' + department.name + '</option>');
                                });
                            },
                            error: function() {
                                alert('Error fetching departments. Please try again.');
                            }
                        });
                    }
                });

                $('#department_ids').on('change', function() {
                    var departmentId = $(this).val();
                    resetDropdown('#major_ids');
                    resetDropdown('#academic_degree_ids');
                    resetDropdown('#grade_level_ids');

                    if (departmentId) {
                        $.ajax({
                            url: '{{ route("get.grade.levels") }}',
                            type: 'GET',
                            data: { department_id: departmentId },
                            success: function(data) {
                                var majorSelect = $('#major_ids');
                                $.each(data.majors, function(index, major) {
                                    majorSelect.append('<option value="' + major.id + '">' + major.name + '</option>');
                                });
                            },
                            error: function() {
                                alert('Error fetching majors. Please try again.');
                            }
                        });
                    }
                });

                $('#major_ids').on('change', function() {
                    var majorId = $(this).val();
                    resetDropdown('#academic_degree_ids');
                    resetDropdown('#grade_level_ids');

                    if (majorId) {
                        $.ajax({
                            url: '{{ route("get.grade.levels") }}',
                            type: 'GET',
                            data: { major_id: majorId },
                            success: function(data) {
                                var academicDegreeSelect = $('#academic_degree_ids');
                                $.each(data.academicDegrees, function(index, academicDegree) {
                                    academicDegreeSelect.append('<option value="' + academicDegree.id + '">' + academicDegree.name + '</option>');
                                });
                            },
                            error: function() {
                                alert('Error fetching academic degrees. Please try again.');
                            }
                        });
                    }
                });

                $('#academic_degree_ids').on('change', function() {
                    var majorId = $('#major_ids').val();
                    var academicDegreeId = $(this).val();
                    resetDropdown('#grade_level_ids');

                    if (majorId && academicDegreeId) {
                        $.ajax({
                            url: '{{ route("get.grade.levels") }}',
                            type: 'GET',
                            data: {
                                major_id: majorId,
                                academic_degree_id: academicDegreeId 
                            },
                            success: function(data) {
                                var gradeLevelSelect = $('#grade_level_ids');
                                $.each(data.gradeLevels, function(index, gradeLevel) {
                                    gradeLevelSelect.append('<option value="' + gradeLevel.id + '">' + gradeLevel.name + '</option>');
                                });
                            },
                            error: function() {
                                alert('Error fetching grade levels. Please try again.');
                            }
                        });
                    }
                });
            }
          
        });
    });

    // Validate before submitting
    $('form').on('submit', function(e) {
        var isValid = true;
        isValid &= validateField('#academic_year_ids');
        isValid &= validateField('#department_ids');
        isValid &= validateField('#major_ids');
        isValid &= validateField('#academic_degree_ids');
        isValid &= validateField('#grade_level_ids');
        isValid &= validateField('#import_file');

        if (!isValid) {
            e.preventDefault(); 
        } 

        $.ajax({
            url: "{{ route('student.import') }}", 
            method: 'GET',
            success: function(response) {
                if(isValid){
                    e.preventDefault(); 
                    swal({
                        title: 'Successfully!',
                        text: response.message,
                        type: "success",
                        timer: 2100
                    });

                }
            
                
            }
        });    
    });
});


</script>
@endpush