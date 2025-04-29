document.addEventListener('DOMContentLoaded', function() {

    let delete_buttons = document.getElementsByClassName("equipment-delete-button");

    Array.from(delete_buttons).forEach(function(element) {
        element.addEventListener('click', delete_function);
    });

}, false);

let delete_function = function() {

    let equipment_add_count = document.getElementsByClassName("equipment-add-div").length;

    if(equipment_add_count > 1)
    {

        this.closest('.equipment-add-div').remove();

    } else {
        alert('Невозможно удалить. Как потом добавлять будете?');
    }

};

let delete_work_function = function() {

    let work_add_count = this.closest('.works-add-parent-div').getElementsByClassName("work-add-div").length;

    if(work_add_count > 1)
    {

        this.closest('.work-add-div').remove();

    } else {
        alert('Невозможно удалить. Как потом добавлять будете?');
    }

};

let delete_group_work_function = function() {

    let group_work_add_count = this.closest('.group-works-add-parent-div').getElementsByClassName("group-work-add-div").length;

    if(group_work_add_count > 1)
    {

        this.closest('.group-work-add-div').remove();

    } else {
        alert('Невозможно удалить. Как потом добавлять будете?');
    }

};


let delete_spare_part_function = function() {

    let spare_part_add_count = this.closest('.spare-parts-add-parent-div').getElementsByClassName("spare-part-add-div").length;

    if(spare_part_add_count > 1)
    {

        this.closest('.spare-part-add-div').remove();

    } else {
        alert('Невозможно удалить. Как потом добавлять будете?');
    }

};



