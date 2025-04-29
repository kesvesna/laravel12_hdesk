document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("good-add-button");

    let add_good_function = function() {

        let good_add_div = this.closest('.good-add-div');
        let goods_add_parent = this.closest('.goods-add-parent-div');

        let new_good_add_div = good_add_div.cloneNode(true);
        let inputNode = new_good_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        goods_add_parent.appendChild(new_good_add_div);

        let scrollDivgood = good_add_div.offsetTop;
        window.scrollTo({ top: scrollDivgood, behavior: 'smooth'});

        Array.from(add_buttons).forEach(function(element) {
            element.addEventListener('click', add_good_function);
        });

        let delete_buttons = document.getElementsByClassName("good-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_good_function);
        });

    };

    Array.from(add_buttons).forEach(function(element) {
        element.addEventListener('click', add_good_function);
    });


}, false);


