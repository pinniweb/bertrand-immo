jQuery(document).ready(function($) {
    $('#btn-call-modal').on('click', function () {
        console.log('open modal');
        $('#modal-call').modal('show');
    })
    $('.callback_demande').hide();
    $('#radio-demande input').prop('checked', false);
    $('#radio-demande input').on('change', function () {
    $('.callback_demande').hide();
    switch ($(this).val()) {
    case 'Un syndic de copropriété':
        $('#callback_demande_syndic').show();
    break;
    case 'La gestion immobilière':
        $('#callback_demande_gestion').show();
    break;
    case 'La vente ou la location d\'un bien':
        $('#callback_demande_vente_loc').show();
    break;
    case 'Une location saisonnière':
        $('#callback_demande_saison').show();
    break;
    default:
}
})
});
