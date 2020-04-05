// Cacher le champ quantité quand l'événement n'est pas sélectionné
$(document).ready(function() {

	var container = '.choix.quantite';

	$(container).hide();
	$("input.evenement:checked", $(this)).each(function() {
		$(this).parent('div').next(container).show('slow');
	});

	$('input.evenement').click(function() {
		$("input.evenement:not(:checked)").each(function() {
			$(this).parent('div').next(container).hide('slow');
		});
		$("input.evenement:checked").each(function() {
			$(this).parent('div').next(container).show('slow');
		});
	});
});
