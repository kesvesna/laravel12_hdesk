document.addEventListener('DOMContentLoaded', function() {

    let delete_buttons = document.getElementsByClassName("user-delete-button");

    Array.from(delete_buttons).forEach(function(element) {
        element.addEventListener('click', delete_function);
    });

}, false);

let delete_function = function() {

    let user_add_count = document.getElementsByClassName("user-add-div").length;

    if(user_add_count > 1)
    {

        this.parentElement.remove();

    } else {
        alert('Невозможно удалить. Как потом добавлять будете?');
    }

};



