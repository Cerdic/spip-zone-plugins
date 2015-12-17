/**
 * Fichier javascript de foundation améliorer par mes soins
 */

// lancer foundation
$(document).foundation();

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
	$("#"+modal).load(href);
});
