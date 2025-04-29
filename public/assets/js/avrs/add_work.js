// document.addEventListener('DOMContentLoaded', function() {
//
//     let add_work_buttons = document.getElementsByClassName("work-add-button");
//
//     let add_work_function = function() {
//
//         let work_add_div = this.closest('.work-add-div');
//         let works_add_parent = this.closest('.works-add-parent-div');
//
//         let new_work_add_div = work_add_div.cloneNode(true);
//         let inputNode = new_work_add_div.getElementsByTagName('INPUT')[0];
//
//         inputNode.value = '';
//
//         works_add_parent.appendChild(new_work_add_div);
//
//         Array.from(add_work_buttons).forEach(function(element) {
//             element.addEventListener('click', add_work_function);
//         });
//
//         let delete_buttons = document.getElementsByClassName("work-delete-button");
//
//         Array.from(delete_buttons).forEach(function(element) {
//             element.addEventListener('click', delete_work_function);
//         });
//
//     };
//
//     Array.from(add_work_buttons).forEach(function(element) {
//         element.addEventListener('click', add_work_function);
//     });
//
//
// }, false);


