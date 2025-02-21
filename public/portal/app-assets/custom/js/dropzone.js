Dropzone.autoDiscover = false;
$(document).ready(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    if($('.dropzone').length > 0){
        $('.dropzone').each(function(){
            if($(this).hasClass('multi')) {
                multiDropzone($(this));
            }else if($(this).hasClass('multi_sortable')) {
                multiDropzoneSortable($(this));
            }else{
                singleDropzone($(this));
            }
        });
    }
    let isDelete = 1;
    $('#entryForm').submit(function(e) {
        isDelete = 0;
    });
    $(window).on('beforeunload', function(){
        let filenamae = $("#thumbnailDropzoneImage").val();
        if(isDelete==1 && filenamae){
            $.ajax({
                type: "GET",
                url: "/delete-tempfile/"+filenamae,
                dataType: 'json',
                async: false,
                success: function (response) {
                    if( response.status === true ) {
                        alert('File Deleted!');
                    }
                    else alert('Something Went Wrong!');
                }
            });
        }

    });
    function singleDropzone(_this){
        var myDropzone = _this;
        var inputSelector = $('#'+_this.data('input'));
        var width = _this.data('width');
        var height = _this.data('height');
        var actionSelector = $('#'+_this.data('action'));

        var dropzone = new Dropzone('#'+_this.attr('id'), {
            acceptedFiles: "image/jpeg, image/png, image/jpg, image/gif, image/*",
            maxFiles: 1,
            uploadMultiple: false,
            // addRemoveLinks: true,
            dictRemoveFileConfirmation: "Are you sure you want to remove this File?",
            thumbnailWidth: width,
            thumbnailHeight: height,
            maxFilesize: 50,
            transformFile: function(file, done) {
                // Create Dropzone reference for use in confirm button click handler
                var myDropZone = this;
                // Create the image editor overlay
                var editor = document.createElement('div');
                editor.style.position = 'fixed';
                editor.style.left = 0;
                editor.style.right = 0;
                editor.style.top = 0;
                editor.style.bottom = 0;
                editor.style.zIndex = 9999;
                editor.style.backgroundColor = '#000';
                document.body.appendChild(editor);
                // Create confirm button at the top left of the viewport
                var buttonConfirm = document.createElement('button');
                buttonConfirm.style.position = 'absolute';
                buttonConfirm.style.left = '10px';
                buttonConfirm.style.top = '10px';
                buttonConfirm.style.zIndex = 9999;
                buttonConfirm.textContent = 'Confirm';
                editor.appendChild(buttonConfirm);
                buttonConfirm.addEventListener('click', function() {
                    // Get the canvas with image data from Cropper.js
                    var canvas = cropper.getCroppedCanvas({
                        width: 500,
                        height: 500
                    });
                    // Turn the canvas into a Blob (file object without a name)
                    canvas.toBlob(function(blob) {
                        // Create a new Dropzone file thumbnail
                        myDropZone.createThumbnail(
                            blob,
                            myDropZone.options.thumbnailWidth,
                            myDropZone.options.thumbnailHeight,
                            myDropZone.options.thumbnailMethod,
                            false,
                            function(dataURL) {

                                // Update the Dropzone file thumbnail
                                myDropZone.emit('thumbnail', file, dataURL);
                                // Return the file to Dropzone
                                done(blob);
                            });
                    });
                    // Remove the editor from the view
                    document.body.removeChild(editor);
                });
                // Create an image node for Cropper.js
                var image = new Image();
                image.src = URL.createObjectURL(file);
                editor.appendChild(image);

                // Create Cropper.js
                var cropper = new Cropper(image, { aspectRatio: 1 });
            },
            init: function(){
                var thisDropzone = this;
                if(actionSelector.val() == 'update'){
                    var name = inputSelector.data('name');
                    var size = inputSelector.data('size');
                    var url  = inputSelector.data('url');
                    if(url){
                        myDropzone.css('height','auto');
                        var mockFile = { name: name, size: size, accepted: true };

                        thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile, url);
                        thisDropzone.files.push(mockFile);
                        thisDropzone.emit("complete", mockFile);
                        thisDropzone.options.maxFiles = thisDropzone.options.maxFiles;
                        thisDropzone._updateMaxFilesReachedClass();
                    }
                }
                this.on('sending', function(file, xhr, formData){
                    formData.append("_token", $("input[name='_token']").val());
                    formData.append("_method", "POST");
                }),
                this.on("success", function(file, response){
                    inputSelector.val(response.path);
                    $(".dz-message").hide();
                }),
                    this.on("addedfile", function(event) {
                        while (thisDropzone.files.length > thisDropzone.options.maxFiles) {
                            console.log(thisDropzone.files[0]);
                            thisDropzone.removeFile(thisDropzone.files[0]);
                        }
                        myDropzone.css('height','auto');
                    });
                this.on("removedfile", function(file){
                    file.previewElement.remove();
                    if(this.files.length < 1){
                        myDropzone.css('height','');
                    }
                })
            }
        })
    }

    function multiDropzone(_this){
        var myDropzone = _this;
        var inputSelector = $('#'+_this.data('input'));
        var deleteSelector = $('#'+_this.data('delete'));
        var width = _this.data('width');
        var height = _this.data('height');
        var actionSelector = $('#'+_this.data('action'));

        myDropzone.dropzone({
            acceptedFiles: "image/jpeg, image/png, image/jpg, image/gif, image/*",
            uploadMultiple: true,
            addRemoveLinks: true,
            dictRemoveFileConfirmation: "Are you sure you want to remove this File?",
            thumbnailWidth: width,
            thumbnailHeight: height,
            maxFilesize: 50,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            transformFile: function(file, done) {
                // Create Dropzone reference for use in confirm button click handler
                var myDropZone = this;
                // Create the image editor overlay
                var editor = document.createElement('div');
                editor.style.position = 'fixed';
                editor.style.left = 0;
                editor.style.right = 0;
                editor.style.top = 0;
                editor.style.bottom = 0;
                editor.style.zIndex = 9999;
                editor.style.backgroundColor = '#000';
                document.body.appendChild(editor);
                // Create confirm button at the top left of the viewport
                var buttonConfirm = document.createElement('button');
                buttonConfirm.style.position = 'absolute';
                buttonConfirm.style.left = '10px';
                buttonConfirm.style.top = '10px';
                buttonConfirm.style.zIndex = 9999;
                buttonConfirm.textContent = 'Confirm';
                editor.appendChild(buttonConfirm);
                buttonConfirm.addEventListener('click', function() {
                    // Get the canvas with image data from Cropper.js
                    var canvas = cropper.getCroppedCanvas({
                        width: 150,
                        height: 150
                    });
                    // Turn the canvas into a Blob (file object without a name)
                    canvas.toBlob(function(blob) {
                        // Create a new Dropzone file thumbnail
                        myDropZone.createThumbnail(
                            blob,
                            myDropZone.options.thumbnailWidth,
                            myDropZone.options.thumbnailHeight,
                            myDropZone.options.thumbnailMethod,
                            false,
                            function(dataURL) {

                                // Update the Dropzone file thumbnail
                                myDropZone.emit('thumbnail', file, dataURL);
                                // Return the file to Dropzone
                                done(blob);
                            });
                    });
                    // Remove the editor from the view
                    document.body.removeChild(editor);
                });
                // Create an image node for Cropper.js
                var image = new Image();
                image.src = URL.createObjectURL(file);
                editor.appendChild(image);

                // Create Cropper.js
                var cropper = new Cropper(image, { aspectRatio: 1 });
            },
            init: function(){
                var thisDropzone = this;
                if(actionSelector.val() == 'update'){
                    var obj_id = inputSelector.data('model-id');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type:'GET',
                        url:myDropzone.data('route-get-image'),
                        dataType: "JSON",
                        data: {id:obj_id,object:myDropzone.data('model'),collection:myDropzone.data('collection')},
                        success:function(data){
                            if(data.success){
                                myDropzone.css('height','auto');
                                Object.keys(data.images).forEach(function(key){
                                    var mockFile = { name: data.images[key].name, size: data.images[key].size, accepted: true };

                                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                                    thisDropzone.options.thumbnail.call(thisDropzone, mockFile, data.images[key].url);
                                    thisDropzone.files.push(mockFile);
                                    thisDropzone.emit("complete", mockFile);
                                    thisDropzone._updateMaxFilesReachedClass();
                                });
                            }else{

                            }
                        },
                        error:function(){
                        }
                    });

                    var name = inputSelector.data('name');
                    var size = inputSelector.data('size');
                    var url  = inputSelector.data('url');
                    if(url){
                        myDropzone.css('height','auto');
                        var mockFile = { name: name, size: size, accepted: true };

                        thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile, url);
                        thisDropzone.files.push(mockFile);
                        thisDropzone.emit("complete", mockFile);
                        thisDropzone.options.maxFiles = thisDropzone.options.maxFiles;
                        thisDropzone._updateMaxFilesReachedClass();
                    }
                }
                this.on('sending', function(file, xhr, formData){
                    formData.append("_token", $("input[name='_token']").val());
                    formData.append("_method", "POST");
                })
                this.on("success", function(file, response){
                    var val = inputSelector.val();
                    if(val == ''){
                        inputSelector.val(response.path);
                    }else{
                        var a = val + ',' + response.path
                        inputSelector.val(a);
                    }
                }),
                    this.on("addedfile", function(event) {
                        myDropzone.css('height','auto');
                    });
                this.on("removedfile", function(file){
                    file.previewElement.remove();
                    if(this.files.length < 1){
                        thisDropzone.css('height','');
                    }
                    var val = deleteSelector.val();
                    if(val == ''){
                        deleteSelector.val(file.name);
                    }else{
                        var a = val + ',' + file.name
                        deleteSelector.val(a);
                    }
                })
            }
        })
    }
    function multiDropzoneSortable(_this){

        var myDropzone = _this;
        var inputSelector = $('#'+_this.data('input'));
        var deleteSelector = $('#'+_this.data('delete'));
        var width = _this.data('width');
        var height = _this.data('height');
        var actionSelector = $('#'+_this.data('action'));

        myDropzone.dropzone({
            acceptedFiles: "image/jpeg, image/png, image/jpg, image/gif, image/*",
            uploadMultiple: true,
            addRemoveLinks: true,
            dictRemoveFileConfirmation: "Are you sure you want to remove this File?",
            thumbnailWidth: width,
            thumbnailHeight: height,
            maxFilesize: 50,
            // previewsContainer: '.visualizacao',
            // previewTemplate : $('.preview').html(),
            init: function(){
                var thisDropzone = this;
                if(actionSelector.val() == 'update'){
                    var obj_id = inputSelector.data('model-id');
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        type:'GET',
                        url:myDropzone.data('route-get-image'),
                        dataType: "JSON",
                        data: {id:obj_id,object:myDropzone.data('model'),collection:myDropzone.data('collection')},
                        success:function(data){
                            if(data.success){
                                myDropzone.css('height','auto');
                                Object.keys(data.images).forEach(function(key){
                                    var mockFile = { name: data.images[key].name, size: data.images[key].size, accepted: true };

                                    thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                                    thisDropzone.options.thumbnail.call(thisDropzone, mockFile, data.images[key].url);
                                    thisDropzone.files.push(mockFile);
                                    thisDropzone.emit("complete", mockFile);
                                    thisDropzone._updateMaxFilesReachedClass();
                                });
                            }else{

                            }
                        },
                        error:function(){
                        }
                    });

                    var name = inputSelector.data('name');
                    var size = inputSelector.data('size');
                    var url  = inputSelector.data('url');
                    if(url){
                        myDropzone.css('height','auto');
                        var mockFile = { name: name, size: size, accepted: true };

                        thisDropzone.options.addedfile.call(thisDropzone, mockFile);
                        thisDropzone.options.thumbnail.call(thisDropzone, mockFile, url);
                        thisDropzone.files.push(mockFile);
                        thisDropzone.emit("complete", mockFile);
                        thisDropzone.options.maxFiles = thisDropzone.options.maxFiles;
                        thisDropzone._updateMaxFilesReachedClass();
                    }
                }
                // this.on('completemultiple', function(file, json) {
                //    console.log(json);
                // }),
                this.on('sending', function(file, xhr, formData){
                    formData.append("_token", $("input[name='_token']").val());
                    formData.append("_method", "POST");
                }),

                this.on("success", function(file, response){
                    var val = inputSelector.val();

                    if(val == ''){
                        inputSelector.val(response.path);
                    }else{
                        var a = val + ',' + response.path;
                        inputSelector.val(a);
                    }

                }),
                    this.on("successmultiple", function(file, response){
                        var path =  response.path;
                        var split_path = path.split(",");

                        split_path.forEach(function(item){
                            $('#sortable').append('<li class="ui-state-default" id="'+item+'">\n' +
                                '                                                  <img src="/'+item+'">\n' +
                                '                                                    <div class="edit">\n' +
                                '                                                    <a href="#"  class="remove-image" style="color: red;text-decoration: none;">\n' +
                                '                                                       <i class="fa fa-trash"></i>\n' +
                                '                                                    </a>\n' +
                                '                                                        </div>\n' +
                                '\n' +
                                '                                                </li>');
                        });
                        setImage_arr();
                    }),
                    this.on("addedfile", function(event) {
                        myDropzone.css('height','auto');
                    });
                this.on("removedfile", function(file){
                    file.previewElement.remove();
                    if(this.files.length < 1){
                        thisDropzone.css('height','');
                    }
                    var val = deleteSelector.val();
                    if(val == ''){
                        deleteSelector.val(file.name);
                    }else{
                        var a = val + ',' + file.name
                        deleteSelector.val(a);
                    }
                })
            }
        })
    }
    function setImage_arr() {
        var imageids_arr = [];
        $('#sortable li').each(function(){
            var id = $(this).attr('id');
            imageids_arr.push(id);
        });
        $('#sortable_image_dz').val(imageids_arr)
    }
});




