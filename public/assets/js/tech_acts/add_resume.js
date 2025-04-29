document.addEventListener('DOMContentLoaded', function() {

    let add_buttons = document.getElementsByClassName("tech-act-resume-add-button");

    let add_resume_function = function() {

        let resume_add_div = this.closest('.tech-act-resume-add-div');
        let resumes_add_parent = this.closest('.tech-act-resumes-add-parent-div');

        let new_resume_add_div = resume_add_div.cloneNode(true);
        let inputNode = new_resume_add_div.getElementsByTagName('INPUT')[0];

        inputNode.value = '';

        resumes_add_parent.appendChild(new_resume_add_div);

        let scrollDivresume = resume_add_div.offsetTop;
        window.scrollTo({ top: scrollDivresume, behavior: 'smooth'});

        Array.from(add_buttons).forEach(function(element) {
            element.addEventListener('click', add_resume_function);
        });

        let delete_buttons = document.getElementsByClassName("tech-act-resume-delete-button");

        Array.from(delete_buttons).forEach(function(element) {
            element.addEventListener('click', delete_resume_function);
        });

    };

    Array.from(add_buttons).forEach(function(element) {
        element.addEventListener('click', add_resume_function);
    });


}, false);


