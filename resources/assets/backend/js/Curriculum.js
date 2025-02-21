import Generic from "./Generic";

export default class Curriculum {
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

    static loadCurriculumSubject(element, curriculum_id, value, placeholder, callback){
        $.ajax({
            type: "GET",
            url: "/ajax/subject/search",
            data: {'curriculum_id' : curriculum_id},
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

    static loadCurriculumSubjectByPeriod(element, curriculum_id, study_period_type_id, value, placeholder, callback){
        $.ajax({
            type: "GET",
            url: "/ajax/subject/search",
            data: {'curriculum_id' : curriculum_id, 'study_period_type_id': study_period_type_id},
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

                }
                if (callback !== undefined){
                    callback(response.status, response.data, response.message);
                }
                else alert('Something Went Wrong!');
            }
        });
    }
    static requestCurriculumSchedules(request_data, callback){
        $.ajax({
            type: "GET",
            url: "/ajax/curriculum_schedule/search",
            data: request_data,
            dataType: 'json',
            async: true,
            success: function (response) {
                if( response.status === true ) {
                    if (callback !== undefined){
                        callback(response.data);
                    }
                }
                else alert('Something Went Wrong!');
            }
        });
    }

    static loadCurriculumClass(element, curriculum_id, value, placeholder, callback){
        $.ajax({
            type: "GET",
            url: "/ajax/class/search",
            data: {'curriculum_id' : curriculum_id},
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
    /**
     * academic related codes
     */

    // static loadCurriculumTeachingSemester(element, curriculum_id, semester, callback){
    //     $.ajax({
    //         type: "GET",
    //         url: "/ajax/curriculum_teaching_semester/search",
    //         data: {'curriculum_id' : curriculum_id},
    //         dataType: 'json',
    //         async: true,
    //         success: function (response) {
    //             if( response.status === true ) {
    //                 $(element).empty(); // Clear existing options
    //                 $(element).append("<option value=''>Pick a semester...</option>");
    //                 $.each(response.data, function(key, name) {
    //                     $(element).append("<option value='" + key + "'>" + name + "</option>");
    //                 });
    //                 if(semester != ''){
    //                     $(element).val(semester);
    //                 }
    //                 if (callback !== undefined){
    //                     callback();
    //                 }
    //             }
    //             else alert('Something Went Wrong!');
    //         }
    //     });
    // }




    static loadCurriculumTeachingPeriodType(element, i_class_id, study_period_type_id, callback){
        $.ajax({
            type: "GET",
            url: "/ajax/curriculum_teaching_period/search",
            data: {'i_class_id' : i_class_id},
            dataType: 'json',
            async: true,
            success: function (response) {
                if( response.status === true ) {
                    $(element).empty(); // Clear existing options
                    $(element).append("<option value=''>Pick a study period...</option>");
                    $.each(response.data, function(key, name) {
                        $(element).append("<option value='" + key + "'>" + name + "</option>");
                    });
                    if(study_period_type_id != ''){
                        $(element).val(study_period_type_id);
                    }
                    if (callback !== undefined){
                        callback();
                    }
                }
                else alert('Something Went Wrong!');
            }
        });
    }



    static loadCurriculumTeachingClass(element, curriculum_id, teaching_period, i_class_id, placeholder, callback){
        if (typeof(placeholder) == 'undefined'){
            placeholder = 'Pick a class...';
        }
        $.ajax({
            type: "GET",
            url: "/ajax/curriculum_teaching_class/search",
            data: {'curriculum_id' : curriculum_id, 'teaching_period' : teaching_period},
            dataType: 'json',
            async: true,
            success: function (response) {
                if( response.status === true ) {
                    $(element).empty(); // Clear existing options
                    $(element).append("<option value=''>" + placeholder + "</option>");
                    $.each(response.data, function(key, name) {
                        $(element).append("<option value='" + key + "'>" + name + "</option>");
                    });
                    if(i_class_id != ''){
                        $(element).val(i_class_id);
                    }
                    if (callback !== undefined){
                        callback();
                    }
                }
                else alert('Something Went Wrong!');
            }
        });
    }

    static loadCurriculumTeachingSubject(element, curriculum_id, semester, i_class_id, subject_id, callback){
        $.ajax({
            type: "GET",
            url: "/ajax/curriculum_teaching_subject/search",
            data: {'curriculum_id' : curriculum_id, 'semester' : semester, 'i_class_id' : i_class_id},
            dataType: 'json',
            async: true,
            success: function (response) {
                if( response.status === true ) {
                    $(element).empty(); // Clear existing options
                    $(element).append("<option value=''>Pick a subject...</option>");
                    $.each(response.data, function(key, name) {
                        $(element).append("<option value='" + key + "'>" + name + "</option>");
                    });
                    if(subject_id != ''){
                        $(element).val(subject_id);
                    }
                    if (callback !== undefined){
                        callback();
                    }
                }
                else alert('Something Went Wrong!');
            }
        });
    }

    static loadCurriculumTeachingTeacher(element, curriculum_id, semester, i_class_id, subject_id, teacher_id, callback){
        $.ajax({
            type: "GET",
            url: "/ajax/curriculum_teaching_teacher/search",
            data: {'curriculum_id' : curriculum_id, 'semester' : semester, 'i_class_id' : i_class_id, 'subject_id': subject_id},
            dataType: 'json',
            async: true,
            success: function (response) {
                if( response.status === true ) {
                    $(element).empty(); // Clear existing options
                    $(element).append("<option value=''>Pick a teacher...</option>");
                    $.each(response.data, function(key, name) {
                        $(element).append("<option value='" + key + "'>" + name + "</option>");
                    });
                    if(teacher_id != ''){
                        $(element).val(teacher_id);
                    }
                    if (callback !== undefined){
                        callback();
                    }
                }
                else alert('Something Went Wrong!');
            }
        });
    }


    static getCurriculumSubjectByPeriod(curriculum_id, study_period_type_id, callback){
        $.ajax({
            type: "GET",
            url: "/ajax/subject/search",
            data: {'curriculum_id' : curriculum_id, 'study_period_type_id': study_period_type_id},
            dataType: 'json',
            async: true,
            success: function (response) {
                if (callback !== undefined){
                    callback(response.status, response.data, response.message);
                }
                else alert('Something Went Wrong!');
            }
        });
    }


    static loadMajor(element, academic_year_id, department_id, value, placeholder, callback){
        $.ajax({
            type: "GET",
            url: "ajax/curriculum_majors_search",
            data: {'academic_year_id' : academic_year_id, 'department_id' : department_id},
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

    static loadGradeLevel(element, academic_year_id, major_id, academic_degree_id, value, placeholder, callback){
        $.ajax({
            type: "GET",
            url: "ajax/curriculum_grade_levels_search",
            data: {'academic_year_id' : academic_year_id, 'major_id' : major_id, 'academic_degree_id': academic_degree_id},
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

    static loadClass(element, academic_year_id, major_id, grade_level_id , value, placeholder, callback){
        $.ajax({
            type: "GET",
            url: "ajax/curriculum_classes_search",
            data: {'academic_year_id' : academic_year_id, 'major_id' : major_id, 'grade_level_id' : grade_level_id},
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


    static loadStudyPeriodType(element, academic_year_id, major_id, grade_level_id , value, placeholder, callback){
        $.ajax({
            type: "GET",
            url: "ajax/curriculum_study_period_type/search",
            data: {'academic_year_id' : academic_year_id, 'major_id' : major_id, 'grade_level_id' : grade_level_id},
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
    static getSubjects(academic_year_id, major_id, grade_level_id, study_period_type_id, callback){
        $.ajax({
            type: "GET",
            url: "ajax/curriculum_subject/search",
            data: {'academic_year_id' : academic_year_id, 'major_id' : major_id, 'grade_level_id' : grade_level_id, 'study_period_type_id': study_period_type_id},
            dataType: 'json',
            async: true,
            success: function (response) {
                if( response.status === true ) {
                    if (callback !== undefined){
                        callback(response.status, response.data, response.message);
                    }
                }
                else alert('Something Went Wrong!');
            }
        });
    }
    static loadSubject(element, academic_year_id, major_id, grade_level_id, study_period_type_id, value, placeholder, callback){
        $.ajax({
            type: "GET",
            url: "ajax/curriculum_subject/search",
            data: {'academic_year_id' : academic_year_id, 'major_id' : major_id, 'grade_level_id' : grade_level_id, 'study_period_type_id': study_period_type_id},
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
    static loadDepartment(element, academic_year_id, value, placeholder, callback){
        $.ajax({
            type: "GET",
            url: " ajax/curriculum_department_search",
            data: {'academic_year_id' : academic_year_id},
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

    static loadAcademicDegree(element, academic_year_id, major_id, value, placeholder, callback){
        this.ajaxSelectorLoad(
            element,
            "ajax/curriculum_academic_degree_search",
            {"academic_year_id": academic_year_id, "major_id": major_id},
            value,
            placeholder,
            callback
        );
    }

}
