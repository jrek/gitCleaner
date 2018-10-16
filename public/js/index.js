$(document).ready(function(){
    $('select').formSelect();
    var branchesCheckBox = $('input[type="checkbox"][name="braches[]"]');

    if (branchesCheckBox.length) {
        branchesCheckBox.on('change', function(e){
            var el = $(e.target);

            if (el.data('checked') != true) {
                el.data('checked', true);
                el.parent().parent().parent().css('background-color', '#FF6D00');
            } else {
                el.data('checked', false);
                el.parent().parent().parent().css('background-color', '#FFF');
            }
        });
    }
});