/**
 * Fichers de fonctions spécifiques complémentaires à l'autocompleteur de jquery.ui
 */

var selecteur_format = function(data){
	var parsed = [];
	var rows = data.split("\n");
	for (var i=0; i < rows.length; i++) {
		var row = $.trim(rows[i]);
		if (row) {
			row = row.split("|");
			parsed[parsed.length] = {
				data: row,
				label: row[0],
				entry: row[1],
				value: row[2],
				result: row[2]
			};
		}
	}
	return parsed;
}

/*
 * Découper une chaine en tableau suivant un séparateur
 *
 * @param string val Chaîne de caractère à découper
 * @param string sep Chaîne considérée comme le séparateur de la liste (par défaut ";" pour garder la compatibilité)
 * return array Retourne une liste de chaînes, sans espaces autour
 */
function split_multiple(val, sep){
	if (!sep){ var sep = ';' }
	sep = '\\s*' + sep + '\\s*';
	//console.log(sep);
	return val.split(new RegExp(sep));
}

/*
 * Renvoie le dernier terme d'une liste caractérisée par un séparateur
 *
 * @param string list Chaîne de caractères constituée d'une liste de termes séparés par un séparateur quelconque
 * @param string sep Chaîne considérée comme le séparateur de la liste
 * @return string Retourne le dernier terme de la liste
 */
function extractLast(list, sep) {
	return split_multiple(list, sep).pop();
}

/*
 * Chercher et appliquer l'autocomplétion sur les champs déclarés comme tel
 */
(function($){
	// Comportement par défaut lors de la sélection dans l'autocomplétion
	var selecteurgenerique_select_callback_dist = function(event, ui){
		// Si le champ est déclaré comme "multiple" on ne remplace que la fin
		if ($(this).attr('multiple')){
			// On définit le séparateur
			var separateur = $(this).data('select-sep');
			if (typeof(separateur) != 'string') {
				separateur = ',';
			}
			// On récupère la liste des termes séparés par une VIRGULE (cas le plus courant)
			var terms = split_multiple(this.value, separateur);
			// On supprime le terme qui était en train d'être tapé
			terms.pop();
			// On ajoute à la fin ce qui a été sélectionné, éventuellement entouré de guillemets
			var guillemets = false;
			if (ui.item.value.indexOf(separateur) != -1){ guillemets = true; }
			terms.push((guillemets ? '"' : '') + ui.item.value + (guillemets ? '"' : ''));
			// On ajoute une entrée vide pour avoir le séparateur lors de la jointure
			terms.push("");
			// On joint tout les termes
			this.value = terms.join(separateur);
		}
		// Sinon on remplace tout
		else{
			this.value = ui.item.value;
		}
		
		return false;
	};
	
	var selecteurgenerique_chercher_selecteurs = function(){
		// chercher tous les inputs déclarés explicitement comme sélecteurs
		var inputs = $('input[data-selecteur][autocomplete!=off]');
		var api = 'selecteur.api/';
		if (typeof(selecteurgenerique_test_espace_prive) != 'undefined' && selecteurgenerique_test_espace_prive){
			api = '../' + api;
		}
	
		inputs.each(function(){
			// L'input en question
			var me = $(this);
			// Quel sélecteur appeler
			var quoi = me.data('selecteur');
			var select_callback = me.data('select-callback');
			if (typeof(select_callback) != 'function' && typeof(select_callback) != 'string'){ select_callback = selecteurgenerique_select_callback_dist; }
			// On définit le séparateur
			var separateur = me.data('select-sep');
			if (typeof(separateur) != 'string') {
				separateur = ',';
			}
			// On regarde si on demande un sélecteur PHP ou le classique squelette
			var php = me.data('select-php');
			// On cherche s'il y a des paramètres supplémentaires sous format objet JSON {cle:'valeur'}
			var params = me.data('select-params');
			if (typeof(params) == 'string') {
				try {
					params = JSON.parse(params);
				}
				catch (e) {
					console.error('Erreur dans l’analyse des paramètres supplémentaires', e);
				}
			}
			if (typeof(params) != 'object') {
				params = {};
			}
			
			me
				// appliquer l'autocomplete dessus
				.autocomplete({
					source: function(request, response) {
						// On génère le terme à chercher
						if (me.attr('multiple')){ var term = extractLast(request.term, separateur); }
						else { var term = request.term; }
						
						// On remplit le tableau à poster
						params.q = term;
						params.php = php;
						
						// On demande les suggestions
						$.getJSON(api+quoi, params, response);
					},
					delay: 300,
					html: true,
					select: eval(select_callback),
					focus: function(event, ui){
						// prevent value inserted on focus
						return false;
					}
				});
		});
	};
	
	$(function(){
		selecteurgenerique_chercher_selecteurs();
		onAjaxLoad(selecteurgenerique_chercher_selecteurs);
	});
})(jQuery);
