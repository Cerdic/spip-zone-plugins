// Déclaration pour ne pas avoir de warning JS
var nmqp_previous_url;

// repris de https://gist.github.com/m1r0/018ed46dd0ff96176a9d
jQuery.fn.bindFirst = function(/*String*/ eventType, /*[Object], Function*/ eventData, /*Function*/ handler) {
	var indexOfDot = eventType.indexOf(".");
	var eventNameSpace = indexOfDot > 0 ? eventType.substring(indexOfDot) : "";

	eventType = indexOfDot > 0 ? eventType.substring(0, indexOfDot) : eventType;
	handler = handler == undefined ? eventData : handler;
	eventData = typeof eventData == "function" ? {} : eventData;

	return this.each(function() {
		var $this = jQuery(this);
		var currentAttrListener = this["on" + eventType];

		if (currentAttrListener) {
			$this.bind(eventType, function(e) {
				return currentAttrListener(e.originalEvent); 
			});

			this["on" + eventType] = null;
		}

		$this.bind(eventType + eventNameSpace, eventData, handler);

		var allEvents = jQuery._data($this[0], "events");
		var typeEvents = allEvents[eventType];
		var newEvent = typeEvents.pop();
		typeEvents.unshift(newEvent);
	});
};

function nemequittepas_confirm() {
	if (jQuery('.formulaire_editer form').hasClass('dirty')) {
		return window.confirm('Voulez-vous vraiment quitter la page et perdre votre saisie ?');
	}
	return true;
}

function init_nemequittepas() {
	var current_url = window.location.pathname+window.location.search;

	// On ne fait l'initialisation que si on arrive sur une nouvelle url (résout le problème pour la prévisualisation grand écran et édition des documents)
	if ((current_url!=nmqp_previous_url) && (jQuery('.formulaire_editer form').length)) {
		jQuery('.formulaire_editer form').areYouSure( {'message':'Voulez-vous vraiment quitter la page et perdre votre saisie ?'} );

		// on cherche les liens de retour ajaxés (seulement si on est arrivé par Ajax)
		if (nmqp_previous_url) {
			jQuery('.retour a').not('.allreadybind').bindFirst('click', function() {
				return nemequittepas_confirm();
			});
			// Et on pose un marqueur pour ne pas ajouter l'évenement plusieurs fois
			jQuery('.retour a').addClass('allreadybind');

			// Il faut aussi gérer le cas du bouton back dans l'historique du navigateur
			if (!window.spip_onpopstate) {
				window.spip_onpopstate = window.onpopstate;
			}
			window.onpopstate = function(popState){
				if (nemequittepas_confirm()==false) {
					// On n'a pas confirmé. Mais le bouton back ne se cancel pas !
					// ==> on remet dans l'historique la page d'édition
					window.history.pushState({}, 'foo', nmqp_previous_url);
				} else {
					// On a confirmé ! Traitement "normal" de l'AJAX/SPIP…
					window.spip_onpopstate(popState);
				}
			};
		}
	}

	nmqp_previous_url = current_url;
}

jQuery(document).ready(function () {
	init_nemequittepas();
	onAjaxLoad(init_nemequittepas);
});
