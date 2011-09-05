/* Scroll infini avec jQuery
 * d'après Arnaud Bosquet  
 */

/* TODO : passer le js en squelette et utiliser des variables cfg
 *  dans cfg
 * 		- classe à cibler pour déclencher l'infinite scoll
 * 		- nombre d'articles à afficher (pagination)
 * 		- classe CSS pour le nombre d'item
 * 
 */
function infiniscroll_init(){jQuery(function(){    
        
    var load = false; // aucun chargement de commentaire n'est en cours
 	$('.loadmore').hide();
	/* la fonction offset permet de récupérer la valeur X et Y d'un élément
	dans une page. Ici on récupère la position du dernier élément qui 
	a pour classe : ".item" dans les listes d'articles*/
	var element = '#contenu .articles';
	var offset = $(element +' .liste-items .item:last').offset(); 
	
	$(window).scroll(function(){ // On surveille l'évènement scroll
		/* Si l'élément offset est en bas de scroll, si aucun chargement 
		n'est en cours, si le nombre d'articles affichés est supérieur 
		à 5 et si tout les articles ne sont pas affichés, alors on 
		lance la fonction. */
		
		if((offset.top-$(window).height() <= $(window).scrollTop()) 
		&& load==false && ($(element +' .liste-items .item').size()>=5) && 
		($(element +' .liste-items .item').size()!=$(element +' .nb_com').text())){
 
			// la valeur passe à vrai, on va charger
			load = true;
 
			//On récupère le numéro (dans la boucle) du dernier item affiché
			var last_id = $(element +' .liste-items .item:last').attr('id').substr(4);
			// Il nous faut récupérer l'id_rubrique des articles
			id_rubrique   = $(element).attr('id').substr(4);
			
			//On affiche un loader
			$('.loadmore').delay(5000).fadeIn('slow');
 
			//On lance la fonction ajax
			$.ajax({
				url: './?page=infiniscroll_articles',
				type: 'get',
				data: 'last='+last_id+'&id_rubrique='+id_rubrique,
 
				//Succès de la requête
				success: function(data) {
 
					//On masque le loader
					//$('.loadmore').delay(800).fadeOut("slow");
					/* On affiche le résultat après
					le dernier commentaire */
					$(element +' .liste-items .item:last').after(data);
					/* On actualise la valeur offset
					du dernier commentaire */
					offset = $(element +' .liste-items .item:last').offset();
					//On remet la valeur à faux car c'est fini
					load = false;
				}
			});
		}
 
 
	});
});}
jQuery(function(){infiniscroll_init.apply(document); onAjaxLoad(infiniscroll_init);});
