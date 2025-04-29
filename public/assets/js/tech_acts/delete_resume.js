document.addEventListener('DOMContentLoaded', function() {

    let delete_buttons = document.getElementsByClassName("tech-act-resume-delete-button");

    Array.from(delete_buttons).forEach(function(element) {
        element.addEventListener('click', delete_resume_function);
    });

}, false);

let delete_resume_function = function() {

    let resume_add_count = document.getElementsByClassName("tech-act-resume-add-div").length;

    if(resume_add_count > 1)
    {

        this.closest('.tech-act-resume-add-div').remove();

    } else {
        alert('Невозможно удалить. Как потом добавлять будете?');
    }

};



