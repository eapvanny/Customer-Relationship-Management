import Generic from "./Generic";

export default class ExamEventRoom {

    static loadExamEventSubjects(element, exam_event_id, value, placeholder, callback){
        $.ajax({
            type: "GET",
            url: "/ajax/exam_event_subjects/search",
            data: {'exam_event_id' : exam_event_id},
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
                        callback();
                    }
                }
                else alert('Something Went Wrong!');
            }
        });
    }



}
