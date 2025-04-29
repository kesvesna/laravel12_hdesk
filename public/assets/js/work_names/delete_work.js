document.addEventListener('DOMContentLoaded', function() {

    let delete_work_buttons = document.getElementsByClassName("work-delete-button");

    Array.from(delete_work_buttons).forEach(function(element) {
        element.addEventListener('click', delete_work_function);
    });

}, false);




