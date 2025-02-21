
$(document).ready(function(){
    // Delete Record
    if($('.delete').length > 0){
        $('.delete').click(function(){
            deleteRecord($(this).data('route'));
        });
    }
    var deleteRecord = function(route){
        swal({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((isConfirm) => {
            if(isConfirm){
                $('.overlay').show();
                $.ajax({
                    type:'DELETE',  
                    url:route,
                    dataType: "JSON",
                    data: {},
                    success:function(data){
                        if(data.success){
                            swalOnResult('Deleted Successfully!','success');
                            location.reload();
                        }else{
                            swalOnResult('Error While Deleting!','error');
                        }
                    },
                    error:function(){
                        swalOnResult('Error While Deleting!','error');
                    }
                });      
            }
        });
    }
    var swalOnResult = function(title, type){
        swal({
            title: title,
            text: "You clicked the button!",
            icon: type,
            timer: 1500,
        });
    }


})
