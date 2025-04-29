document.addEventListener('DOMContentLoaded', function() {

    $('input[type=radio][name=avr_type]').change(function() {
        if (this.value == 'group_works_avr') {
            $('.equipments-add-parent-div').addClass('d-none');
            $('.group-works-visibility-div').removeClass('d-none');
        }
        else if (this.value == 'simple_avr') {
            $('.equipments-add-parent-div').removeClass('d-none');
            $('.group-works-visibility-div').addClass('d-none');
        }
    });

}, false);





