document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("equipment-add-button");

    let onchange_equipment_function = function() {

        let equipment_id = $(this).val();

        $(this).attr('data-equipment-id', equipment_id);
        $(this).attr('name', 'equipments[' + equipment_id + '][id]');

        $(this).closest('tr').find('.measuring_point_number').attr('name', 'equipments[' + equipment_id + '][measuring_point_number]');
        $(this).closest('tr').find('.air_direction_type').attr('name', 'equipments[' + equipment_id + '][air_direction_type]');
        $(this).closest('tr').find('.length_or_diameter').attr('name', 'equipments[' + equipment_id + '][length_or_diameter]');
        $(this).closest('tr').find('.width').attr('name', 'equipments[' + equipment_id + '][width]');
        $(this).closest('tr').find('.air_speed').attr('name', 'equipments[' + equipment_id + '][air_speed]');
        $(this).closest('tr').find('.duct_cross_sectional_area').attr('name', 'equipments[' + equipment_id + '][duct_cross_sectional_area]');
        $(this).closest('tr').find('.air_flow_rate').attr('name', 'equipments[' + equipment_id + '][air_flow_rate]');
        $(this).closest('tr').find('.air_pressure').attr('name', 'equipments[' + equipment_id + '][air_pressure]');
        $(this).closest('tr').find('.air_temperature').attr('name', 'equipments[' + equipment_id + '][air_temperature]');
        $(this).closest('tr').find('.air_throttling_valve').attr('name', 'equipments[' + equipment_id + '][air_throttling_valve]');
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





