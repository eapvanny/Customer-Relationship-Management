import Generic from "./Generic";

export default class Academic {
    /**
     * academic related codes
     */
    static iclassInit() {
        Generic.initCommonPageJS();
        Generic.initDeleteDialog();
        $('#academic_year_id').on('change', function () {

            let getUrl = window.location.href.split('?')[0];

            if($('select[name="academic_year_id"]').length){
                let academic_year_id = $('select[name="academic_year_id"]').val();
                getUrl +="?academic_year_id="+academic_year_id;
            }

            window.location = getUrl;

        });
    }
    static sectionInit() {
        Generic.initCommonPageJS();
        Generic.initDeleteDialog();
    }
    static subjectInit() {
        Generic.initCommonPageJS();
        Generic.initDeleteDialog();

        // $('select[name="section_id"]').on('change', function () {
        //     $('#subject_list_filter').trigger('dp.change');
        // });

        $('#class_id_filter').on('change', function () {
            let class_id = $(this).val();
            let getUrl = window.location.href.split('?')[0];
            if(class_id){
                getUrl +="?class="+class_id;
                if($('select[name="org_id"]').length){
                    let org_id = $('select[name="org_id"]').val();
                    getUrl +="&org_id="+org_id;
                }
            }
            window.location = getUrl;

        });


        // $('#subject_list_filter').on('dp.change', function (event) {
        //     let atDate = $(this).val();
        //     let classId = $('select[name="class_id"]').val();

        //     //check year, class, section and date is fill up then procced
        //     if(!atDate || !classId){
        //         toastr.warning('Fill up class!');
        //         return false;
        //     }

        //     let queryString = "?class="+classId+"&subject="+subjectId+"&attendance_date="+atDate;

        //     let getUrl = window.location.href.split('?')[0]+queryString;
        //     window.location = getUrl;

        // });
    }
    static studentInit() {
        Generic.initCommonPageJS();
        Generic.initDeleteDialog();
        $('select[name="nationality"]').on('change', function () {
            // console.log('fire me');
            var value = $(this).val();
            if(value == 'Other'){
                $('input[name="nationality_other"]').prop('readonly', false);
            }
            else{
                $('input[name="nationality_other"]').val('');
                $('input[name="nationality_other"]').prop('readonly', true);
            }
        });

        // $('select[name="class_id"]').on('change', function () {
        //     let class_id = $(this).val();
        //     Academic.getSection(class_id);
        //     // Academic.getExam(class_id);

        // });
        $('select[name="class_id_edit"]').on('change', function () {
            let class_id = $(this).val();
            Academic.getSection(class_id,window.subject_edit);
        });

        $('#student_add_edit_class_change').on('change', function () {
            //get subject of requested class
            Generic.loaderStart();
            let class_id = $(this).val();
            let type = (institute_category == "college") ? 0 : 2;
            Academic.getSubject(class_id, type, function (res={}) {
                // console.log(res);
                if (Object.keys(res).length){

                    $('select[name="fourth_subject"]').empty().prepend('<option selected=""></option>').select2({placeholder: 'Pick a subject...', data: res});

                }
                else{
                    // clear subject list dropdown
                    $('select[name="fourth_subject"]').empty().select2({placeholder: 'Pick a subject...'});
                    toastr.warning('This class have no subject!');
                }
                Generic.loaderStop();
            });
            if(institute_category == "college") {
                Generic.loaderStart();
                Academic.getSubject(class_id, 1, function (res={}) {
                    // console.log(res);
                    if (Object.keys(res).length){

                        $('select[name="alt_fourth_subject"]').empty().prepend('<option selected=""></option>').select2({placeholder: 'Pick a subject...', data: res});

                    }
                    else{
                        // clear subject list dropdown
                        $('select[name="alt_fourth_subject"]').empty().select2({placeholder: 'Pick a subject...'});
                        toastr.warning('This class have no subject!');
                    }
                    Generic.loaderStop();
                });

            }
        });

        $('#student_list_filter').on('change', function () {
            let class_id = $('select[name="class_id"]').val();
            let section_id = $(this).val();
            let urlLastPart = '';
            if(institute_category == 'college'){
                let ac_year = $('select[name="academic_year"]').val();
                if(!ac_year){
                    toastr.error('Select academic year!');
                    return false;
                }

                urlLastPart ="&academic_year="+ac_year;
            }
            if(class_id && section_id){
                let getUrl = null;
                if($('.list-org').length == 1){
                    //  let org_id = $('select[name="org_id"]').val();
                     getUrl = window.location.href.split('?')[0]+"?class="+class_id+"&section="+section_id+urlLastPart+"&org_id="+$('select[name="org_id"]').val();
                }else{
                     getUrl = window.location.href.split('?')[0]+"?class="+class_id+"&section="+section_id+urlLastPart;
                }
                console.log($('.list-org').length);
                window.location = getUrl;

            }

        });
        $('select[name="academic_year"]').on('change', function () {
            $('#student_list_filter').trigger('change');
        });


    }
    static  getSection(class_id,$edit = false) {
        let getUrl = window.section_list_url + "?class=" + class_id;
        if (class_id) {
            Generic.loaderStart();
            axios.get(getUrl)
                .then((response) => {
                    if(!$edit)
                    {
                        if (Object.keys(response.data).length) {
                            $('select[name="section_id"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a section...', data: response.data});
                        }
                        else {
                            $('select[name="section_id"]').empty().select2({placeholder: 'Pick a section...'});
                            toastr.error('This class have no section!');
                        }
                    }else{
                        if (Object.keys(response.data).length) {
                            $('select[name="section_id_edit"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a section...', data: response.data});
                            $('select[name="section_id_edit"]').val(window.id_section).trigger('change');
                        }
                        else {
                            $('select[name="section_id_edit"]').empty().select2({placeholder: 'Pick a section...'});
                            toastr.error('This class have no section!');
                        }
                    }
                    Generic.loaderStop();
                }).catch((error) => {
                let status = error.response.statusText;
                toastr.error(status);
                Generic.loaderStop();

            });
        }
        else {
            // clear section list dropdown
            $('select[name="section_id"]').empty().select2({placeholder: 'Pick a section...'});
        }
    }
    static  getSubject(class_id, $type=0, cb) {
        let getUrl = window.subject_list_url + "?class=" + class_id+"&type="+$type;
        // if($('select[name="subject_id"]').length){
        //     let section_id = $('select[name="section_id"]').val();
        //     getUrl+="&section="+section_id;
        // }

        if (class_id) {
            axios.get(getUrl)
                .then((response) => {
                    cb(response.data);

                }).catch((error) => {
                let status = error.response.statusText;
                toastr.error(status);
                cb();

            });
        }
        else {
            cb();
        }
    }
    static getStudentByAcYearAndClassAndSection(acYear=0, classId, sectionId, cb=function(){}) {
        let getUrl = window.getStudentAjaxUrl +"?academic_year="+acYear+"&class="+classId+"&section="+sectionId;
        axios.get(getUrl)
            .then((response) => {
                // console.log(response);
                cb(response.data);
            }).catch((error) => {
            let status = error.response.statusText;
            toastr.error(status);
            cb([]);
        });
    }

    /**
     * Student Attendance
     */
    static attendanceInit() {
        Generic.initCommonPageJS();
        Generic.initDeleteDialog();

        // $('select[name="class_id"]').on('change', function () {
        //     let class_id = $(this).val();
        //     // Academic.getSection(class_id);
        //     // if($('select[name="subject_id"]').length){
        //         Academic.getSubject(class_id, 0, function (res={}) {
        //             // console.log(res);
        //             if (Object.keys(res).length){
        //                 $('select[name="subject_id"]').empty().prepend('<option selected=""></option>').select2({placeholder: 'Pick a subject...', data: res});
        //             }
        //             else{
        //                 // clear subject list dropdown
        //                 $('select[name="subject_id"]').empty().select2({placeholder: 'Pick a subject...'});
        //                 toastr.warning('This class have no subject!');
        //             }
        //             Generic.loaderStop();
        //         });
        //     // }

        // });
        // $('select[name="section_id"]').on('change', function () {
        //     $('#attendance_list_filter').trigger('dp.change');
        // });
        $('#attendance_list_filter').on('dp.change', function (event) {
            let atDate = $(this).val();
            let classId = $('select[name="class_id"]').val();
            let sectionId = $('select[name="section_id"]').val();
            let acYearId = $('select[name="academic_year"]').val();

            //check year, class, section and date is fill up then procced

            if($('select[name="subject_id"]').length){
                let subjectId = $('select[name="subject_id"]').val();
                if(!atDate || !classId || !sectionId || !subjectId){
                    toastr.warning('Fill up class, section, subject first!');
                    return false;
                }
            }else {
                if(!atDate || !classId || !sectionId){
                    toastr.warning('Fill up class, section and date first!');
                    return false;
                }
            }
            if(institute_category == "college" && !acYearId){
                toastr.warning('Select academic year first!');
                return false;
            }
            let queryString = "?class="+classId+"&section="+sectionId+"&attendance_date="+atDate;
            if($('select[name="subject_id"]').length){
                let subjectId = $('select[name="subject_id"]').val();
                queryString = "?class="+classId+"&subject="+subjectId+"&section="+sectionId+"&attendance_date="+atDate;
            }

            if(institute_category == 'college'){
                queryString +="&academic_year="+acYearId;
            }

            let getUrl = window.location.href.split('?')[0]+queryString;
            window.location = getUrl;

        });

        $('.attendanceExistsChecker').on('dp.change', function (event) {
            Academic.checkAttendanceExists(function (data) {
                    if(data>0){
                        toastr.error('Attendance already exists!');
                    }
                    else{
                        $('#section_id_filter').trigger('change');
                    }
            });

        });
        $('#toggleCheckboxes').on('ifChecked ifUnchecked', function(event) {
            if (event.type == 'ifChecked') {
                $('input:checkbox:not(.notMe)').iCheck('check');
            } else {
                $('input:checkbox:not(.notMe)').iCheck('uncheck');
            }
        });

        $('#section_id_filter').on('change', function () {
            //hide button
            let sectionId = $(this).val();
            let classId =  $('select[name="class_id"]').val();
            let acYearId =  $('select[name="academic_year"]').val();
            //validate input
            if(!classId || !sectionId){
                return false;
            }
            //check year then procced
            if(institute_category == "college"){
                if(!acYearId) {
                    toastr.warning('Select academic year first!');
                    return false;
                }
            }
            else {
                acYearId = 0;
            }

            Generic.loaderStart();
            Academic.checkAttendanceExists(function (data) {
                if(data>0){
                    toastr.error('Attendance already exists!');
                }

                Generic.loaderStop();

            });


        });

        $('input.inTime').on('dp.change', function (event) {
            let attendance_date = window.moment($('input[name="attendance_date"]').val(),'DD-MM-YYYY');
            let inTime =  window.moment(event.date,'DD-MM-YYYY');
            if(inTime.isBefore(attendance_date)){
                toastr.error('In time can\'t be less than attendance date!');
                $(this).data("DateTimePicker").date(attendance_date.format('DD/MM/YYYY, hh:mm A'));
                return false;
            }

            let timeDiff = window.moment.duration(inTime.diff(attendance_date));
            if(timeDiff.days()>0){
                toastr.error('In time can\'t be greater than attendance date!');
                $(this).data("DateTimePicker").date(attendance_date.format('DD/MM/YYYY, hh:mm A'));
                return false;
            }

        });

        $('input.outTime').on('dp.change', function (event) {
            let inTime = window.moment($(this).parents('tr').find('input.inTime').val(),'DD-MM-YYYY, hh:mm A');
            let outTime =  window.moment(event.date,'DD-MM-YYYY, hh:mm A');

            if(outTime.isBefore(inTime)){
                toastr.error('Out time can\'t be less than in time!');
                $(this).data("DateTimePicker").date(inTime);
                return false;
            }
            let timeDiff = window.moment.duration(outTime.diff(inTime));
            if(timeDiff.days()>0){
                toastr.error('Can\'t stay more than 24 hrs!');
                $(this).data("DateTimePicker").date(inTime);
                return false;
            }
            let workingHours = [timeDiff.hours(), timeDiff.minutes()].join(':');
            $(this).parents('tr').find('span.stayingHour').text(workingHours);

        });
    }

    static checkAttendanceExists(cb={}) {
        let atDate = $('input[name="attendance_date"]').val();
        let classId = $('select[name="class_id"]').val();
        let sectionId = $('select[name="section_id"]').val();
        let acYearId = $('select[name="academic_year"]').val();
        let queryString = "?class="+classId+"&section="+sectionId+"&attendance_date="+atDate;

        if(institute_category == 'college'){
            queryString +="&academic_year="+acYearId;
        }

        let getUrl = window.attendanceUrl + queryString;
        axios.get(getUrl)
            .then((response) => {
              cb(response.data);
            }).catch((error) => {
            let status = error.response.statusText;
            toastr.error(status);
            cb(0);
            Generic.loaderStop();
        });

    }

    static attendanceFileUploadStatus() {
        // progress status js code here
        $.ajax({
            'url': window.fileUploadStatusURL,
        }).done(function(r) {
            if(r.success) {
                $('#statusMessage').html(r.msg);
                setTimeout(function () {
                    window.location.reload();
                }, 5000);
            } else {
                $('#statusMessage').html(r.msg);
                if(r.status == 0){
                    setTimeout(function () {
                        Academic.attendanceFileUploadStatus();
                    }, 500);
                }
                else if(r.status == -1){
                    $('.progressDiv').removeClass('alert-info');
                    $('.progressDiv').addClass('alert-danger');
                    $('#spinnerspan').remove();
                }

            }
        }).fail(function() {
                $('#statusMessage').html("An error has occurred...Contact administrator" );
            });

    }

    static studentProfileInit() {
        $('.btnPrintInformation').click(function () {
            $('ul.nav-tabs li:not(.active)').addClass('no-print');
            $('ul.nav-tabs li.active').removeClass('no-print');
            window.print();
        });

        $('#tabAttendance').click(function () {
            let id = $(this).attr('data-pk');
            let geturl = window.attendanceUrl+'?student_id='+id;
            Generic.loaderStart();
            $('#attendanceTable tbody').empty();
            axios.get(geturl)
                .then((response) => {
                   // console.log(response);
                   if(response.data.length){
                       response.data.forEach(function (item) {
                           let color = item.present == "Present" ? 'bg-green' : 'bg-red';
                          let trrow = ' <tr>\n' +
                              '  <td class="text-center">'+item.attendance_date+'</td>\n' +
                              '  <td class="text-center"> <span class="badge '+ color+'">'+item.present+'</span></td>\n' +
                              '</tr>';

                           $('#attendanceTable tbody').append(trrow);
                       });
                   }

                    Generic.loaderStop();
                }).catch((error) => {
                let status = error.response.statusText;
                toastr.error(status);
                Generic.loaderStop();
            });
        });
    }

    static examRuleInit() {
        Generic.initCommonPageJS();
        Generic.initDeleteDialog();
        $('#exam_rules_add_class_change').on('change', function () {
            //get subject of requested class
            // Generic.loaderStart();
            // let class_id = $(this).val();
            // Academic.getSubject(class_id, 0, function (res={}) {
            //     // console.log(res);
            //     if (Object.keys(res).length){
            //         $('select[name="subject_id"]').empty().prepend('<option selected=""></option>').select2({placeholder: 'Pick a subject...', data: res});
            //         $('select[name="combine_subject_id"]').empty().prepend('<option selected=""></option>').select2({placeholder: 'Pick a subject...', data: res});
            //     }
            //     else{
            //         // clear subject list dropdown
            //         $('select[name="subject_id"]').empty().select2({placeholder: 'Pick a subject...'});
            //         $('select[name="combine_subject_id"]').empty().select2({placeholder: 'Pick a subject...'});
            //         toastr.warning('This class have no subject!');
            //     }
            //     Generic.loaderStop();
            // });

            //now fetch exams for this class
            // Academic.getExam(class_id);

            //get section of requested class
            let class_id = $(this).val();
            Academic.getSection(class_id);

        });

        $('#exam_rules_add_section_change').on('change', function () {
            //get combine subject of requested section
            Generic.loaderStart();
            let class_id = $('#exam_rules_add_class_change').val();
            Academic.getSubject(class_id, 0, function (res={}) {
                // console.log(res);
                if (Object.keys(res).length){
                    $('select[name="combine_subject_id"]').empty().prepend('<option selected=""></option>').select2({placeholder: 'Pick a subject...', data: res});
                }
                else{
                    // clear subject list dropdown
                    $('select[name="combine_subject_id"]').empty().select2({placeholder: 'Pick a subject...'});
                    // toastr.warning('This section have no subject!');
                }
                Generic.loaderStop();
            });

        });

        $('#exam_rules_add_subject_change').on('change', function () {

            //now fetch exams for this subject
            Generic.loaderStart();
            let subject_id = $(this).val();
            let class_id = $('#exam_rules_add_class_change').val();
            let section_id = $('#exam_rules_add_section_change').val();
            Academic.SubjectGetExam(subject_id,section_id,class_id);
        });

        $('select[name="exam_id"]').on('change', function () {
            $('#distributionTypeTable tbody').empty();
            if($(this).val()) {
                let getUrl = window.exam_details_url + "?exam_id=" + $(this).val();
                Generic.loaderStart();
                axios.get(getUrl)
                    .then((response) => {
                        // console.log(response.data);
                        response.data.forEach(function (item) {
                            let trrow = '<tr>\n' +
                                ' <td>\n' +
                                ' <span>' + item.text + '</span>\n' +
                                ' <input type="hidden" name="type[]" value="' + item.id + '">\n' +
                                ' </td>\n' +
                                ' <td>\n' +
                                '<input type="number" class="form-control" name="total_marks[]" value="" required min="0">\n' +
                                '</td>\n' +
                                ' <td>\n' +
                                '<input type="number" class="form-control" name="pass_marks[]" value="0" required min="0">\n' +
                                '</td>\n' +
                                '</tr>';

                            $('#distributionTypeTable tbody').append(trrow);
                        });
                        Generic.loaderStop();
                    }).catch((error) => {
                    let status = error.response.statusText;
                    toastr.error(status);
                    Generic.loaderStop();
                });
            }
        });

        function fetchGradeInfo() {
            $('input[name="total_exam_marks"]').val('');
            $('input[name="over_all_pass"]').val('');
            let gradeId =  $('select[name="grade_id"]').val();
            if(gradeId) {
                let getUrl = window.grade_details_url + "?grade_id=" + gradeId;
                Generic.loaderStart();
                axios.get(getUrl)
                    .then((response) => {
                        // console.log(response.data);
                        $('input[name="total_exam_marks"]').val(response.data.totalMarks);
                        $('input[name="over_all_pass"]').val(response.data.passingMarks);
                        Generic.loaderStop();
                    }).catch((error) => {
                    let status = error.response.statusText;
                    toastr.error(status);
                    Generic.loaderStop();
                });
            }
        }

        $('select[name="grade_id"]').on('change', function () {
            fetchGradeInfo();
        });
        $('select[name="combine_subject_id"]').on('change', function () {
            let subjectId =  $('select[name="subject_id"]').val();
            let combineSujectId = $(this).val();

            if(subjectId==combineSujectId){
                toastr.error("Same subject can not be a combine subject!");
                $('select[name="combine_subject_id"]').val('').trigger('change');
            }
        });
        $('select[name="passing_rule"]').on('change', function () {
            let passingRule = $(this).val();
            if(passingRule == 2) {
                // individual pass
                $('input[name="over_all_pass"]').val(0);
                $('input[name="pass_marks[]"]').prop('readonly', false);
                $('input[name="pass_marks[]"]').val(0);
            }
            else{
                if($('input[name="over_all_pass"]').val() == 0){
                    fetchGradeInfo();
                }
                $('.overAllPassDiv').show();
            }

            if(passingRule == 1){
                $('input[name="pass_marks[]"]').prop('readonly', true);
            }
            else{
                $('input[name="pass_marks[]"]').prop('readonly', false);
                $('input[name="pass_marks[]"]').val(0);
            }
        });

        //
        $('html').on('change keyup paste','input[name="total_marks[]"]', function(){
            let grandTotalMakrs = parseInt($('input[name="total_exam_marks"]').val());
            let distributionTotalMarks = 0;
            $('input[name="total_marks[]"]').each(function (index,element) {
                if($(element).val().length) {
                    distributionTotalMarks += parseInt($(element).val());
                }
            });
            // console.log(grandTotalMakrs, distributionTotalMarks);
            if(distributionTotalMarks> grandTotalMakrs){
                toastr.error("Marks distribution is wrong! Not match with total marks.");
                $('input[name="total_marks[]"]').val(0);
            }
        });

        //list page js
        // $('select[name="class"]').on('change', function () {
        //     let classId = $(this).val();
        //     if(classId){
        //         //now fetch exams for this class
        //         Generic.loaderStart();
        //         let getUrl = window.exam_list_url + "?class_id=" + classId;
        //         axios.get(getUrl)
        //             .then((response) => {
        //                 if (Object.keys(response.data).length) {
        //                     $('select[name="exam"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a exam...', data: response.data});
        //                 }
        //                 else {
        //                     $('select[name="exam"]').empty().select2({placeholder: 'Pick a exam...'});
        //                     toastr.error('This class have no exam!');
        //                 }
        //                 Generic.loaderStop();
        //             }).catch((error) => {
        //             let status = error.response.statusText;
        //             toastr.error(status);
        //             Generic.loaderStop();

        //         });
        //     }
        //     else{
        //         $('select[name="exam"]').empty().select2({placeholder: 'Pick a exam...'});
        //     }
        // });
        // $('#exam_rule_list_filter').on('change', function () {
        //     let classId =  $('select[name="class_id"]').val();
        //     let sectionId =  $('select[name="section_id"]').val();
        //     let subjectId =  $('select[name="subject_id"]').val();
        //     let examId =  $('select[name="exam_id"]').val();
        //     if(classId && examId && section_id && subject_id){
        //         let getUrl = window.location.href.split('?')[0]+"?class_id="+classId+"&exam_id="+examId+"&section_id="+sectionId+"&subject_id="+subjectId;
        //         if($('select[name="org_id"]').length){
        //             let org_id = $('select[name="org_id"]').val();
        //             getUrl +="&org_id="+org_id;
        //         }
        //         window.location = getUrl;
        //     }
        // });
    }

    static getExam(class_id) {
        let getUrl = window.exam_list_url + "?class_id=" + class_id;
        if (class_id) {
            Generic.loaderStart();
            axios.get(getUrl)
                .then((response) => {
                    if (Object.keys(response.data).length) {
                        $('select[name="exam_id"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a exam...', data: response.data});
                    }
                    else {
                        $('select[name="exam_id"]').empty().select2({placeholder: 'Pick a exam...'});
                        toastr.error('This class have no exam!');
                    }
                    Generic.loaderStop();
                }).catch((error) => {
                let status = error.response.statusText;
                toastr.error(status);
                Generic.loaderStop();

            });
        }
        else {
            // clear section list dropdown
            $('select[name="exam_id"]').empty().select2({placeholder: 'Pick a exam...'});
        }
    }

    static SubjectGetExam(subject_id,section_id,class_id) {
        let getUrl = window.exam_list_url + "?class_id=" + class_id + "&section_id=" + section_id + "&subject_id=" + subject_id;
        if (subject_id) {
            Generic.loaderStart();
            axios.get(getUrl)
                .then((response) => {
                    if (Object.keys(response.data).length) {
                        $('select[name="exam"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a exam...', data: response.data});
                        $('select[name="exam_id"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a exam...', data: response.data});
                    }
                    else {
                        $('select[name="exam"]').empty().select2({placeholder: 'Pick a exam...'});
                        $('select[name="exam_id"]').empty().select2({placeholder: 'Pick a exam...'});
                        toastr.error('This section, subject have no exam!');
                    }
                    Generic.loaderStop();
                }).catch((error) => {
                let status = error.response.statusText;
                toastr.error(status);
                Generic.loaderStop();

            });
        }
        else {
            // clear section list dropdown
            $('select[name="exam_id"]').empty().select2({placeholder: 'Pick a exam...'});
        }
    }
    static SubjectGetGroupExam(class_id,section_id) {
        let getUrl = window.groupexam_list_url + "?class_id=" + class_id;
        if (class_id) {
            Generic.loaderStart();
            axios.get(getUrl)
                .then((response) => {
                    if (Object.keys(response.data).length) {
                        $('select[name="group_exam_id"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a group exam...', data: response.data});
                    }
                    else {
                        $('select[name="group_exam_id"]').empty().select2({placeholder: 'Pick a group exam...'});
                        toastr.error('This class section have no group exam!');
                    }
                    Generic.loaderStop();
                }).catch((error) => {
                let status = error.response.statusText;
                toastr.error(status);
                Generic.loaderStop();

            });
        }
        else {
            // clear section list dropdown
            $('select[name="group_exam_id"]').empty().select2({placeholder: 'Pick a group exam...'});
        }
    }

    static ClassGetGroupExam(class_id) {
        let getUrl = window.groupexam_list_url + "?class_id=" + class_id;
        if (class_id) {
            Generic.loaderStart();
            axios.get(getUrl)
                .then((response) => {
                    if (Object.keys(response.data).length) {
                        $('select[name="groupexam_id"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a group exam...', data: response.data});
                    }
                    else {
                        $('select[name="groupexam_id"]').empty().select2({placeholder: 'Pick a group exam...'});
                        toastr.error('This section, have no group exam!');
                    }
                    Generic.loaderStop();
                }).catch((error) => {
                let status = error.response.statusText;
                toastr.error(status);
                Generic.loaderStop();

            });
        }
        else {
            // clear section list dropdown
            $('select[name="groupexam_id"]').empty().select2({placeholder: 'Pick a group exam...'});
        }
    }

    static marksInit() {
        Generic.initCommonPageJS();
        $("#markForm").validate({
            errorElement: "em",
            errorPlacement: function (error, element) {
                // Add the `help-block` class to the error element
                error.addClass("help-block");
                error.insertAfter(element);

            },
            highlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-success").removeClass("has-error");
            }
        });
        $('#class_change').on('change', function () {
            let class_id = $(this).val();
            if(class_id) {
                //get sections
                Academic.getSection(class_id);
                //get subject of requested class
                // Generic.loaderStart();
                // Academic.getSubject(class_id, 0, function (res = {}) {
                //     // console.log(res);
                //     if (Object.keys(res).length) {
                //         $('select[name="subject_id"]').empty().prepend('<option selected=""></option>').select2({
                //             allowClear: true,
                //             placeholder: 'Pick a subject...',
                //             data: res
                //         });
                //     }
                //     else {
                //         // clear subject list dropdown
                //         $('select[name="subject_id"]').empty().select2({placeholder: 'Pick a subject...'});
                //         toastr.warning('This class have no subject!');
                //     }
                //     Generic.loaderStop();
                // });

                // //get sections
                // Academic.getExam(class_id);
            }
            else{
                $('select[name="section_id"]').empty().select2({placeholder: 'Pick a section...'});
                $('select[name="subject_id"]').empty().select2({placeholder: 'Pick a subject...'});
                $('select[name="exam_id"]').empty().select2({placeholder: 'Pick a exam...'});
            }

        });

        $('#mark_add_section_change').on('change', function () {
            //get combine subject of requested section
            let class_id = $('#class_change').val();
                // get subject of requested class
                Generic.loaderStart();
                Academic.getSubject(class_id, 0, function (res = {}) {
                    console.log(res);
                    if (Object.keys(res).length) {
                        $('select[name="subject_id"]').empty().prepend('<option selected=""></option>').select2({
                            allowClear: true,
                            placeholder: 'Pick a subject...',
                            data: res
                        });
                    }
                    else {
                        // clear subject list dropdown
                        $('select[name="subject_id"]').empty().select2({placeholder: 'Pick a subject...'});
                        toastr.warning('This section have no subject!');
                    }
                    Generic.loaderStop();
                });

        });

        $('#mark_add_subject_change').on('change', function () {

            //now fetch exams for this subject
            Generic.loaderStart();
            let subject_id = $(this).val();
            let class_id = $('#class_change').val();
            let section_id = $('#mark_add_section_change').val();
            Academic.SubjectGetExam(subject_id,section_id,class_id);
        });

        $('input[type="number"]').on('change keyup paste', function () {
            let marksElements = $(this).closest('tr').find('input[type="number"]');
            let totalMarks = 0;
            marksElements.each(function (index, element) {
                let marks = parseFloat($(element).val());
                if(marks){
                    totalMarks += marks;
                }
            });
            $(this).closest('tr').find('input.totalMarks').val(totalMarks.toFixed(2));
        });

        var title = $('title').text() + $('select[name="class_id"] option[selected]').text();
        title += '-'+ $('select[name="section_id"] option[selected]').text();
        title += '-'+ $('select[name="subject_id"] option[selected]').text();
        title += '-'+ $('select[name="exam_id"] option[selected]').text();
        $('title').text(title);

    }

    static resultInit() {
        Generic.initCommonPageJS();
        $('#class_change').on('change', function () {
            let class_id = $(this).val();
            if(class_id) {
                if(!window.generatePage) {
                    //get sections
                    Academic.getSection(class_id);
                }
                //get sections
                // Academic.getExam(class_id);
                //get group exam
                Academic.ClassGetGroupExam(class_id);
            }
            else{
                $('select[name="section_id"]').empty().select2({placeholder: 'Pick a section...'});
                // $('select[name="exam_id"]').empty().select2({placeholder: 'Pick a exam...'});
            }

        });

        $('#result_add_section_change').on('change', function () {
            //get combine subject of requested section
            let class_id = $('#class_change').val();
                // get subject of requested class
                Generic.loaderStart();
                Academic.getSubject(class_id, 0, function (res = {}) {
                    console.log(res);
                    if (Object.keys(res).length) {
                        $('select[name="subject_id"]').empty().prepend('<option selected=""></option>').select2({
                            allowClear: true,
                            placeholder: 'Pick a subject...',
                            data: res
                        });
                    }
                    else {
                        // clear subject list dropdown
                        $('select[name="subject_id"]').empty().select2({placeholder: 'Pick a subject...'});
                        toastr.warning('This section have no subject!');
                    }
                    Generic.loaderStop();
                });

        });

        $('#result_add_subject_change').on('change', function () {

            //now fetch exams for this subject
            Generic.loaderStart();
            let subject_id = $(this).val();
            let class_id = $('#class_change').val();
            let section_id = $('#result_add_section_change').val();
            Academic.SubjectGetExam(subject_id,section_id,class_id);
        });

        var title = $('title').text() + $('select[name="class_id"] option[selected]').text();
        if($('select[name="section_id"]').val()) {
            title += '-' + $('select[name="section_id"] option[selected]').text();
        }
        title += '-'+ $('select[name="exam_id"] option[selected]').text();
        $('title').text(title);

        //marksheetview button click
        $('.viewMarksheetPubBtn').click(function (e) {
            e.preventDefault();
            postForm(this)

        });

        function postForm(btnElement) {
            let regiNo = $(btnElement).attr('data-regino');
            let pubMarksheetBtn = $(btnElement).hasClass( "viewMarksheetPubBtn" );
            // let classId = $('select[name="class_id"]').val();
            // let sectionId = $('select[name="section_id"]').val();
            // let subjectId = $('select[name="subject_id"]').val();
            // let groupExamId = $('select[name="groupexam_id"]').val();
            // let examId = $('select[name="exam_id"]').val();
            let classId = $(btnElement).attr('data-class_id');
            let sectionId = $(btnElement).attr('data-section_id');
            let subjectId = $(btnElement).attr('data-subject_id');
            let groupExamId = $(btnElement).attr('data-group_exam_id');
            let examId = $(btnElement).attr('data-exam_id');
            let csrf = document.head.querySelector('meta[name="csrf-token"]').content;
            let formHtml = '';
            if(examId){
                formHtml = '<form id="marksheedForm" action="'+window.marksheetpub_url+'" method="post" target="_blank" enctype="multipart/form-data">\n' +
                '    <input type="hidden" name="_token" value="'+csrf+'">\n' +
                '    <input type="hidden" name="class_id" value="'+classId+'">\n' +
                '    <input type="hidden" name="section_id" value="'+sectionId+'">\n' +
                '    <input type="hidden" name="subject_id" value="'+subjectId+'">\n' +
                '    <input type="hidden" name="exam_id" value="'+examId+'">\n' +
                '    <input type="hidden" name="regi_no" value="'+regiNo+'">\n';
            }
            if(groupExamId){
                formHtml = '<form id="marksheedForm" action="'+window.marksheetpub_url+'" method="post" target="_blank" enctype="multipart/form-data">\n' +
                '    <input type="hidden" name="_token" value="'+csrf+'">\n' +
                '    <input type="hidden" name="class_id" value="'+classId+'">\n' +
                // '    <input type="hidden" name="section_id" value="'+sectionId+'">\n' +
                '    <input type="hidden" name="groupexam_id" value="'+groupExamId+'">\n' +
                '    <input type="hidden" name="regi_no" value="'+regiNo+'">\n';
            }

            if(pubMarksheetBtn){
                formHtml += '    <input type="hidden" name="authorized_form" value="1">\n';
            }
            formHtml += '</form>';

            $('body').append(formHtml);
            $('#marksheedForm').submit();
            $('#marksheedForm').remove();
        }
    }
}
