/**
 * Fichier javascript de foundation améliorer par mes soins
 */

// lancer foundation
$(document).foundation();

function spip_foundation() {
	// Support Ajax pour les reveal-modal
	// Cela passe par une class .reveal-ajax
	$(".reveal-ajax").on('click', function (e) {

		// bloquer l'événement click
		e.preventDefault();
		e.stopPropagation();

		// Récupérer le lien ciblé par la requête ajax
		var href = $(this).data("reveal-ajax");
		// On a besoin de savoir sur quel modal il faut agir
		var modal = $(this).data("toggle");

		// Dans le cas ou la modal est vide sur toggle,
		// on vérifie que ce n'est pas data-open qui est utilisé
		if (!modal) {
			modal = $(this).data("open");
		}

		// On charge la page dans la reveal

		$("#"+modal).load(href, function () {
			$(this).foundation("open");
		});

	});


	// Restaurer le deeplinking des tab Foundation
	// En attendant que la fonction soit de retour officiellement
	var link_tab = window.location.hash.substr(1);
	if (link_tab) {
		$('[data-tabs]').eq(0).foundation('selectTab', $('#'+link_tab));
	}
}

spip_foundation();
onAjaxLoad(function () {
	spip_foundation();
});
