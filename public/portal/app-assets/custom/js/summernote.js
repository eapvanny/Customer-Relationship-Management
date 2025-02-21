$(document).ready(function() {
    $('.summernote').summernote({
        toolbar: [
            ['style', ['style']],
            ['fontsize', ['fontsize']],
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['insert', ['picture', 'hr']],
            ['table', ['table']]
        ],
        height: 200,
        dialogsInBody: true,
        callbacks: {
            onInit:function(){
                $('body > .note-popover').hide();
            },
            onImageUpload: function(files, editor, welEditable) {
                sendFile(files[0], editor, welEditable,$(this));
            }
        }
    });

    function sendFile(file, editor, welEditable,selector) {
        var saveUrl = selector.data('save-image-url') ? selector.data('save-image-url') : window.location.origin + '/admin/save-summernote-image';
        data = new FormData();
        data.append("file", file);
        $.ajax({
            data: data,
            type: "POST",
            url: saveUrl,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                selector.summernote('editor.insertImage', response.url);
            }
        });
    }
});