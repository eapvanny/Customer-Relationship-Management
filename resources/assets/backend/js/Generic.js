import Academic from "./Academic";

export default class Generic {
    /**
     * academic related codes
     */
    static initFilter() {
        //filter by org_id
        if(window.filter_org){
            $('select[name="org_id"]').on('change', function () {
                let org_id = $(this).val();
                let getUrl = window.location.href.split('?')[0];
                if(org_id){
                    getUrl +="?org_id="+org_id;

                }
                window.location = getUrl;

            });
        }
        $('select[name="class_id"]').on('change', function () {
            if($('select[name="subject_id"]').length){
                $('select[name="subject_id"]').empty().select2({placeholder: 'Pick a subject...'});
            }
            if($('select[name="fourth_subject"]').length){
                $('select[name="fourth_subject"]').empty().select2({placeholder: 'Pick a subject...'});
            }
        });
        $('select[name="section_id"]').on('change', function () {
            //hide button
            let sectionId = $(this).val();
            let classId =  $('select[name="class_id"]').val();

            //validate input
            if(!classId || !sectionId){
                return false;
            }

            Generic.loaderStart();

            if($('select[name="subject_id"]').length){
                getSubject(classId, window.subjectType , sectionId,function (res={}) {
                    // console.log(res);
                    if (Object.keys(res).length){
                        $('select[name="subject_id"]').empty().prepend('<option selected=""></option>').select2({placeholder: 'Pick a subject...', data: res});
                    }
                    else{
                        // clear subject list dropdown
                        $('select[name="subject_id"]').empty().select2({placeholder: 'Pick a subject...'});
                        toastr.warning('This class have no subject!');
                    }
                    Generic.loaderStop();
                });
            }
            if($('select[name="fourth_subject"]').length){
                getSubject(classId, window.subjectType , sectionId,function (res={}) {
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
            }
            // Academic.checkAttendanceExists(function (data) {
            //     if(data>0){
            //         toastr.error('Attendance already exists!');
            //     }
            //
            //     Generic.loaderStop();
            //
            // });


        });
        function  getSubject(class_id, $type=0,$section=0, cb) {
            let getUrl = window.subject_list_url + "?class=" + class_id+"&type="+$type+"&section="+$section;
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
    }
    static initCommonPageJS() {
        $("#entryForm").on("submit", function(){
            $(this).find("button[type='submit']").attr("disabled", true);
        });
        $("#entryForm").validate({
            errorElement: "em",
            errorPlacement: function (error, element) {
                // Add the `help-block` class to the error element
                error.addClass("help-block");
                error.insertAfter(element);

            },
            highlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-error").removeClass("has-success");
                $("#entryForm").find("button[type='submit']").removeAttr("disabled");
            },
            unhighlight: function (element, errorClass, validClass) {
                $(element).parents(".form-group").addClass("has-success").removeClass("has-error");
            }
        });

        $(".date_picker").datetimepicker({
            format: "DD/MM/YYYY",
            viewMode: 'days',
            ignoreReadonly: true,
        });
        $(".date_picker_with_clear").datetimepicker({
            format: "DD/MM/YYYY",
            viewMode: 'days',
            showClear: true,
            ignoreReadonly: true
        });


        $(".date_picker2").datetimepicker({
            format: "DD/MM/YYYY",
            viewMode: 'years',
            ignoreReadonly: true
        });

        $(".time_picker").datetimepicker({
            format: 'LT',
            showClear: true,
            ignoreReadonly: true
        });

        $(".date_time_picker").datetimepicker({
            format: "DD/MM/YYYY LT",
            viewMode: 'days',
            ignoreReadonly: true
        });

        $('.date_picker_with_disable_days').datetimepicker({
            format: "DD/MM/YYYY",
            viewMode: 'days',
            ignoreReadonly: true,
            daysOfWeekDisabled: window.disableWeekDays,
            useCurrent: false
        });

        $('.only_year_picker').datetimepicker({
            format: "YYYY",
            viewMode: 'years',
            ignoreReadonly: true,
            useCurrent: false
        });
        $('html .datatable-server').on('change', 'input.statusChange', function (e) {
            let that = $(this);
            if (stopchange === false) {
                let isActive = $(this).prop('checked') ? 1 : 0;
                let pk = $(this).attr('data-pk');
                let newpostUrl = postUrl.replace(/\.?0+$/, pk);
                axios.post(newpostUrl, { 'status': isActive })
                    .then((response) => {
                        if (response.data.success) {
                            toastr.success(response.data.message);
                        }
                        else {
                            let status = response.data.message;
                            if (stopchange === false) {
                                stopchange = true;
                                that.bootstrapToggle('toggle');
                                stopchange = false;
                            }
                            toastr.error(status);
                        }

                    }).catch((error) => {
                    // console.log(error.response);
                    let status = error.response.statusText;
                    if (stopchange === false) {
                        stopchange = true;
                        that.bootstrapToggle('toggle');
                        stopchange = false;
                    }
                    toastr.error(status);

                });
            }
        });
        // var buttonCommon = {
        //     exportOptions: {
        //         columns: ':not(.notexport)',
        //         format: {
        //             body: function (data, row, column, node) {
        //                 if(typeof(window.changeExportColumnIndex) !== 'undefined') {
        //                     var onValue = typeof window.changeExportColumnValue !== 'undefined' ? window.changeExportColumnValue[0] : 'Active';
        //                     var offValue = typeof window.changeExportColumnValue !== 'undefined' ? window.changeExportColumnValue[1] : 'Inactive';
        //                     if (column === window.changeExportColumnIndex) {
        //                         data = /checked/.test(data) ? onValue : offValue;
        //                     }
        //                 }
        //                 return data;
        //             }
        //         }
        //     }
        // };

        //table with out search
        var table = $('#listDataTable').DataTable({
            pageLength: 25,
            lengthChange: false,
            responsive: true,
            // buttons: [
            //     $.extend(true, {}, buttonCommon, {
            //         extend: 'copy',
            //         text: '<i class="fa fa-files-o"></i>',
            //         titleAttr: 'copy',
            //     }),
            //     $.extend(true, {}, buttonCommon, {
            //         extend: 'csv',
            //         text: '<i class="fa fa-file-text-o"></i>',
            //         titleAttr: 'csv',
            //     }),
            //     $.extend(true, {}, buttonCommon, {
            //         extend: 'excel',
            //         text: '<i class="fa fa-file-excel-o"></i>',
            //         titleAttr: 'Excel',
            //     }),
            //     $.extend(true, {}, buttonCommon, {
            //         extend: 'pdf',
            //         text: '<i class="fa fa-file-pdf-o"></i>',
            //         titleAttr: 'pdf',
            //         customize: function (doc) {
            //             doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
            //             doc.content[1].alignment = "center";
            //         }
            //     }),
            //     $.extend(true, {}, buttonCommon, {
            //         extend: 'print',
            //         text: '<i class="fa fa-print"></i>',
            //         titleAttr: 'print',
            //     })
            // ]
        });
        // table.buttons().container().appendTo($('.col-sm-6:eq(0)', table.table().container()));

        //table custom
        var table33 = $('#listDataTableOnlyPrint').DataTable({
            lengthChange: false,
            responsive: true,
            paging: false,
            filter: false,
            // buttons: [
            //     $.extend(true, {}, buttonCommon, {
            //         extend: 'print',
            //         text: '<i class="fa fa-print"></i>',
            //         titleAttr: 'print',
            //     })
            // ]
        });
        // table33.buttons().container().appendTo($('.col-sm-6:eq(0)', table33.table().container()));


        //style table with search
        // Setup - add a text input to each footer cell
        // $('#listDataTableWithSearch thead tr').clone(true).appendTo( '#listDataTableWithSearch thead' );
        // $('#listDataTableWithSearch thead tr:eq(1) th').each( function (i) {
        //
        //     if(window.excludeFilterComlumns.indexOf(i) > -1) {
        //         $(this).html( '' );
        //         return;
        //     }
        //
        //     $(this).html( '<input type="text" placeholder="Search" />' );
        //     $( 'input', this ).on( 'keyup change', function () {
        //         if ( table2.column(i).search() !== this.value ) {
        //             table2
        //                 .column(i)
        //                 .search( this.value )
        //                 .draw();
        //         }
        //     } );
        // } );

        var table2 = $('#listDataTableWithSearch').DataTable({
            pageLength: 25,
            lengthChange: false,
            orderCellsTop: true,
            responsive: true,
            // buttons: [
            //     $.extend(true, {}, buttonCommon, {
            //         extend: 'copy',
            //         text: '<i class="fa fa-files-o"></i>',
            //         titleAttr: 'copy',
            //     }),
            //     $.extend(true, {}, buttonCommon, {
            //         extend: 'csv',
            //         text: '<i class="fa fa-file-text-o"></i>',
            //         titleAttr: 'csv',
            //     }),
            //     $.extend(true, {}, buttonCommon, {
            //         extend: 'excel',
            //         text: '<i class="fa fa-file-excel-o"></i>',
            //         titleAttr: 'Excel',
            //     }),
            //     $.extend(true, {}, buttonCommon, {
            //         extend: 'pdf',
            //         text: '<i class="fa fa-file-pdf-o"></i>',
            //         titleAttr: 'pdf',
            //         customize: function (doc) {
            //             doc.content[1].table.widths = Array(doc.content[1].table.body[0].length + 1).join('*').split('');
            //             doc.content[1].alignment = "center";
            //         }
            //     }),
            //     $.extend(true, {}, buttonCommon, {
            //         extend: 'print',
            //         text: '<i class="fa fa-print"></i>',
            //         titleAttr: 'print',
            //     })
            // ]
        });
        // table2.buttons().container().appendTo($('.col-sm-6:eq(0)', table2.table().container()));


        let stopchange = false;
        $('html #listDataTableWithSearch, html #listDataTable').on('change', 'input.statusChange', function (e) {
            let that = $(this);
            if (stopchange === false) {
                let isActive = $(this).prop('checked') ? 1 : 0;
                let pk = $(this).attr('data-pk');
                let newpostUrl = postUrl.replace(/\.?0+$/, pk);
                axios.post(newpostUrl, { 'status': isActive })
                    .then((response) => {
                        if (response.data.success) {
                            toastr.success(response.data.message);
                        }
                        else {
                            let status = response.data.message;
                            if (stopchange === false) {
                                stopchange = true;
                                that.bootstrapToggle('toggle');
                                stopchange = false;
                            }
                            toastr.error(status);
                        }
                    }).catch((error) => {
                        // console.log(error.response);
                        let status = error.response.statusText;
                        if (stopchange === false) {
                            stopchange = true;
                            that.bootstrapToggle('toggle');
                            stopchange = false;
                        }
                        toastr.error(status);

                    });
            }
        });

        $(".year_picker").datetimepicker({
            format: "YYYY",
            viewMode: 'years',
            ignoreReadonly: true
        });

        $('input').not('.dont-style').iCheck({
            checkboxClass: 'icheckbox_square-blue',
            radioClass: 'iradio_square-blue',
            increaseArea: '20%' /* optional */
        });


    }

    static initDeleteDialog() {
        $('html #listDataTableWithSearch, html #listDataTable, html #listDataTableOnlyPrint').on('submit', 'form.myAction', function (e) {
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
    }

    static initMarksheetPublic() {
        Generic.initCommonPageJS();
        // $('#class_change').val('').trigger('change');
        $('#class_change').on('change', function () {
            let class_id = $(this).val();
            if(class_id) {
                //get sections
                Academic.getExam(class_id);
            }
            else{
                $('select[name="exam_id"]').empty().select2({placeholder: 'Pick a exam...'});
            }

        });
    }
    static loaderStart(){
        // console.log('loader started...');
        $('.ajax-loader').css('display','block');
    }
    static loaderStop(){
        // console.log('loader stoped...');
        $('.ajax-loader').css('display','none');
    }
}
