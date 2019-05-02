
/*
 * Implémente le design pattern "waterfall" pour faire des actions sur une liste
 * dans un ordre précis, dans un contexte d'actions asynchrones
 * 
 * Cette version est propre à jquery, la liste n'étant pas un tableau mais un jquery
 * donc on récupère l'élement suivant avec .eq()
 */
function WaterfallOverJQuery(list, iterator, callback) {
	var nextItemIndex = 0;  //keep track of the index of the next item to be processed

	function report() {
		nextItemIndex++;

		// if nextItemIndex equals the number of items in list, then we're done
		if(nextItemIndex === list.length)
			callback();
		else
			// otherwise, call the iterator on the next item
			iterator(list.eq(nextItemIndex), report);
	}

	// instead of starting all the iterations, we only start the 1st one
	iterator(list.eq(0), report);
}

;(function($){
	var campagnes_ids = [];
	
	/*
	 * Fonction qui détecte les pubs asyncs et qui les charge si besoin
	 */
	function campagnes_async() {
		var encarts = $('[data-id_encart]');
		
		// On nettoie les choses déjà affiché
		encarts.html('');
		campagnes_ids = [];
		
		// On parcourt tous les encarts en les rechargeant en asynchrone
		WaterfallOverJQuery(encarts, function(encart, report) {
			var id_encart = encart.data('id_encart');
			var id_html = encart.attr('id');
			var id_campagne = encart.data('id_campagne');
			var contexte = encart.data('contexte');
			var largeur_max = encart.data('largeur_max');
			var hauteur_max = encart.data('hauteur_max');
			var media = encart.data('media');
			//console.log(id_encart);
			//console.log(id_html);
			
			// On ne charge la campagne que si pas de media query ou si elle valide
			if (!media || ((mq = window.matchMedia(media)) && mq.matches)) {
				// On recharge le bloc ajax en demandant le chargement d'une pub
				encart.ajaxReload({
					history: false,
					args: {
						charger: 'oui',
						campagnes_ids: campagnes_ids,
						id: id_html,
					},
					callback: function() {
						// On retourne chercher le <div> vu qu'il vient d'être rechargé dans le DOM
						encart = $('#'+id_html);
						// Du coup on récupère quelle pub a été chargée dedans
						if (id_campagne = encart.data('id_campagne')) {
							//console.log(id_campagne);
							// Et on l'ajoute au tableau global
							campagnes_ids.push(id_campagne);
						}
						//console.log(campagnes_ids);
						
						// On lance la suite
						report();
					}
				});
			}
			// Si on ne charge pas la pub, on continue quand même de parcourir la liste !
			else {
				report();
			}
		}, function() {
			console.log('Campagnes : finito');
		});
	}
	
	// Au chargement du DOM
	$(function(){
		campagnes_async();
	});
	
	// Après des changements AJAX => non car boucle infinie
	//onAjaxLoad(campagnes_async);
	
	// Après un changement de taille
	// Bug safari : l'event resize est lancé au scroll !
	// On stocke la taille en amont pour éviter de le lancer sans arrêt
	var resizeTimer;
	var windowWidth = $(window).width();
	$(window).resize(function() {
		if ($(window).width() != windowWidth) {
			windowWidth = $(window).width();
			clearTimeout(resizeTimer);
			resizeTimer = setTimeout(campagnes_async, 100);
			//console.log('resize');
		}
	});
	// Après un changement d'orientation
	$(window).on('orientationchange', function(){
		clearTimeout(resizeTimer);
		resizeTimer = setTimeout(campagnes_async, 100);
		//console.log('orientation change');
	});
})(jQuery);
