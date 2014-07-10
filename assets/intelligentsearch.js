$(document).ready(function() {
    
    // Remove old quicksearch
    $('#quicksearch').children().remove();
    
    // Add new quicksearch
    $('#quicksearch').prepend($('.intelligentsearch').keypress(function(event) {
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if (keycode == '13') {
            event.preventDefault();
            window.location = STUDIP.URLHelper.getURL('plugins.php/intelligentesucheplugin/show/index')+'?search='+$('.intelligentsearch').val();
        }
    }).show());
});