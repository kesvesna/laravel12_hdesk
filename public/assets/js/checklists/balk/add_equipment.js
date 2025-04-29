document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("equipment-add-button");

    let onchange_equipment_function = function() {

        let equipment_id = $(this).val();

        $(this).attr('data-equipment-id', equipment_id);
        $(this).attr('name', 'equipments[' + equipment_id + '][id]');

        $(this).closest('tr').find('.balk_size_type').attr('name', 'equipments[' + equipment_id + '][balk_size_type]');
        $(this).closest('tr').find('.air_speed').attr('name', 'equipments[' + equipment_id + '][air_speed]');
        $(this).closest('tr').find('.air_pressure').attr('name', 'equipments[' + equipment_id + '][air_pressure]');
        $(this).closest('tr').find('.air_flow_rate').attr('name', 'equipments[' + equipment_id + '][air_flow_rate]');
        $(this).closest('tr').find('.air_flap').attr('name', 'equipments[' + equipment_id + '][air_flap]');
        $(this).closest('tr').find('.air_inlet_temperature').attr('name', 'equipments[' + equipment_id + '][air_inlet_temperature]');
        $(this).closest('tr').find('.air_outlet_temperature').attr('name', 'equipments[' + equipment_id + '][air_outlet_temperature]');

        $(this).closest('tr').find('.cold_water_inlet_temperature').attr('name', 'equipments[' + equipment_id + '][cold_water_inlet_temperature]');
        $(this).closest('tr').find('.cold_water_outlet_temperature').attr('name', 'equipments[' + equipment_id + '][cold_water_outlet_temperature]');
        $(this).closest('tr').find('.cold_water_pressure_drop').attr('name', 'equipments[' + equipment_id + '][cold_water_pressure_drop]');
        $(this).closest('tr').find('.cold_water_valve').attr('name', 'equipments[' + equipment_id + '][cold_water_valve]');
        $(this).closest('tr').find('.cold_water_rate').attr('name', 'equipments[' + equipment_id + '][cold_water_rate]');

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





