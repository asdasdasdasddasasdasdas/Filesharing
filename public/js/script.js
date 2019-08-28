$('.header-angle-down').on('click', function () {
    $(this).toggleClass('scaleY')
    $('.header-dropdown-menu').toggleClass('d-none');
})