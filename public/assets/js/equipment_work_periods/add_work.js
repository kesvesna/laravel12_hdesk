document.addEventListener('DOMContentLoaded', function() {

    let add_work_buttons = document.getElementsByClassName("work-add-button");
    let delete_buttons = document.getElementsByClassName("work-delete-button");

    let add_work_function = function() {

        let add_work_buttons = document.getElementsByClassName("work-add-button");
        let work_add_div = this.closest('.work-add-div');
        let works_add_parent = this.closest('.works-add-parent-div');
        let new_work_add_div = work_add_div.cloneNode(true);

        $('.work-add-div').each(function() {
            let rand_id = (Math.random() + 1).toString(20);
            $(this).find('.work-type-input').attr('name', 'works[' + rand_id + '][work_name]')
            $(this).find('.work-days').attr('name', 'works[' + rand_id + '][value]')
            $(this).find('.work-comment-textarea').attr('name', 'works[' + rand_id + '][comment]')
        });

        let inputNode = new_work_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        works_add_parent.appendChild(new_work_add_div);

        let scrollDivWork = work_add_div.offsetTop;
        window.scrollTo({ top: scrollDivWork, behavior: 'smooth'});

        Array.from(add_work_buttons).forEach(function(element) {
            element.addEventListener('click', add_work_function);
        });

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_work_function);
        });

    };

    Array.from(add_work_buttons).forEach(function(element) {
        element.addEventListener('click', add_work_function);
    });

    Array.from(delete_buttons).forEach(function(element) {
        element.addEventListener('click', delete_work_function);
    });

}, false);





