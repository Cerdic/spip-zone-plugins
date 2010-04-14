// Gerer l'ouverture et la fermeture des blocs du forum
$(document).ready(function() {

	// Cacher tous les elements de classs .forum-texte
	$(".forum-texte").hide();

	$('.forum-titre').click(function() {
		// Faut-il refermer toutes les autres boites ?
		// $(".forum-texte").hide();
		$(this).next('.forum-texte').slideToggle('medium');
	});

	// Deplier les elements appeles par un lien interne a la page
	// $("a.ancre").click(function(){
	//	$($this).attr('href').hash.children('.forum-texte').slideToggle('medium');
	// });

	// Tout deplier
	// $("p.tout_deplier").click(function(){
	// 		$('.forum-texte').slideToggle('medium');
	// }

});

// Ouvrir automatiquement les blocs appeles par un lien exterieur
$(function() {
	$(document.location.hash).children('div.forum-texte').slideToggle('medium');
});

