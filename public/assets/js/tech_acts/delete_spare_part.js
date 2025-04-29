document.addEventListener('DOMContentLoaded', function() {

    let delete_buttons = document.getElementsByClassName("tech-act-spare-part-delete-button");

    Array.from(delete_buttons).forEach(function(element) {
        element.addEventListener('click', delete_spare_part_function);
    });

}, false);

let delete_spare_part_function = function() {

    let spare_part_add_count = document.getElementsByClassName("tech-act-spare-part-add-div").length;

    if(spare_part_add_count > 1)
    {

        this.closest('.tech-act-spare-part-add-div').remove();

    } else {
        alert('Невозможно удалить. Как потом добавлять будете?');
    }

};



