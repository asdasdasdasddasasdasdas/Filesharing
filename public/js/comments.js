$(document).ready(function () {
    $('.response').on('click', function () {

        $(this).parent('.col-xl-2').parent('.row').children('.response-form').toggleClass('d-none')
    })

    $('.show-responses').on('click', function () {
        $(this).parent('.responses').children('.response-comments').toggleClass('d-none')
    })
})
