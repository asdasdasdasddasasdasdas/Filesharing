$(document).ready(function () {

    let fd = new FormData;
    let d = $('.dropzone,.drag-and-drop');

    let dropzone = $('.dropzone');

    let progressBar = $('#progressbar');


    $('.dropzone-form').submit(function (e) {

        e.preventDefault();
        fd.append('description', $(".description").val())
        $('#dropzone-submit').attr('disabled', true)
        f(fd)

    });

    d.on('dragover', function (e) {
        e.preventDefault();
        e.stopPropagation();
        dropzone.addClass('hover');
    });
    d.on('dragenter', function (e) {
        e.preventDefault();
        e.stopPropagation();

        dropzone.addClass('hover');
        return false;
    });


    d.on('dragleave', function (e) {
        e.preventDefault();
        e.stopPropagation();

        dropzone.removeClass('hover');
        return false;
    });


    $('.dropzone').on('drop', function (e) {
        e.preventDefault();

        dropzone.removeClass('hover');
        let files = e.originalEvent.dataTransfer.files;

        files = [...files];

        for (i = 0; i < files.length; i++) {

            if (files[i].type == '') {
                alert("Недопустимый тип файла");
                return;
            }
            if (fd.getAll('file[]').length >= 20) {
                alert("Слишком много файлов максимум: 20");
                return;
            }

            fd.append('file[]', files[i])
            previewFile(files[i])
        }

        $('.dropzone-form').removeClass('d-none');

        $('.left-part').fadeOut();

        let token = $("input[name=token]").attr('value');

        fd.append('token', token);

    });

    function previewFile(file) {

        let reader = new FileReader();
        reader.readAsDataURL(file);


        let src = getSrc(file);
        reader.onloadend = function (e) {
            if (src == null) {
                src = e.target.result
            }
            var upload = $("<div class='col-3'><img class='upload-img mx-2' src=" + src + " alt=\"\"><p class='img-name'>" + file.name + "</p></div>").hide().fadeIn(400);
            $('.gallery-files').children('.row').append(upload)
        }
    }

    function getSrc(file) {
        let src = file.type;


        src = src.split('/')[0];

        switch (src) {
            case 'image':
                return null;
            case 'application':
                return '/img/application.png';
            case 'video':
                return '/img/video.png';
            case 'audio':
                return '/img/audio.png';
            case 'text':
                return '/img/text.png';
            case '' :
                return false;
            default:
                return "/img/undefined.png";

        }

    }

    function f(files) {

        $.ajax({
            url: "/",
            type: "POST",
            data: files,
            beforeSend: function () {

            },
            success: function (response) {

                window.location = '/show'
            },
            error: function (errorThrown) {

                return errorThrown.responseText
            },
            xhr: function () {
                var xhr = $.ajaxSettings.xhr();

                xhr.upload.addEventListener('progress', function (evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = Math.ceil(evt.loaded / evt.total * 100);
                        progressBar.val(percentComplete).text('Загружено ' + percentComplete + '%');
                    }
                }, false);
                return xhr;
            },
            cache: false,
            contentType: false,
            dataType: 'text',
            processData: false,

        });
        return false;
    }


});

