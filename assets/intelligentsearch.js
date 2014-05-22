$(document).ready(function() {
    $('#quicksearch').prepend($('.intelligentsearch').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            event.preventDefault();
            window.location = STUDIP.URLHelper.getURL('plugins.php/intelligentesucheplugin/show/index?search='+$('.intelligentsearch').val());
        }
    }).show());
});