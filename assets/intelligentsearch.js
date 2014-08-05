$(document).ready(function() {

    // Remove old quicksearch
    $('#quicksearch').children().remove();

    // Add new quicksearch
    $('#quicksearch').prepend($('.intelligentsearch').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            event.preventDefault();
            var params = {'search': $('.intelligentsearch').val(),
                'utf8': true
            };
            window.location = STUDIP.URLHelper.getURL('plugins.php/intelligentesucheplugin/show/index', params);
        }
    }).show());
});