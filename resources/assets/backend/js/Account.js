/**
 * Short description for file
 *
 * Author: H.R. Shadhin <dev@hrshadhin.me>
 *
 * LICENSE: AGP 3.0
 *
 * DATE: 3/6/19
 * TIME: 7:44 PM
 */
import Generic from "./Generic";
import Academic from "./Academic";

export default class Account {

    static feeInit() {
        Generic.initCommonPageJS();
        Generic.initDeleteDialog();
    }
    static budgetInit() {
        Generic.initCommonPageJS();
        Generic.initDeleteDialog();
        if($('select[name="academic_year"] option[selected]').text().length) {
            $('title').text($('title').text() + '-' + +'(' + $('select[name="academic_year"] option[selected]').text() + ')');
        }

        $('select[name="academic_year"]').on('change', function () {
            let ac_year = $('select[name="academic_year"]').val();
            if(!ac_year){
                return false;
            }

            let urlLastPart ="?academic_year="+ac_year;
            let getUrl = window.location.href.split('?')[0]+urlLastPart;
            window.location = getUrl;

        });

    }

    static invoiceInit() {
        Generic.initCommonPageJS();
        Generic.initDeleteDialog();

        //list page js
        $('select[name="class_id"]').on('change', function () {
            let class_id = $(this).val();
            Academic.getSection(class_id);
        });
        $('#section_id_filter').on('change', function () {
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
            Academic.getStudentByAcYearAndClassAndSection(acYearId, classId, sectionId, function (data) {
                let students = data;
                if (students.length) {
                    let studentOptions = [];
                    students.forEach(function(item) {
                        studentOptions.push({
                            id: item.id,
                            text: item.student.name +'['+item.roll_no+']'
                        });
                    });
                    $('select[name="student_id"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a student...', data: studentOptions});
                    //now show the submit button
                    $('button[type="submit"]').show();
                }
                else {
                    $('select[name="student_id"]').empty().select2({placeholder: 'Pick a student...'});
                    toastr.error('This section have no student!');
                }
                Generic.loaderStop();
            });

        });

        //add page js
        $('#fee_type_id').select2({
            placeholder: 'Pick a fee',
            data: window.fee_types,
            allowClear: true,
            templateSelection: function (data, container) {
                $(data.element).attr('data-amount', data.amount);
                return data.text;
            }
        });


        $('.nav-tabs').on('click','li#searchByClassTab',function () {
            setTimeout(function () {
                $('select#academic_year_id').select2();
                $('select#class_id').select2({placeholder: 'Pick a class...'});
            },200);

        });

        $('select#class_id').on('change', function () {
            let class_id = $(this).val();
            let getUrl = window.section_list_url + "?class=" + class_id;
            if (class_id) {
                Generic.loaderStart();
                axios.get(getUrl)
                    .then((response) => {
                        if (Object.keys(response.data).length) {
                            $('select#section_id').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a section...', data: response.data});
                        }
                        else {
                            $('select#section_id').empty().select2({placeholder: 'Pick a section...'});
                            toastr.error('This class have no section!');
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
                $('select#section_id').empty().select2({placeholder: 'Pick a section...'});
            }
        });

        $('input#regi_no, input#roll_no').on('blur',function () {
            $('select[name="student_id"]').empty().select2({placeholder: 'Pick a student...'});

            let whichInput = $(this).attr('id');
            let urlParams = "";
            if(whichInput == "regi_no"){
                let regiNo = $(this).val();
                if(!regiNo.length){
                    toastr.warning('write regi. no!');
                    return false;
                }
                urlParams = "?regi_no="+regiNo;
            }
            else{
                let classId =  $('select#class_id').val();
                let sectionId =  $('select#section_id').val();
                let acYearId =  $('select#academic_year_id').val();
                let rollNo = $(this).val();
                //validate input
                if(!classId || !sectionId || !rollNo.length){
                    toastr.warning('Fill up form fields!');
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

                urlParams = "?academic_year="+acYearId+"&class="+classId+"&section="+sectionId+"&roll_no="+rollNo;
            }

            let getUrl = window.getStudentAjaxUrl + urlParams;
            Generic.loaderStart();
            axios.get(getUrl)
                .then((response) => {
                    // console.log(response);
                    let students = response.data;
                    if (students.length) {
                        let student = students[0];
                        let studentOptions = [{
                            id: student.id,
                            text: student.student.name +'['+student.regi_no+']'
                        }];

                        $('select[name="student_id"]').empty().prepend('<option selected=""></option>').select2({allowClear: true,placeholder: 'Pick a student...', data: studentOptions});

                        $('select[name="student_id"]').val(student.id).trigger('change')

                    }
                    else {
                        toastr.error('Student Not Found!');
                    }
                    Generic.loaderStop();
                }).catch((error) => {
                let status = error.response.statusText;
                toastr.error(status);
                Generic.loaderStop();
            });
        });

        $('.documentUp').change(function(){
            var input = this;
            var url = $(this).val();
            var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
            if (input.files && input.files[0]&& (ext == "png" || ext == "jpeg" || ext == "jpg"
                || ext == "txt" || ext == "text" || ext == "doc" || ext == "docx" || ext == "pdf" || ext == "odt"))
            {
                //validate size
                var sizeImg =input.files[0].size/1024;
                if (sizeImg>1024) {
                    toastr.error("File is too big!");
                    $(input).val('');
                    return false
                }
            }
            else{
                $(input).val('');
            }
        });

        $('select#fee_type_id').on('select2:select', function () {
            let fee_id = $(this).val();
            if(parseInt(fee_id)){
                //check fee type exists or not
                let exists = false;
                $('input[name="fee_types[]"]').each(function (index, element) {
                    if($(element).val() == fee_id){
                        exists = true;
                        return false;
                    }
                });

                if(exists){
                    toastr.error("Fee already added in list!");
                    return false;
                }
                else{
                    let feeName = $('#fee_type_id').find(':selected').text();
                    let feeAmount = $('#fee_type_id').find(':selected').data('amount');
                    let trRow = '<tr>\n' +
                        '<td>\n' +
                        ' <span class="text-bold">'+feeName+'</span>\n' +
                        '<input type="hidden" value="'+fee_id+'" name="fee_types[]">\n' +
                        '</td>\n' +
                        '<td>\n' +
                        '<input type="number" name="fee_amount['+fee_id+']" class="form-control fee_amount" value="'+feeAmount+'" required min="0">\n' +
                        ' </td>\n' +
                        '<td>\n' +
                        '<button type="button" class="btn btn-sm btn-danger btnRemoveItem"><i class="fa fa-times-circle"></i></button>\n' +
                        '</td>\n' +
                        '</tr>';

                    $('#feeListTable tbody').append(trRow);
                    //calculate sum function call
                    calculateAmounts();
                }

            }

        });


        $('html').on('click','button.btnRemoveItem', function(){
            $(this).closest('tr').remove();
            //calculate sum function call
            calculateAmounts();
        });

        $('input[name="discount_amount"]').on('change keyup paste',function () {
            calculateAmounts();
        });

        $('input[name="waiver_amount"]').on('change keyup paste',function () {
            calculateAmounts();
        });

        $('input[name="paid_amount"]').on('change keyup paste',function () {
            calculateAmounts();
        });

        $('html').on('blur','input.fee_amount',function () {
            calculateAmounts();
        });

        let calculateAmounts = function () {
            let totalAmount = 0.00;

            $('input.fee_amount').each(function (index, element) {
                let feeAmount = parseFloat($(element).val());
                if(feeAmount){
                    totalAmount += feeAmount;
                }
            });

            $('#totalAmount').val(totalAmount.toFixed(2))

            let discountAmount = parseFloat($('input[name="discount_amount"]').val());
            if(discountAmount){
                totalAmount -= discountAmount;

            }
            let waiverAmount = parseFloat($('input[name="waiver_amount"]').val());
            if(waiverAmount){
                totalAmount -= waiverAmount;
            }

            $('#netAmount').val(totalAmount.toFixed(2));

            let paidAmount = parseFloat($('input[name="paid_amount"]').val());
            if(!paidAmount){
                paidAmount = 0.00;
            }

            if(totalAmount < paidAmount){
                $('input[name="paid_amount"]').val(0.00);
                paidAmount = 0.00;
            }

            $('#dueAmount').val((totalAmount - paidAmount).toFixed(2));
        };

        $('select[name="student_id"]').not('.no-ajax').on('change', function () {
            $('#dueInvoiceList>ul li').remove();
           let studentId = $(this).val();
           if(parseInt(studentId)){
               let getUrl = window.getStudentInvoiceAjaxUrl + "?student_id=" + studentId;
               Generic.loaderStart();
               axios.get(getUrl)
                   .then((response) => {
                       // console.log(response);
                       if (response.data.length) {
                           response.data.forEach(function(invoice) {
                               let dueInvoiceHtml = '<li><a href="'+window.invoicePaymentUrl.replace(/\.?0+$/, invoice.id)+'" target="_blank">'+invoice.invoice_no+' <span class="pull-right badge bg-1">'+invoice.due+'</span></a></li>';
                               $('#dueInvoiceList>ul').append(dueInvoiceHtml);
                           });
                       }
                       Generic.loaderStop();
                   }).catch((error) => {
                   let status = error.response.statusText;
                   toastr.error(status);
                   Generic.loaderStop();
               });
           }
        });

        //payment related js
        $('.btnPaymentDeatils').click(function () {
            let pk = $(this).attr('data-pk');
            let getUrl = window.paymentListUrl.replace(/\.?0+$/, pk);
            $('#paymentListTable tbody').empty();
            Generic.loaderStart();
            axios.get(getUrl)
                .then((response) => {
                    // console.log(response);
                    if(response.data.success){
                        response.data.payments.forEach(function(payment, index) {
                            let trRow = '<tr>\n' +
                                '<td>'+(index + 1)+'</td>\n' +
                                ' <td>'+payment.payment_date+'</td>\n' +
                                '<td>'+payment.payment_method+'</td>\n' +
                                '<td>'+payment.amount+' '+ window.currencySymbol+'</td>\n' +
                                '<td>\n';
                                if(payment.payment_reference){
                                    trRow += payment.payment_reference;
                                }
                                trRow +='</td>\n' +
                                '<td>\n';
                               if(payment.doc.length){
                                   trRow += '<a href="/storage/invoice/'+payment.doc+'" target="_blank" title="document" class="btn-link"><i class="fa fa-download"></i></a>\n';
                               }

                                trRow +='</td>\n' +
                                ' <td>\n' +
                                '<a href="'+window.paymentShoweUrl.replace(/\.?0+$/, payment.id)+'?print_it=1" target="_blank" title="Print" class="btn btn-primary btn-xs"><i class="fa fa-print"></i></a>\n'+
                               '<a href="#" title="Delete" class="btn btn-danger btn-xs btnPaymentDelete" data-pk="'+payment.id+'"><i class="fa fa-times-circle"></i></a>\n'+
                                '</td>\n' +
                                '</tr>';
                            $('#paymentListTable tbody').append(trRow);
                        });

                        $('#modalPaymentDetails').modal('show');
                    }
                    else{
                        toastr.error(response.data.message);
                    }
                    Generic.loaderStop();
                }).catch((error) => {
                let status = error.response.statusText;
                toastr.error(status);
                Generic.loaderStop();
            });
        });
        $('html').on('click','a.btnPaymentDelete', function () {
            let that = this;
            let pk = $(this).attr('data-pk');
            let postUrl = window.paymentDleteUrl.replace(/\.?0+$/, pk);
            Generic.loaderStart();
            axios.post(postUrl)
                .then((response) => {
                    // console.log(response);
                    if(response.data.success){
                        toastr.info(response.data.message);
                        $(this).closest('tr').remove();
                    }
                    Generic.loaderStop();
                }).catch((error) => {
                let status = error.response.statusText;
                toastr.error(status);
                Generic.loaderStop();
            });
        });

    }
    static incomeOrExpenseInit() {
        Generic.initCommonPageJS();
        Generic.initDeleteDialog();

        //add page js
        $('#account_head_id').select2({
            placeholder: 'Pick a head',
            data: window.heads,
            allowClear: true,
            templateSelection: function (data, container) {
                $(data.element).attr('data-amount', data.amount);
                return data.text;
            }
        });


        $('.documentUp').change(function(){
            var input = this;
            var url = $(this).val();
            var ext = url.substring(url.lastIndexOf('.') + 1).toLowerCase();
            if (input.files && input.files[0]&& (ext == "png" || ext == "jpeg" || ext == "jpg"
                || ext == "txt" || ext == "text" || ext == "doc" || ext == "docx" || ext == "pdf" || ext == "odt"))
            {
                //validate size
                var sizeImg =input.files[0].size/1024;
                if (sizeImg>1024) {
                    toastr.error("File is too big!");
                    $(input).val('');
                    return false
                }
            }
            else{
                $(input).val('');
            }
        });

        $('select#account_head_id').on('select2:select', function () {
            let head_id = $(this).val();
            if(parseInt(head_id)){
                //check fee type exists or not
                let exists = false;
                $('input[name="head_id[]"]').each(function (index, element) {
                    if($(element).val() == head_id){
                        exists = true;
                        return false;
                    }
                });

                if(exists){
                    toastr.error("Head already added in list!");
                    return false;
                }
                else{
                    let headName = $('#account_head_id').find(':selected').text();
                    let headAmount = $('#account_head_id').find(':selected').data('amount');
                    let trRow = '<tr>\n' +
                        '<td>\n' +
                        ' <span class="text-bold">'+headName+'</span>\n' +
                        '<input type="hidden" value="'+head_id+'" name="head_id[]">\n' +
                        '</td>\n' +
                        '<td>\n' +
                        '<input type="number" name="head_amount['+head_id+']" class="form-control head_amount" value="'+headAmount+'" required min="0">\n' +
                        ' </td>\n' +
                        '<td>\n' +
                        '<button type="button" class="btn btn-sm btn-danger btnRemoveItem"><i class="fa fa-times-circle"></i></button>\n' +
                        '</td>\n' +
                        '</tr>';

                    $('#headListTable tbody').append(trRow);
                    //calculate sum function call
                    calculateIncomeExpenseAmounts();
                }

            }

        });


        $('html').on('click','button.btnRemoveItem', function(){
            $(this).closest('tr').remove();
            //calculate sum function call
            calculateIncomeExpenseAmounts();
        });

        $('html').on('blur','input.head_amount',function () {
            calculateIncomeExpenseAmounts();
        });

        let calculateIncomeExpenseAmounts = function () {
            let totalAmount = 0.00;

            $('input.head_amount').each(function (index, element) {
                let headAmount = parseFloat($(element).val());
                if(headAmount){
                    totalAmount += headAmount;
                }
            });

            $('#totalAmount').val(totalAmount.toFixed(2));
        };


    }
}