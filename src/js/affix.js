$(function () {
    // Make statusbar affixed (with Bootstrap script)
    var statusbar = $('#statusbar');
    if(statusbar && $(document).width() > 769) {
        statusbar.affix({
            offset: {
                top: statusbar.offset().top
            }
        });
        statusbar.on('affix.bs.affix', function () {
            statusbar.find('.dropup').removeClass('dropup').addClass('dropdown');
        });
        statusbar.on('affix-top.bs.affix', function () {
            statusbar.find('.dropdown').removeClass('dropdown').addClass('dropup');
        });
    }
    // Activate all tooltips
    $('[data-toggle="tooltip"]').tooltip();
});
