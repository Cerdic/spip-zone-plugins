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
			// On récupère la liste des termes séparés par une VIRGULE (cas le plus courant)
			var terms = split_multiple(this.value, ',');
			// On supprime le terme qui était en train d'être tapé
			terms.pop();
			// On ajoute à la fin ce qui a été sélectionné
			terms.push(ui.item.value);
			// On ajoute une entrée vide pour avoir le séparateur lors de la jointure
			terms.push("");
			// On joint tout les termes
			this.value = terms.join(", ");
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
		if (typeof(selecteurgenerique_test_espace_prive) != 'undefined'){
			api = '../' + api;
		}
	
		inputs.each(function(){
			// L'input en question
			var me = $(this);
			// Quel sélecteur appeler
			var quoi = me.data('selecteur');
			var select_callback = me.data('select-callback');
			if (typeof(select_callback) != 'function' && typeof(select_callback) != 'string'){ select_callback = selecteurgenerique_select_callback_dist; }
			
			me
				// appliquer l'autocomplete dessus
				.autocomplete({
					source: function(request, response) {
						if (me.attr('multiple')){ var term = extractLast(request.term, ','); }
						else { var term = request.term; }
						//console.log('"'+term+'"');
						$.getJSON(api+quoi, {q:term}, response);
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
