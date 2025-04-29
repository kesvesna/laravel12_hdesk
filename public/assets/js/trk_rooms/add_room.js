document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("room-add-button");

    let add_function = function() {

        let room_add_div = this.closest('div');
        let rooms_add_parent = this.closest('.rooms-add-parent-div');

        let new_room_add_div = room_add_div.cloneNode(true);
        let inputNode = new_room_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        rooms_add_parent.appendChild(new_room_add_div);

        Array.from(add_buttons).forEach(function(element) {
            element.addEventListener('click', add_function);
        });

        let delete_buttons = document.getElementsByClassName("room-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_function);
        });

    };

    Array.from(add_buttons).forEach(function(element) {
        element.addEventListener('click', add_function);
    });


}, false);


