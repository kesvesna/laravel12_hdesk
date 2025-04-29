document.addEventListener('DOMContentLoaded', function() {

    let delete_spare_part_buttons = document.getElementsByClassName("spare-part-delete-button");

    Array.from(delete_spare_part_buttons).forEach(function(element) {
        element.addEventListener('click', delete_spare_part_function);
    });

}, false);




