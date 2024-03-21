

$(document).on('click', '#add-credit-card-modal input[name=agree_policies_add_card]', function (){
    $('#error-agree-add-card-1').text('');
});

$(document).on('click', '#assign-credit-card-modal input[name=agree_policies_add_card]', function (){
    $('#error-agree-add-card-2').text('');
});

$(document).on('click', '#assign-credit-card-modal input[name=agree_policies_save_selection]', function (){
    $('#error-agree-save-selection').text('');
});

$(document).on('click', '#set-up-canteen-account input[name=agree_policies_set_account]', function (){
    $('#error-agree-set-account').text('');
});

$(document).on('click', '.assign-label', function (){

    $(this).parent('div').find('.assign-label').each(function (){
        $(this).removeClass('active');
        $(this).addClass('opacity-30');

        var contentDiv = $(this).attr('data-contentDiv');
        $('div.' + contentDiv).addClass('d-none');
    });

    $(this).removeClass('opacity-30');
    $(this).addClass('active');

    var contentDiv = $(this).attr('data-contentDiv');
    $('div.' + contentDiv).removeClass('d-none');
});


$(document).on('show.bs.modal', '#delete-credit-card-modal', function () {
    $(this).find('input').each(function (){
        if ($(this).attr('name') != '_token') {
            $(this).val('');
        }
    });
});

$(document).on('show.bs.modal', '#unassign-credit-card-modal', function () {
    $(this).find('input').each(function (){
        if ($(this).attr('name') != '_token') {
            $(this).val('');
        }
    });
});

// $(document).on('show.bs.modal', '#change-password-canteen-account', function () {
//     $(this).find('input').each(function (){
//         $(this).val('');
//     });
// });

$(document).on('click', '.delete-credit-card', function () {

    $("#delete-credit-card-modal").modal("show");

    var credit_card_id = $(this).attr('data-cardID');

    $("#delete-credit-card-modal input[name=card_token_id]").val(credit_card_id);


});


$(document).on('click', '.unassign-credit-card', function () {

    $("#unassign-credit-card-modal").modal("show");

    var canteen_user_id = $(this).attr('data-canteenUser');

    $("#unassign-credit-card-modal input[name=canteen_user_id]").val(canteen_user_id);

    // console.log('canteen user: ', canteen_user_id);


});





