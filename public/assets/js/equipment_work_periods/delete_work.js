
let delete_work_function = function() {

    let work_add_count = this.closest('.works-add-parent-div').getElementsByClassName("work-add-div").length;

    if(work_add_count > 1)
    {

        this.closest('.work-add-div').remove();

    } else {
        alert('Невозможно удалить тех. мероприятия все. Как потом добавлять будете?');
    }

};





