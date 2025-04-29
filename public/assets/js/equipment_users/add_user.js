document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("equipment-user-add-button");

    let add_function = function() {

        var $tr = $(this).closest('tr');
        var $clone = $tr.clone();
        $clone.find('input').val('');
        $tr.after($clone);

        let equipment_add_count = document.getElementsByClassName("equipment-user-add-tr").length - 1;

        $(this).closest('tr').find('.equipment-name-input').attr('name', 'equipments[' + equipment_add_count + '][building]');
        $(this).closest('tr').find('.balk_size_type').attr('name', 'equipments[' + equipment_add_count + '][floor]');
        $(this).closest('tr').find('.air_speed').attr('name', 'equipments[' + equipment_add_count + '][room]');

        Array.from(add_buttons).forEach(function(element) {
            element.addEventListener('click', add_function);
        });

        let delete_buttons = document.getElementsByClassName("equipment-user-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_function);
        });

    };

    Array.from(add_buttons).forEach(function(element) {
        element.addEventListener('click', add_function);
    });

}, false);





