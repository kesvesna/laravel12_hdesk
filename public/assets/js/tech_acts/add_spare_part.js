document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("tech-act-spare-part-add-button");

    let add_spare_part_function = function() {

        let spare_part_add_div = this.closest('.tech-act-spare-part-add-div');
        let spare_parts_add_parent = this.closest('.tech-act-spare-parts-add-parent-div');

        let new_spare_part_add_div = spare_part_add_div.cloneNode(true);
        let inputNode = new_spare_part_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        spare_parts_add_parent.appendChild(new_spare_part_add_div);

        let scrollDivspare_part = spare_part_add_div.offsetTop;
        window.scrollTo({ top: scrollDivspare_part, behavior: 'smooth'});

        Array.from(add_buttons).forEach(function(element) {
            element.addEventListener('click', add_spare_part_function);
        });

        let delete_buttons = document.getElementsByClassName("tech-act-spare-part-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_spare_part_function);
        });

    };

    Array.from(add_buttons).forEach(function(element) {
        element.addEventListener('click', add_spare_part_function);
    });


}, false);


