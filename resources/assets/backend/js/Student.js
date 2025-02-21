import Generic from "./Generic";

export default class Student {
    static loadStudentByClass(element, class_id, value, placeholder, callback){
        $.ajax({
            type: "GET",
            url: "/ajax/student/search",
            data: {'class_id' : class_id},
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
                        callback(response.data);
                    }
                }
                else alert('Something Went Wrong!');
            }
        });
    }

}
