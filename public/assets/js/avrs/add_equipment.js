document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("equipment-add-button");
    let add_spare_part_buttons = document.getElementsByClassName("spare-part-add-button");
    let add_work_buttons = document.getElementsByClassName("work-add-button");
    let add_group_work_buttons = document.getElementsByClassName("group-work-add-button");


    let onchange_equipment_function = function() {

        let equipment_id = $(this).val();

        $(this).attr('data-equipment-id', equipment_id);
        $(this).attr('name', 'equipment[' + equipment_id + ']');

        let equip_div = $(this).closest('.equipment-add-div');

        // set all works input name
        $(equip_div).find('.work-add-div').each(function() {
            let rand_id = (Math.random() + 1).toString(20);
            $(this).find('.work-type-input').attr('name', 'equipment[' + equipment_id + '][work][' + rand_id + '][type]')
            $(this).find('.work-comment-textarea').attr('name', 'equipment[' + equipment_id + '][work][' + rand_id + '][comment]')
        });

        //set all spare_parts inputs name
        $(equip_div).find('.spare-part-add-div').each(function() {
            let rand_id = (Math.random() + 1).toString(20);
            $(this).find('.spare-part-name-input').attr('name', 'equipment[' + equipment_id + '][spare_part][' + rand_id + '][name]')
            $(this).find('.spare-part-model-input').attr('name', 'equipment[' + equipment_id + '][spare_part][' + rand_id + '][model]')
            $(this).find('.spare-part-value-input').attr('name', 'equipment[' + equipment_id + '][spare_part][' + rand_id + '][value]')
        });

    };


    $('.equipment-name-input').on('input', onchange_equipment_function);


    let add_function = function() {

        let equipment_add_div = this.closest('.equipment-add-div');
        let equipments_add_parent = this.closest('.equipments-add-parent-div');

        let new_equipment_add_div = equipment_add_div.cloneNode(true);

        let inputNode = new_equipment_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        equipments_add_parent.appendChild(new_equipment_add_div);

        let scrollDiv = equipment_add_div.offsetTop;
        window.scrollTo({ top: scrollDiv, behavior: 'smooth'});

        $('.equipment-name-input').on('input', onchange_equipment_function);

        Array.from(add_buttons).forEach(function(element) {
            element.addEventListener('click', add_function);
        });

        let delete_buttons = document.getElementsByClassName("equipment-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_function);
        });

        Array.from(add_work_buttons).forEach(function(element) {
            element.addEventListener('click', add_work_function);
        });

        let delete_work_buttons = document.getElementsByClassName("work-delete-button");

        Array.from(delete_work_buttons).forEach(function(element) {
            element.addEventListener('click', delete_work_function);
        });

        //=================================================================
        Array.from(add_group_work_buttons).forEach(function(element) {
            element.addEventListener('click', add_group_work_function);
        });

        let delete_group_work_buttons = document.getElementsByClassName("group-work-delete-button");

        Array.from(delete_group_work_buttons).forEach(function(element) {
            element.addEventListener('click', delete_group_work_function);
        });
        //==================================================================

        Array.from(add_spare_part_buttons).forEach(function(element) {
            element.addEventListener('click', add_spare_part_function);
        });

        let delete_spare_part_buttons = document.getElementsByClassName("spare-part-delete-button");

        Array.from(delete_spare_part_buttons).forEach(function(element) {
            element.addEventListener('click', delete_spare_part_function);
        });

    };



    let add_work_function = function() {

        let add_work_buttons = document.getElementsByClassName("work-add-button");

        let work_add_div = this.closest('.work-add-div');
        let works_add_parent = this.closest('.works-add-parent-div');

        let new_work_add_div = work_add_div.cloneNode(true);

        let equip_div = this.closest('.equipment-add-div');

        let equipment_id = $(equip_div).find('.equipment-name-input').attr('data-equipment-id');

        $(equip_div).find('.work-add-div').each(function() {
            let rand_id = (Math.random() + 1).toString(20);
            $(this).find('.work-type-input').attr('name', 'equipment[' + equipment_id + '][work][' + rand_id + '][type]')
            $(this).find('.work-comment-textarea').attr('name', 'equipment[' + equipment_id + '][work][' + rand_id + '][comment]')
        });

        let inputNode = new_work_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        works_add_parent.appendChild(new_work_add_div);

        let scrollDivWork = work_add_div.offsetTop;
        window.scrollTo({ top: scrollDivWork, behavior: 'smooth'});

        Array.from(add_work_buttons).forEach(function(element) {
            element.addEventListener('click', add_work_function);
        });

        let delete_buttons = document.getElementsByClassName("work-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_work_function);
        });

    };

    let add_group_work_function = function() {

        let add_group_work_buttons = document.getElementsByClassName("group-work-add-button");

        let group_work_add_div = this.closest('.group-work-add-div');
        let group_works_add_parent = this.closest('.group-works-add-parent-div');

        let new_group_work_add_div = group_work_add_div.cloneNode(true);

        let inputNode = new_group_work_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        group_works_add_parent.appendChild(new_group_work_add_div);

        $('.group-works-add-parent-div .group-work-add-div').each(function() {
            let rand_id = (Math.random() + 1).toString(20);
            $(this).find('.group-work-type-input').attr('name', 'group_works[' + rand_id + '][name]')
            $(this).find('.group-work-comment-textarea').attr('name', 'group_works[' + rand_id + '][comment]')
        });

        let scrollDivWork = group_work_add_div.offsetTop;
        window.scrollTo({ top: scrollDivWork, behavior: 'smooth'});

        Array.from(add_group_work_buttons).forEach(function(element) {
            element.addEventListener('click', add_group_work_function);
        });

        let delete_buttons = document.getElementsByClassName("group-work-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_group_work_function);
        });

    };



    let add_spare_part_function = function() {

        let add_spare_part_buttons = document.getElementsByClassName("spare-part-add-button");

        let spare_part_add_div = this.closest('.spare-part-add-div');
        let spare_parts_add_parent = this.closest('.spare-parts-add-parent-div');

        let new_spare_part_add_div = spare_part_add_div.cloneNode(true);

        let equip_div = this.closest('.equipment-add-div');

        let equipment_id = $(equip_div).find('.equipment-name-input').attr('data-equipment-id');

        $(equip_div).find('.spare-part-add-div').each(function() {
            let rand_id = (Math.random() + 1).toString(20);
            $(this).find('.spare-part-name-input').attr('name', 'equipment[' + equipment_id + '][spare_part][' + rand_id + '][name]')
            $(this).find('.spare-part-model-input').attr('name', 'equipment[' + equipment_id + '][spare_part][' + rand_id + '][model]')
            $(this).find('.spare-part-value-input').attr('name', 'equipment[' + equipment_id + '][spare_part][' + rand_id + '][value]')
        });

        let inputNode = new_spare_part_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        spare_parts_add_parent.appendChild(new_spare_part_add_div);

        let scrollDivSparePart = spare_part_add_div.offsetTop;
        window.scrollTo({ top: scrollDivSparePart, behavior: 'smooth'});

        Array.from(add_spare_part_buttons).forEach(function(element) {
            element.addEventListener('click', add_spare_part_function);
        });

        let delete_spare_part_buttons = document.getElementsByClassName("spare-part-delete-button");

        Array.from(delete_spare_part_buttons).forEach(function(element) {
            element.addEventListener('click', delete_spare_part_function);
        });

    };

    Array.from(add_buttons).forEach(function(element) {
        element.addEventListener('click', add_function);
    });

    Array.from(add_work_buttons).forEach(function(element) {
        element.addEventListener('click', add_work_function);
    });

    Array.from(add_group_work_buttons).forEach(function(element) {
        element.addEventListener('click', add_group_work_function);
    });

    Array.from(add_spare_part_buttons).forEach(function(element) {
        element.addEventListener('click', add_spare_part_function);
    });

}, false);





