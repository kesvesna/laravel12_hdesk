document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("user-add-button");

    let add_function = function() {

        let user_add_div = this.closest('div');
        let users_add_parent = this.closest('.users-add-parent-div');

        let new_user_add_div = user_add_div.cloneNode(true);
        let inputNode = new_user_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        users_add_parent.appendChild(new_user_add_div);

        Array.from(add_buttons).forEach(function(element) {
            element.addEventListener('click', add_function);
        });

        let delete_buttons = document.getElementsByClassName("user-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_function);
        });

    };

    Array.from(add_buttons).forEach(function(element) {
        element.addEventListener('click', add_function);
    });


}, false);


