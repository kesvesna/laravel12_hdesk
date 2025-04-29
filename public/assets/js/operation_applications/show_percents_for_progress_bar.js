document.addEventListener('DOMContentLoaded', function() {

    $('#done_percents').on('input', function() {
        $('#percents_done_progress').text($(this).val() + '%');
    });

}, false);


