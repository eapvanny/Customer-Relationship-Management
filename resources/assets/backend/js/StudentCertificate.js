import Generic from "./Generic";

export default class StudentCertificate{
    static ajaxSelectorLoad(element, url, search_data, value, placeholder,callback)
    {
        $.ajax({
            type: "GET",
            url: url,
            data: search_data,
            dataType: 'json',
            async: true,
            success: function(response){
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
                } else {
                    swal({
                        title:  response.message,
                        text: response.data,
                        type: "warning"
                    });
                }
            }
        });
    }

    static ajaxSelectorLoadGeadeLevel(element, url, search_data, value, placeholder, callback) {
        $.ajax({
            type: "GET",
            url: url,
            data: search_data,
            dataType: 'json',
            async: true,
            success: function(response) {
                if (response.status === true) {
                    $(element).empty(); // Clear existing options
                    $(element).append("<option value=''>" + placeholder + "</option>");
                    const sortedGradeLevels = Object.entries(response.data).sort((a, b) => {
                        const nameA = a[1];
                        const nameB = b[1];
                        // Using localeCompare for language-agnostic sorting
                        return nameA.localeCompare(nameB, undefined, { numeric: true, sensitivity: 'base' });
                    });
                    sortedGradeLevels.forEach(([key, name]) => {
                        $(element).append("<option value='" + key + "'>" + name + "</option>");
                    });
                    if (value != '') {
                        $(element).val(value);
                    }
                    if (callback !== undefined) {
                        callback(response.status, response.data, response.message);
                    }
                } else {
                    swal({
                        title: response.message,
                        text: response.data,
                        type: "warning"
                    });
                }
            }
        });
    }
    
    static loadDepartment(element, academic_year_id, value, placeholder, callback)
    {
        this.ajaxSelectorLoad(
            element,
            "ajax/student_certificate_department_search",
            {'academic_year_id': academic_year_id},
            value,
            placeholder,
            callback
        );
    }

    static loadMajor(element, academic_year_id, department_id, value, placeholder, callback)
    {
        this.ajaxSelectorLoad(
            element,
            "ajax/student_certificate_major_search",
            {'academic_year_id' : academic_year_id, 'department_id' : department_id},
            value,
            placeholder,
            callback
        );
    }

    static loadAcademicDegree(element, academic_year_id, major_id, value, placeholder, callback)
    {
        this.ajaxSelectorLoad(
            element,
            "ajax/student_certificate_academic_degree_search",
            {"academic_year_id": academic_year_id, "major_id": major_id},
            value,
            placeholder,
            callback
        );
    }

    static loadGradeLevel(element, academic_year_id, major_id, academic_degree_id, value, placeholder, callback){
        this.ajaxSelectorLoadGeadeLevel(
            element,
            "ajax/student_certificate_grade_level_search",
            {'academic_year_id' : academic_year_id, 'major_id' : major_id, 'academic_degree_id': academic_degree_id},
            value,
            placeholder,
            callback
        );
    }

    static loadClass(element, academic_year_id, major_id, grade_level_id , value, placeholder, callback){
        this.ajaxSelectorLoad(
            element,
            "ajax/student_certificate_class_search",
            {'academic_year_id' : academic_year_id, 'major_id' : major_id, 'grade_level_id' : grade_level_id},
            value,
            placeholder,
            callback
        );

    }

    static loadStudyPeriodType(element, academic_year_id, major_id, grade_level_id , value, placeholder, callback){
        this.ajaxSelectorLoad(
            element,
            "ajax/student_certificate_study_period_search",
            {'academic_year_id' : academic_year_id, 'major_id' : major_id, 'grade_level_id' : grade_level_id},
            value,
            placeholder,
            callback
        );

    }
}