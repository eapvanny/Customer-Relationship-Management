$(document).ready(function () {
    $('input.parent_status').on('ifChecked', function(event){
        var status = $(this).val();
        if(status=='all'){
            $('.father_block').show();
            $('.mather_block').show();
        }
        if(status=='father'){
            $('.father_block').show();
            $('.mather_block').hide();
        }
        if(status=='mother'){
            $('.father_block').hide();
            $('.mather_block').show();
        }
    });
});