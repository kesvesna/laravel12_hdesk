document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("equipment-add-button");

    let add_function = function() {

        let equipment_add_div = this.closest('div');
        let equipments_add_parent = this.closest('.equipments-add-parent-div');

        let new_equipment_add_div = equipment_add_div.cloneNode(true);
        let inputNode = new_equipment_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        equipments_add_parent.appendChild(new_equipment_add_div);

        Array.from(add_buttons).forEach(function(element) {
            element.addEventListener('click', add_function);
        });

        let delete_buttons = document.getElementsByClassName("equipment-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_function);
        });

    };

    Array.from(add_buttons).forEach(function(element) {
        element.addEventListener('click', add_function);
    });


}, false);


