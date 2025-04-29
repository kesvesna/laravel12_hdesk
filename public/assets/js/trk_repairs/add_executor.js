document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("executor-add-button");

    let add_function = function() {

        let executor_add_div = this.closest('div');
        let executors_add_parent = this.closest('.executors-add-parent-div');

        let new_executor_add_div = executor_add_div.cloneNode(true);
        let inputNode = new_executor_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        executors_add_parent.appendChild(new_executor_add_div);

        Array.from(add_buttons).forEach(function(element) {
            element.addEventListener('click', add_function);
        });

        let delete_buttons = document.getElementsByClassName("executor-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_function);
        });

    };

    Array.from(add_buttons).forEach(function(element) {
        element.addEventListener('click', add_function);
    });


}, false);


