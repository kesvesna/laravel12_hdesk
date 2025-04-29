document.addEventListener('DOMContentLoaded', function() {

    let delete_buttons = document.getElementsByClassName("good-delete-button");

    Array.from(delete_buttons).forEach(function(element) {
        element.addEventListener('click', delete_good_function);
    });

}, false);

let delete_good_function = function() {

    let good_add_count = document.getElementsByClassName("good-add-div").length;

    if(good_add_count > 1)
    {

        this.closest('.good-add-div').remove();

    } else {
        alert('Невозможно удалить. Как потом добавлять будете?');
    }

};



