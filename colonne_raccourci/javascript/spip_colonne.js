/* 
 *	Code pour SPIP
 *  Gestion des .spip_colonne
 *	GPL3 2019
 *	Anne-lise Martenot 
 *	http://elastick.net
*/

$(document).ready(function(){
	// compter le nombre de colonnes
	var nb_colonne;
	var nb_colonne = $(".spip_colonne").length;
	// au moins 2 colonnes présentes
	if(nb_colonne > 1){
		
		//la premiere colonne trouvée prend la class 'spip_colonne_first'
		$(".spip_colonne:first").addClass('spip_colonne_first');
		
		//on repére les nœuds frères suivants chaque .spip_colonne qui seraient des P
		//la premiere .spip_colonne trouvée prend la class spip_colonne_first
		
		 $(".spip_colonne").nextAll("p").each(function() {
					$(this).nextAll('.spip_colonne:first').addClass('spip_colonne_first');
		});
		
		$(".spip_colonne_first").each(function() {
						$(this).
						first().
						nextUntil('p').
						andSelf().
						wrapAll('<div class="row" />');
		 });
		
		//on supprime les br immiscés entre 2 spip_colonne (nextAll = nœuds frères)
		$(".row .spip_colonne").nextAll("br").each(function() {
				$(this).remove();
		});
		
		//on peut maintenant supprimer la class de chaque première colonne
		$('.spip_colonne').removeClass('spip_colonne_first');
		
		//on compte le nombre de colonne par row
		//pour affecter les class inclues par HTML5
		//on pourrait faire 12/count mais bon hein …
		$(".row").each(function() {
					var count = $(this).children(".spip_colonne").length;
					if(count == 2 ){
						$(this).children('.spip_colonne').addClass('col-6 col-12-small');
					} else if (count == 3 ){
						$(this).children('.spip_colonne').addClass('col-4 col-12-medium');
					} else if (count == 4 ){
						$(this).children('.spip_colonne').addClass('col-3 col-12-medium');
					} else if (count >= 5 ){
						$(this).children('.spip_colonne').addClass('col-2 col-12-medium');
					}
		});
				
	}
	
});
