document.addEventListener('DOMContentLoaded', function() {

    let delete_buttons = document.getElementsByClassName("executor-delete-button");

    Array.from(delete_buttons).forEach(function(element) {
        element.addEventListener('click', delete_function);
    });

}, false);

let delete_function = function() {

    let executor_add_count = document.getElementsByClassName("executor-add-div").length;

    if(executor_add_count > 1)
    {

        this.parentElement.remove();

    } else {
        alert('Невозможно удалить. Как потом добавлять будете?');
    }

};



