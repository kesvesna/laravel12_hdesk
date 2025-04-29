document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("equipment-add-button");

    let onchange_equipment_function = function() {

        let equipment_id = $(this).val();

        $(this).attr('data-equipment-id', equipment_id);
        $(this).attr('name', 'equipments[' + equipment_id + '][id]');

        $(this).closest('tr').find('.extract_air_t').attr('name', 'equipments[' + equipment_id + '][supply_air_t]');
        $(this).closest('tr').find('.extract_engine_t').attr('name', 'equipments[' + equipment_id + '][supply_engine_t]');
        $(this).closest('tr').find('.front_bearing_t').attr('name', 'equipments[' + equipment_id + '][front_bearing_t]');
        $(this).closest('tr').find('.extract_engine_terminal_contact_t').attr('name', 'equipments[' + equipment_id + '][supply_engine_terminal_contact_t]');
        $(this).closest('tr').find('.service_switch_contact_t').attr('name', 'equipments[' + equipment_id + '][service_switch_contact_t]');
        $(this).closest('tr').find('.extract_engine_actual_current').attr('name', 'equipments[' + equipment_id + '][supply_engine_actual_current]');
        $(this).closest('tr').find('.extract_engine_passport_current').attr('name', 'equipments[' + equipment_id + '][supply_engine_passport_current]');
        $(this).closest('tr').find('.extract_engine_actual_frequency').attr('name', 'equipments[' + equipment_id + '][supply_engine_actual_frequency]');
        $(this).closest('tr').find('.extract_engine_passport_frequency').attr('name', 'equipments[' + equipment_id + '][supply_engine_passport_frequency]');
        $(this).closest('tr').find('.extract_air_actual_rate').attr('name', 'equipments[' + equipment_id + '][supply_air_actual_rate]');
        $(this).closest('tr').find('.extract_air_passport_rate').attr('name', 'equipments[' + equipment_id + '][supply_air_passport_rate]');
        $(this).closest('tr').find('.comment').attr('name', 'equipments[' + equipment_id + '][comment]');
    };

    $('.equipment-name-input').on('change', onchange_equipment_function);

    let add_function = function() {

        var $tr = $(this).closest('tr');
        var $clone = $tr.clone();
        $clone.find('input').val('');
        $tr.after($clone);

        $('.equipment-name-input').on('change', onchange_equipment_function);

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





