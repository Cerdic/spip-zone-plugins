jQuery(document).ready(function($) {

    $('.liste-objets.commits .commit div.titre').click(function(event){
        event.preventDefault();
        var target = $(this);
        var fiche = target.closest('tr').next('.fiche_commit');
        if (fiche.hasClass('hidden')) {
            fiche.addClass('visible').removeClass('hidden');
            target.addClass('ouvert').removeClass('ferme');
        } else if (fiche.hasClass('visible')) {
            fiche.addClass('hidden').removeClass('visible');
            target.addClass('ferme').removeClass('ouvert');
        };
    });

});
