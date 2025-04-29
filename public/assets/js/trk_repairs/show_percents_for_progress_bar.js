document.addEventListener('DOMContentLoaded', function() {

    $('#done_progress').on('input', function() {
        $('#percents_done_progress').text($(this).val() + '%');
    });

}, false);


