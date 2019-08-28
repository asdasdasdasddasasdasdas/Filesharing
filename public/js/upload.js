$(document).ready(function () {
    var i = 1;


    $('.custom-file-wrapper').on('change', '.input-file', function (e) {

        let id = $(this).attr('id');

        $('#' + id.replace('file', 'label')).css('background', 'green')
        $('#' + id.replace('file', 'label')).css('color', 'white')
        $('#' + id.replace('file', 'label')).html(this.files[0].name)


    })


    $('#add-input').on('click', function () {
        if (i >= 20) {
            return false;
        }
        i++
        let attr = 'file-' + i;
        let label = 'label-' + i;
        $('.custom-file-wrapper').append(" <div class=\"file-upload mb-4\">\n" +
            "                <label class=\"file-upload-label mx-0 mx-xl-5 text-center\" id=" + label + " for=" + attr + ">\n" +
            "                    <span class=\"h6\">\n" +
            "                        Choose file\n" +
            "                    </span>\n" +
            "                    <span class=\"button\">Open</span></label>\n" +
            "                <input id=" + attr + " class=\"input-file\" name=\"file[]\" type=\"file\"  multiple>\n" +
            "\n" +
            "            </div>");


    })


})