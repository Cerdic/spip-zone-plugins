// Cacher le champ quantité quand l'événement n'est pas sélectionné
$(document).ready(function() {

  var container = '.choix.quantite';

  $(container).hide();

  $("input.evenement:checked", $(this)).each(function() {
    $(this).parent('div').next(container).show(container);
  });

  $('input.evenement').click(function() {
    $(this).parent('div').next(container).toggle(container);
  });

});
