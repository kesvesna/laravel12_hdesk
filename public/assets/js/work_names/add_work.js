document.addEventListener('DOMContentLoaded', function() {

    let add_work_buttons = document.getElementsByClassName("work-add-button");

    let onchange_work_function = function() {

        let work_id = $(this).val();

        $(this).attr('data-work-id', work_id);
        $(this).attr('name', 'works[' + work_id + ']');

        let work_period_day_input = $(this).closest('.work-add-div').find('.work-period-day-input');

        work_period_day_input.attr('name', 'works[' + work_id + '][period_days]');
        work_period_day_input.attr('data-work-period-day', work_id);

        let work_div = $(this).closest('.work-add-div');

        // set all works input name
        $(work_div).find('.work-add-div').each(function () {
            let rand_id = (Math.random() + 1).toString(20);
            $(this).find('.work-type-select').attr('name', 'works[' + rand_id + ']')
            $(this).find('.work-period-day-input').attr('name', 'works[' + rand_id + '][period_days]')
        });
    }

    $('.work-type-select').on('input', onchange_work_function);

    let add_work_function = function() {

        let work_add_div = this.closest('.work-add-div');
        let works_add_parent = this.closest('.works-add-parent-div');

        let new_work_add_div = work_add_div.cloneNode(true);
        let inputNode = new_work_add_div.getElementsByTagName('SELECT')[0];

        inputNode.value = '';

        works_add_parent.appendChild(new_work_add_div);

        $('.work-type-select').on('input', onchange_work_function);

        Array.from(add_work_buttons).forEach(function(element) {
            element.addEventListener('click', add_work_function);
        });

        let delete_buttons = document.getElementsByClassName("work-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_work_function);
        });

    };

    Array.from(add_work_buttons).forEach(function(element) {
        element.addEventListener('click', add_work_function);
    });

    let delete_work_function = function() {

        let work_add_count = this.closest('.works-add-parent-div').getElementsByClassName("work-add-div").length;

        if(work_add_count > 1)
        {

            this.closest('.work-add-div').remove();

        } else {
            alert('Невозможно удалить. Как потом добавлять будете?');
        }

    };


}, false);


