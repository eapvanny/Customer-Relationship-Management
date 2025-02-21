import Generic from "./Generic";

export default class CurriculumGroup {
    static ajaxSelectorLoad(element, url, search_data, value, placeholder, callback)
    {
        $.ajax({
            type: "GET",
            url: url,
            data: search_data,
            dataType: 'json',
            async: true,
            success: function (response) {
                if( response.status === true ) {
                    $(element).empty(); // Clear existing options
                    $(element).append("<option value=''>" + placeholder + "</option>");
                    $.each(response.data, function(key, name) {
                        $(element).append("<option value='" + key + "'>" + name + "</option>");
                    });
                    if(value != ''){
                        $(element).val(value);
                    }
                    if (callback !== undefined){
                        callback(response.status, response.data, response.message);
                    }
                }
                else alert('Something Went Wrong!');
            }
        });
    }

    static loadAcademicYears(element, value, placeholder, callback){
        this.ajaxSelectorLoad(
            element,
            "ajax/ajax_exam_event/get_academic_year_options",
            {},
            value,
            placeholder,
            callback
        );
    }

    static loadDepartments(element, academic_year_id, value, placeholder, callback){
        this.ajaxSelectorLoad(
            element,
            "ajax/ajax_exam_event/get_department_options",
            {'academic_year_id': academic_year_id},
            value,
            placeholder,
            callback
        );
    }

    static loadMajors(element, academic_year_id, department_id, value, placeholder, callback){
        this.ajaxSelectorLoad(
            element,
            "ajax/ajax_exam_event/get_major_options",
            {'academic_year_id': academic_year_id, 'department_id': department_id},
            value,
            placeholder,
            callback
        );
    }

    static loadExamEvents(element, academic_year_id, major_id, value, placeholder, callback){
        this.ajaxSelectorLoad(
            element,
            "ajax/ajax_exam_event/get_exam_event_options",
            {'academic_year_id': academic_year_id, 'major_id': major_id},
            value,
            placeholder,
            callback
        );
    }

    static loadExamEventRooms(element, exam_event_id, value, placeholder, callback){
        this.ajaxSelectorLoad(
            element,
            "ajax/ajax_exam_event/get_exam_event_room_options",
            {'exam_event_id': exam_event_id},
            value,
            placeholder,
            callback
        );
    }

}
