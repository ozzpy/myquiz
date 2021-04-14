$(function() {
    /*$(".alert").delay(4000).slideUp(200, function() {
        $(this).alert('close');
    });*/

    setTimeout(function () {
        $('.alert').alert('close');
    }, 6000);

    $('.timeago').timeago();

    /** if you want to display cool tooltips do this */
    $('[data-toggle="tooltip"]').tooltip({placement: 'bottom'});
});
